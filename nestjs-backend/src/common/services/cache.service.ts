import { Injectable, Inject } from '@nestjs/common';
import { CACHE_MANAGER } from '@nestjs/cache-manager';
import type { Cache } from 'cache-manager';
import { RedisUtil } from '../../core/utils/redis.util';

@Injectable()
export class CacheService {
  constructor(
    @Inject(CACHE_MANAGER) private cacheManager: Cache,
    private readonly redis: RedisUtil,
  ) {}

  /**
   * Lấy giá trị từ cache
   */
  async get<T>(key: string): Promise<T | undefined> {
    return await this.cacheManager.get<T>(key);
  }

  /**
   * Lưu giá trị vào cache
   */
  async set<T>(key: string, value: T, ttl?: number): Promise<void> {
    await this.cacheManager.set(key, value, ttl);
  }

  /**
   * Xóa giá trị khỏi cache
   */
  async del(key: string): Promise<void> {
    await this.cacheManager.del(key);
  }

  /**
   * Xóa tất cả cache
   */
  async reset(): Promise<void> {
    await this.cacheManager.clear();
  }

  /**
   * Lấy hoặc set cache với callback
   */
  async getOrSet<T>(
    key: string,
    callback: () => Promise<T>,
    ttl?: number,
  ): Promise<T> {
    const cached = await this.get<T>(key);
    if (cached !== undefined) {
      return cached;
    }

    const value = await callback();
    await this.set(key, value, ttl);
    return value;
  }

  /**
   * Xóa cache theo pattern (prefix)
   */
  async deletePattern(pattern: string): Promise<void> {
    if (this.redis?.isEnabled()) {
      const keys = await this.redis.keys(pattern);
      await Promise.all(keys.map(key => this.redis.del(key)));
    }
  }
}

