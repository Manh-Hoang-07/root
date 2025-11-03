import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { JwtService } from '@nestjs/jwt';
import * as bcrypt from 'bcryptjs';
import { User } from '../../../shared/entities/user.entity';
import { Profile } from '../../../shared/entities/profile.entity';
import { LoginDto } from '../dto/login.dto';
import { RegisterDto } from '../dto/register.dto';
import { ConfigService } from '@nestjs/config';
import { UserStatus } from '../../../shared/enums/user-status.enum';
import { ResponseUtil } from '../../../common/utils/response.util';
import { RedisUtil } from '../../../core/utils/redis.util';
import { generateTokens, decodeRefresh, issueAndStoreNewTokens, getAccessTtlSec, getRefreshTtlSec } from '../utils/token.util';
import { deleteRefreshKey, refreshKeyExists, storeRefreshToken } from '../utils/refresh-store.util';
import { TokenBlacklistService } from '../../../core/security/token-blacklist.service';
import { safeUser } from '../utils/user.util';

@Injectable()
export class AuthService {
  constructor(
    @InjectRepository(User) private readonly userRepository: Repository<User>,
    @InjectRepository(Profile) private readonly profileRepository: Repository<Profile>,
    private readonly jwtService: JwtService,
    private readonly configService: ConfigService,
    private readonly redis: RedisUtil,
    private readonly tokenBlacklistService: TokenBlacklistService,
  ) { }

  async login(dto: LoginDto) {
    const user = await this.userRepository.findOne({
      where: { email: dto.email },
      select: { id: true, email: true, username: true, password: true, status: true, },
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

    const { accessToken, refreshToken, refreshJti, accessTtlSec } = generateTokens(this.jwtService, this.configService, user.id, user.email);

    await storeRefreshToken(this.redis, this.configService, user.id, refreshJti, getRefreshTtlSec(this.configService)).catch(() => undefined);

    return ResponseUtil.success(
      { token: accessToken, refreshToken: refreshToken, expiresIn: accessTtlSec, },
      'Đăng nhập thành công.',
    );
  }

  async register(dto: RegisterDto) {
    const existingByEmail = await this.userRepository.findOne({ where: { email: dto.email } });
    if (existingByEmail) {
      return ResponseUtil.badRequest('Email đã được sử dụng.');
    }

    if (dto.username) {
      const existingByUsername = await this.userRepository.findOne({ where: { username: dto.username } });
      if (existingByUsername) {
        return ResponseUtil.badRequest('Tên đăng nhập đã được sử dụng.');
      }
    }

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

    if (saved && saved.id) {
      const profile = this.profileRepository.create({
        userId: saved.id,
        name: saved.username || saved.email,
      });
      await this.profileRepository.save(profile).catch(() => undefined);
    }

    return ResponseUtil.success({ user: safeUser(saved) }, 'Đăng ký thành công.');
  }

  async logout(userId: number, token?: string) {
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user) {
      return ResponseUtil.unauthorized('Người dùng không tồn tại');
    }

    if (token) {
      const ttlSeconds = getAccessTtlSec(this.configService);
      await this.tokenBlacklistService.blacklist(token, ttlSeconds);
    }
    return ResponseUtil.success(null, 'Đăng xuất thành công.');
  }

  async refreshTokenByValue(refreshToken: string) {
    try {
      const decoded = decodeRefresh(refreshToken, this.configService);
      if (!decoded) return ResponseUtil.unauthorized('Invalid refresh token');

      const userId = Number(decoded.sub);
      const jti = decoded.jti as string | undefined;
      if (!userId || !jti) return ResponseUtil.unauthorized('Invalid refresh token');

      const active = await refreshKeyExists(this.redis, this.configService, userId, jti);
      if (!active) return ResponseUtil.unauthorized('Refresh token revoked or expired');

      await deleteRefreshKey(this.redis, this.configService, userId, jti);

      const { accessToken, refreshToken: newRt, accessTtlSec } = await issueAndStoreNewTokens(this.jwtService, this.configService, this.redis, userId, decoded.email as string | undefined);

      return ResponseUtil.success({ token: accessToken, refreshToken: newRt, expiresIn: accessTtlSec }, 'Token refreshed successfully.');
    } catch (error) {
      return ResponseUtil.unauthorized('Invalid or expired token');
    }
  }

  async me(userId: number) {
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user) return ResponseUtil.unauthorized('Không thể lấy thông tin user');
    return ResponseUtil.success(safeUser(user), 'Lấy thông tin user thành công');
  }
}
