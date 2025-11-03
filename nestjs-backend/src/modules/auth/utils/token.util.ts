import { ConfigService } from '@nestjs/config';
import { JwtService } from '@nestjs/jwt';
import * as jwt from 'jsonwebtoken';
import { RedisUtil } from '../../../core/utils/redis.util';
import { storeRefreshToken } from './refresh-store.util';

const DEFAULT_AT_TTL = 3600;
const DEFAULT_RT_TTL = 86400;

export function parseDurationToSeconds(input: string | undefined | null, fallback: number): number {
  if (!input) return fallback;
  const match = /^(\d+)([smhd])?$/.exec(input.trim());
  if (!match) return fallback;
  const val = parseInt(match[1], 10);
  const unit = match[2] || 's';
  switch (unit) {
    case 's': return val;
    case 'm': return val * 60;
    case 'h': return val * 3600;
    case 'd': return val * 86400;
    default: return fallback;
  }
}

export function getAccessTtlSec(config: ConfigService): number {
  const exp = config.get<string>('jwt.expiresIn') || process.env.JWT_EXPIRES_IN;
  return parseDurationToSeconds(exp, DEFAULT_AT_TTL);
}

export function getRefreshTtlSec(config: ConfigService): number {
  const exp = config.get<string>('jwt.refreshExpiresIn') || process.env.JWT_REFRESH_EXPIRES_IN;
  return parseDurationToSeconds(exp, DEFAULT_RT_TTL);
}

export function getJwtIssuer(config: ConfigService): string | undefined {
  return config.get<string>('jwt.issuer');
}

export function getJwtAudience(config: ConfigService): string | undefined {
  return config.get<string>('jwt.audience');
}

export function getRefreshSecret(config: ConfigService): string {
  return (config.get<string>('jwt.refreshSecret') || process.env.JWT_REFRESH_SECRET) as string;
}

export function generateJti(): string {
  return Math.random().toString(36).slice(2) + Date.now().toString(36);
}

export function generateTokens(
  jwtService: JwtService,
  config: ConfigService,
  userId: number,
  email?: string,
) {
  const payload = { sub: userId, email } as Record<string, any>;
  const accessToken = jwtService.sign(payload);
  const accessTtlSec = getAccessTtlSec(config);

  const jti = generateJti();
  const issuer = getJwtIssuer(config);
  const audience = getJwtAudience(config);
  const refreshSecret = getRefreshSecret(config);
  const refreshExpiresIn = (config.get<string>('jwt.refreshExpiresIn') || process.env.JWT_REFRESH_EXPIRES_IN || '1d') as any;

  const refreshToken = jwt.sign(
    { sub: userId, email, jti },
    refreshSecret,
    { expiresIn: refreshExpiresIn, issuer, audience }
  );

  const refreshTtlSec = getRefreshTtlSec(config);
  return { accessToken, refreshToken, refreshJti: jti, accessTtlSec, refreshTtlSec } as const;
}

export function verifyRefreshToken(refreshToken: string, config: ConfigService) {
  const refreshSecret = getRefreshSecret(config);
  const audience = getJwtAudience(config);
  const issuer = getJwtIssuer(config);
  return jwt.verify(refreshToken, refreshSecret, { audience, issuer }) as jwt.JwtPayload & {
    sub: number | string;
    jti?: string;
    email?: string;
  };
}

export function decodeRefresh(refreshToken: string, config: ConfigService) {
  try {
    return verifyRefreshToken(refreshToken, config);
  } catch {
    return null;
  }
}

export async function issueAndStoreNewTokens(
  jwtService: JwtService,
  config: ConfigService,
  redis: RedisUtil | undefined,
  userId: number,
  email?: string,
) {
  const { accessToken, refreshToken, refreshJti, accessTtlSec } = generateTokens(jwtService, config, userId, email);
  await storeRefreshToken(redis, config, userId, refreshJti, getRefreshTtlSec(config)).catch(() => undefined);
  return { accessToken, refreshToken, accessTtlSec } as const;
}


