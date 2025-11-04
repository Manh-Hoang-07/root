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
import { TokenService } from './token.service';
import { TokenBlacklistService } from '../../../core/security/token-blacklist.service';
import { AttemptLimiterService } from '../../../core/security/attempt-limiter.service';
import { safeUser } from '../utils/user.util';
import { ForgotPasswordDto } from '../dto/forgot-password.dto';
import { ResetPasswordDto } from '../dto/reset-password.dto';
import * as crypto from 'crypto';

@Injectable()
export class AuthService {
  constructor(
    @InjectRepository(User) private readonly userRepository: Repository<User>,
    @InjectRepository(Profile) private readonly profileRepository: Repository<Profile>,
    private readonly jwtService: JwtService,
    private readonly configService: ConfigService,
    private readonly redis: RedisUtil,
    private readonly tokenBlacklistService: TokenBlacklistService,
    private readonly tokenService: TokenService,
    private readonly accountLockoutService: AttemptLimiterService,
  ) { }

  async login(dto: LoginDto) {
    const identifier = dto.email.toLowerCase();
    const scope = 'auth:login';
    const lockout = await this.accountLockoutService.check(scope, identifier);
    
    if (lockout.isLocked) {
      return ResponseUtil.unauthorized(
        `Tài khoản đã bị khóa tạm thời do quá nhiều lần đăng nhập sai. Vui lòng thử lại sau ${lockout.remainingMinutes} phút.`
      );
    }

    const user = await this.userRepository.findOne({
      where: { email: dto.email },
      select: { id: true, email: true, username: true, password: true, status: true, },
    });

    let authError: string | null = null;

    if (!user || !user.password) {
      await this.accountLockoutService.add(scope, identifier);
      authError = 'Email hoặc mật khẩu không đúng.';
    } else {
      const isPasswordValid = await bcrypt.compare(dto.password, user.password);
      if (!isPasswordValid) {
        await this.accountLockoutService.add(scope, identifier);
        authError = 'Email hoặc mật khẩu không đúng.';
      } else if (user.status !== UserStatus.Active) {
        authError = 'Tài khoản đã bị khóa hoặc không hoạt động.';
      }
    }

    if (authError) {
      return ResponseUtil.unauthorized(authError);
    }

    await this.accountLockoutService.reset(scope, identifier);

    this.userRepository
      .update({ id: user.id }, { last_login_at: new Date() })
      .catch(() => undefined);

    const { accessToken, refreshToken, refreshJti, accessTtlSec } = this.tokenService.generateTokens(user.id, user.email);

    await this.redis.set(this.buildRefreshKey(user.id, refreshJti), '1', this.tokenService.getRefreshTtlSec()).catch(() => undefined);

    return ResponseUtil.success(
      { token: accessToken, refreshToken: refreshToken, expiresIn: accessTtlSec, },
      'Đăng nhập thành công.',
    );
  }

  async register(dto: RegisterDto) {
    const existingByEmail = await this.userRepository.findOne({ where: { email: dto.email } });
    let registerError: string | null = null;
    if (existingByEmail) {
      registerError = 'Email đã được sử dụng.';
    }

    if (!registerError && dto.username) {
      const existingByUsername = await this.userRepository.findOne({ where: { username: dto.username } });
      if (existingByUsername) {
        registerError = 'Tên đăng nhập đã được sử dụng.';
      }
    }

    if (!registerError && dto.phone) {
      const existingByPhone = await this.userRepository.findOne({ where: { phone: dto.phone } });
      if (existingByPhone) {
        registerError = 'Số điện thoại đã được sử dụng.';
      }
    }

    if (registerError) {
      return ResponseUtil.badRequest(registerError);
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
      const ttlSeconds = this.tokenService.getAccessTtlSec();
      await this.tokenBlacklistService.add(token, ttlSeconds);
    }
    return ResponseUtil.success(null, 'Đăng xuất thành công.');
  }

