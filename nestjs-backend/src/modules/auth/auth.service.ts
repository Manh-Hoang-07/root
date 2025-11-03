import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { JwtService } from '@nestjs/jwt';
import * as bcrypt from 'bcryptjs';
import { User } from '../../shared/entities/user.entity';
import { Profile } from '../../shared/entities/profile.entity';
import { LoginDto } from './dto/login.dto';
import { RegisterDto } from './dto/register.dto';
import { ConfigService } from '@nestjs/config';
import { UserStatus } from '../../shared/enums/user-status.enum';
import { ResponseUtil } from '../../common/utils/response.util';
import { RedisUtil } from '../../core/utils/redis.util';

// Fallback in-memory blacklist when Redis is not configured
const tokenBlacklist = new Set<string>();
const DEFAULT_TOKEN_TTL_SECONDS = 3600; // default 1h

@Injectable()
export class AuthService {
  constructor(
    @InjectRepository(User) private readonly userRepository: Repository<User>,
    @InjectRepository(Profile) private readonly profileRepository: Repository<Profile>,
    private readonly jwtService: JwtService,
    private readonly configService: ConfigService,
    private readonly redis: RedisUtil,
  ) { }

  async login(dto: LoginDto) {
    const user = await this.userRepository.findOne({
      where: { email: dto.email },
      select: {
        id: true,
        email: true,
        username: true,
        password: true,
        status: true,
      },
    });
    if (!user || !user.password) {
      return ResponseUtil.unauthorized('Email hoặc mật khẩu không đúng.');
    }

    const isPasswordValid = await bcrypt.compare(dto.password, user.password);
    if (!isPasswordValid) {
      return ResponseUtil.unauthorized('Email hoặc mật khẩu không đúng.');
    }

    if (user.status !== UserStatus.Active) {
      return ResponseUtil.unauthorized('Tài khoản đã bị khóa hoặc không hoạt động.');
    }

    this.userRepository
      .update({ id: user.id }, { last_login_at: new Date() })
      .catch(() => undefined);

    const payload = { sub: user.id, email: user.email };
    const accessToken = this.jwtService.sign(payload);

    return ResponseUtil.success(
      { token: accessToken },
      'Đăng nhập thành công.',
    );
  }

  async register(dto: RegisterDto) {
    // Check email uniqueness
    const existingByEmail = await this.userRepository.findOne({ where: { email: dto.email } });
    if (existingByEmail) {
      return ResponseUtil.badRequest('Email đã được sử dụng.');
    }

    // Check username uniqueness if provided
    if (dto.username) {
      const existingByUsername = await this.userRepository.findOne({ where: { username: dto.username } });
      if (existingByUsername) {
        return ResponseUtil.badRequest('Tên đăng nhập đã được sử dụng.');
      }
    }

    // Check phone uniqueness if provided
    if (dto.phone) {
      const existingByPhone = await this.userRepository.findOne({ where: { phone: dto.phone } });
      if (existingByPhone) {
        return ResponseUtil.badRequest('Số điện thoại đã được sử dụng.');
      }
    }

    const hashed = await bcrypt.hash(dto.password, 10);
    const user = this.userRepository.create({
      username: dto.username ?? dto.email,
      email: dto.email,
      phone: dto.phone ?? null,
      password: hashed,
      status: UserStatus.Active,
    });
    const saved = await this.userRepository.save(user);

    // Tạo profile cho người dùng mới
    if (saved && saved.id) {
      const profile = this.profileRepository.create({
        userId: saved.id,
        name: saved.username || saved.email,
      });
      await this.profileRepository.save(profile).catch(() => undefined);
    }

    return ResponseUtil.success({ user: this.safeUser(saved) }, 'Đăng ký thành công.');
  }

  async logout(userId: number, token?: string) {
    // Tìm người dùng để đảm bảo họ tồn tại
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user) {
      return ResponseUtil.unauthorized('Người dùng không tồn tại');
    }

    // Nếu có token, thêm vào blacklist để ngăn sử dụng lại
    if (token) {
      const ttlSeconds = this.getAccessTokenTtlSeconds();
      const key = this.buildBlacklistKey(token);
      if (this.redis && this.redis.isEnabled()) {
        await this.redis.set(key, '1', ttlSeconds).catch(() => tokenBlacklist.add(token));
      } else {
        tokenBlacklist.add(token);
        // Note: in-memory fallback has no TTL; acceptable for dev only
      }
    }

    // Xóa refresh token của người dùng (nếu có lưu trong database)
    // Điều này ngăn người dùng sử dụng refresh token cũ để lấy token mới
    await this.userRepository
      .update({ id: userId }, {
        updated_at: new Date(),
        remember_token: null // Xóa refresh token
      })
      .catch(() => undefined);

    return ResponseUtil.success(null, 'Đăng xuất thành công.');
  }

  // Kiểm tra token có trong blacklist không
  isTokenBlacklisted(token: string): boolean {
    // Prefer Redis if available
    // Note: this method is sync in guard usage; pre-check in guard uses sync path.
    // We do a best-effort cached check; for strict check, create async path.
    return tokenBlacklist.has(token);
  }

  // Async variant for services wanting strong guarantee
  async isTokenBlacklistedAsync(token: string): Promise<boolean> {
    if (this.redis && this.redis.isEnabled()) {
      const val = await this.redis.get(this.buildBlacklistKey(token));
      if (val) return true;
    }
    return tokenBlacklist.has(token);
  }

  async refreshToken(userId: number) {
    try {
      const user = await this.userRepository.findOne({ where: { id: userId } });
      if (!user) return ResponseUtil.unauthorized('User not authenticated');

      const newPayload = { sub: user.id, email: user.email };
      const accessToken = this.jwtService.sign(newPayload);

      return ResponseUtil.success({ token: accessToken }, 'Token refreshed successfully.');
    } catch (error) {
      return ResponseUtil.unauthorized('Invalid or expired token');
    }
  }

  async me(userId: number) {
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user) return ResponseUtil.unauthorized('Không thể lấy thông tin user');
    return ResponseUtil.success(this.safeUser(user), 'Lấy thông tin user thành công');
  }

  async validateUser(userId: number) {
    const user = await this.userRepository.findOne({ where: { id: Number(userId) } });
    return user || null;
  }

  // roles not used at the moment

  private safeUser(user: User) {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { password, remember_token, ...rest } = user;
    return rest;
  }

  private buildBlacklistKey(token: string): string {
    const env = this.configService.get<string>('app.environment') || process.env.NODE_ENV || 'development';
    return `auth:blacklist:${env}:${token}`;
  }

  private getAccessTokenTtlSeconds(): number {
    const exp = this.configService.get<string>('jwt.expiresIn') || process.env.JWT_EXPIRES_IN;
    if (!exp) return DEFAULT_TOKEN_TTL_SECONDS;
    // Support like "1h", "15m", "3600s"
    const match = /^([0-9]+)([smhd])?$/.exec(exp.trim());
    if (!match) return DEFAULT_TOKEN_TTL_SECONDS;
    const val = parseInt(match[1], 10);
    const unit = match[2] || 's';
    switch (unit) {
      case 's': return val;
      case 'm': return val * 60;
      case 'h': return val * 3600;
      case 'd': return val * 86400;
      default: return DEFAULT_TOKEN_TTL_SECONDS;
    }
  }
}


