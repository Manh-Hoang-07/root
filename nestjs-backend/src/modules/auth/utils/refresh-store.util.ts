import { ConfigService } from '@nestjs/config';
import { RedisUtil } from '../../../core/utils/redis.util';

function getEnv(config: ConfigService): string {
  return config.get<string>('app.environment') || process.env.NODE_ENV || 'development';
}

export function buildRefreshKey(config: ConfigService, userId: number, jti: string): string {
  return `auth:refresh:${getEnv(config)}:${userId}:${jti}`;
}

export async function storeRefreshToken(
  redis: RedisUtil | undefined,
  config: ConfigService,
  userId: number,
  jti: string,
  ttlSeconds: number,
): Promise<void> {
  if (!redis || !redis.isEnabled()) return;
  const key = buildRefreshKey(config, userId, jti);
  await redis.set(key, '1', ttlSeconds);
}

export async function refreshKeyExists(
  redis: RedisUtil | undefined,
  config: ConfigService,
  userId: number,
  jti: string,
): Promise<boolean> {
  if (!redis || !redis.isEnabled()) return true; // dev fallback
  const key = buildRefreshKey(config, userId, jti);
  const val = await redis.get(key);
  return !!val;
}

export async function deleteRefreshKey(
  redis: RedisUtil | undefined,
  config: ConfigService,
  userId: number,
  jti: string,
): Promise<void> {
  if (!redis || !redis.isEnabled()) return;
  const key = buildRefreshKey(config, userId, jti);
  await redis.del(key).catch(() => undefined);
}

export function buildBlacklistKey(config: ConfigService, token: string): string {
  return `auth:blacklist:${getEnv(config)}:${token}`;
}


