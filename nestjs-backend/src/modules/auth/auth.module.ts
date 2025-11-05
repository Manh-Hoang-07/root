import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { JwtModule } from '@nestjs/jwt';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { ThrottlerModule, ThrottlerGuard } from '@nestjs/throttler';
import { APP_GUARD } from '@nestjs/core';
import { AuthService } from './services/auth.service';
import { AuthController } from './controllers/auth.controller';
import { User } from '../../shared/entities/user.entity';
import { Profile } from '../../shared/entities/profile.entity';
import jwtConfig from '../../core/config/jwt.config';
import { JwtStrategy } from './strategies/jwt.strategy';
import { TokenService } from './services/token.service';
import { RedisThrottlerStorageService } from '../../core/security/redis-throttler-storage.service';
import { RedisUtil } from '../../core/utils/redis.util';

@Module({
  imports: [
    TypeOrmModule.forFeature([User, Profile]),
    ConfigModule.forFeature(jwtConfig),
    JwtModule.registerAsync({
      inject: [ConfigService],
      useFactory: (configService: ConfigService) => ({
        secret: configService.get<string>('jwt.secret'),
        signOptions: {
          // jsonwebtoken@9 has stricter typings for expiresIn
          expiresIn: (configService.get<string>('jwt.expiresIn') || '60m') as any,
          issuer: configService.get<string>('jwt.issuer'),
          audience: configService.get<string>('jwt.audience'),
        },
      }),
    }),
    ThrottlerModule.forRootAsync({
      inject: [RedisUtil],
      useFactory: (redis: RedisUtil) => {
        // Use Redis storage if available, otherwise fallback to in-memory
        const storage = redis.isEnabled() 
          ? new RedisThrottlerStorageService(redis)
          : undefined; // undefined = use default in-memory storage
        
        return {
          throttlers: [{
            ttl: 60000, // 60 seconds
            limit: 10, // Default limit (can be overridden by @Throttle decorator)
          }],
          storage,
        };
      },
    }),
  ],
  controllers: [AuthController],
  providers: [
    AuthService,
    JwtStrategy,
    TokenService,
    {
      provide: APP_GUARD,
      useClass: ThrottlerGuard,
    },
  ],
  exports: [AuthService],
})
export class AuthModule { }


