import { Injectable } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { RedisUtil } from '../utils/redis.util';

@Injectable()
export class TokenBlacklistService {
  private readonly localSet = new Set<string>();

  constructor(
    private readonly configService: ConfigService,
    private readonly redis: RedisUtil,
  ) {}

  private buildKey(token: string): string {
    return `auth:blacklist:${token}`;
  }

  /**
   * Add a token to blacklist with TTL
   */
  async add(token: string, ttlSeconds: number): Promise<void> {
    const key = this.buildKey(token);
    if (this.redis && this.redis.isEnabled()) {
      await this.redis.set(key, '1', ttlSeconds).catch(() => this.localSet.add(token));
    } else {
      this.localSet.add(token);
    }
  }

  /**
   * Check blacklist in-memory only (fast path)
   */
  isBlacklisted(token: string): boolean { return this.localSet.has(token); }

  /**
   * Check blacklist with Redis (fallback to in-memory)
   */
  async has(token: string): Promise<boolean> {
    if (this.redis && this.redis.isEnabled()) {
      const key = this.buildKey(token);
      const val = await this.redis.get(key);
      if (val) return true;
    }
    return this.isBlacklisted(token);
  }
}