  async refreshTokenByValue(refreshToken: string) {
    try {
      let refreshError: string | null = null;
      const decoded = this.tokenService.decodeRefresh(refreshToken);
      if (!decoded) {
        refreshError = 'Invalid refresh token';
      }

      let userId: number | undefined;
      let jti: string | undefined;
      if (!refreshError) {
        userId = Number(decoded!.sub);
        jti = decoded!.jti as string | undefined;
        if (!userId || !jti) {
          refreshError = 'Invalid refresh token';
        }
      }

      if (!refreshError) {
      const active = !!(await this.redis.get(this.buildRefreshKey(userId!, jti!)));
        if (!active) {
          refreshError = 'Refresh token revoked or expired';
        }
      }

      if (refreshError) {
        return ResponseUtil.unauthorized(refreshError);
      }

      await this.redis.del(this.buildRefreshKey(userId!, jti!));

      const { accessToken, refreshToken: newRt, accessTtlSec } = await this.tokenService.issueAndStoreNewTokens(userId!, (decoded as any).email as string | undefined);

      return ResponseUtil.success({ token: accessToken, refreshToken: newRt, expiresIn: accessTtlSec }, 'Token refreshed successfully.');
    } catch (error) {
      return ResponseUtil.unauthorized('Invalid or expired token');
    }
  }

  private buildRefreshKey(userId: number, jti: string): string {
    return `auth:refresh:${userId}:${jti}`;
  }

  async me(userId: number) {
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user) return ResponseUtil.unauthorized('Không thể lấy thông tin user');
    return ResponseUtil.success(safeUser(user), 'Lấy thông tin user thành công');
  }

  async forgotPassword(dto: ForgotPasswordDto) {
    const user = await this.userRepository.findOne({
      where: { email: dto.email },
      select: { id: true, email: true },
    });

    if (user && user.email) {
      const token = crypto.randomBytes(32).toString('hex');
      const key = `password_reset:${token}`;
      const data = JSON.stringify({ userId: user.id, email: user.email });
      
      await this.redis.set(key, data, 3600); // 1 hour TTL

      // Log token in development
      if (this.configService.get('app.environment') === 'development') {
        const resetUrl = `${process.env.FRONTEND_URL || 'http://localhost:3000'}/reset-password?token=${token}`;
        console.log(`Password reset for ${user.email}: ${token}`);
        console.log(`Reset URL: ${resetUrl}`);
      }
    }

    return ResponseUtil.success(
      null,
      'Nếu email tồn tại trong hệ thống, bạn sẽ nhận được email hướng dẫn đặt lại mật khẩu.',
    );
  }

  async resetPassword(dto: ResetPasswordDto) {
    let resetError: string | null = null;

    if (dto.password !== dto.confirmPassword) {
      resetError = 'Mật khẩu xác nhận không khớp.';
    }

    const key = `password_reset:${dto.token}`;
    const data = resetError ? null : await this.redis.get(key);

    if (!resetError && !data) {
      resetError = 'Token không hợp lệ hoặc đã hết hạn.';
    }

    let userIdForReset: number | undefined;
    if (!resetError && data) {
      try {
        const tokenData = JSON.parse(data);
        userIdForReset = Number(tokenData.userId);
        if (!userIdForReset) resetError = 'Token không hợp lệ hoặc đã hết hạn.';
      } catch {
        resetError = 'Token không hợp lệ hoặc đã hết hạn.';
      }
    }

    let user: Pick<User, 'id' | 'email'> | null = null;
    if (!resetError && userIdForReset) {
      user = await this.userRepository.findOne({
        where: { id: userIdForReset },
        select: { id: true, email: true },
      });
      if (!user) resetError = 'Người dùng không tồn tại.';
    }

    if (resetError) {
      return ResponseUtil.badRequest(resetError);
    }

    const hashedPassword = await bcrypt.hash(dto.password, 10);
    await this.userRepository.update({ id: user!.id }, { password: hashedPassword });
    await this.redis.del(key);
    await this.accountLockoutService.reset('auth:login', user!.email!.toLowerCase());

    return ResponseUtil.success(null, 'Đặt lại mật khẩu thành công. Vui lòng đăng nhập lại.');
  }
}
