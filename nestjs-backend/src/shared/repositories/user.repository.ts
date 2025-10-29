import { Injectable } from '@nestjs/common';
import { DataSource, FindOptionsWhere } from 'typeorm';
import { BaseRepository } from '../../common/base/repositories/base.repository';
import { User } from '../entities/user.entity';

@Injectable()
export class UserRepository extends BaseRepository<User> {
  constructor(dataSource: DataSource) {
    super(User, dataSource);
  }

  /**
   * Find user by email
   */
  async findByEmail(email: string): Promise<User | null> {
    return this.findOne({
      where: { email, deletedAt: null } as FindOptionsWhere<User>,
    });
  }

  /**
   * Find user by username
   */
  async findByUsername(username: string): Promise<User | null> {
    return this.findOne({
      where: { username, deletedAt: null } as FindOptionsWhere<User>,
    });
  }

  /**
   * Find user by email or username
   */
  async findByEmailOrUsername(identifier: string): Promise<User | null> {
    return this.findOne([
      { email: identifier, deletedAt: null },
      { username: identifier, deletedAt: null },
    ] as FindOptionsWhere<User>[]);
  }

  /**
   * Find user with password (for authentication)
   */
  async findByEmailWithPassword(email: string): Promise<User | null> {
    return this.findOne({
      where: { email, deletedAt: null } as FindOptionsWhere<User>,
      select: [
        'id', 'email', 'username', 'password', 'firstName', 'lastName',
        'isActive', 'emailVerified', 'loginAttempts', 'lockedUntil'
      ],
      relations: ['roles'],
    });
  }

  /**
   * Find user by verification token
   */
  async findByEmailVerificationToken(token: string): Promise<User | null> {
    return this.findOne({
      where: { emailVerificationToken: token, deletedAt: null } as FindOptionsWhere<User>,
    });
  }

  /**
   * Find user by password reset token
   */
  async findByPasswordResetToken(token: string): Promise<User | null> {
    return this.findOne({
      where: {
        passwordResetToken: token,
        deletedAt: null,
      } as FindOptionsWhere<User>,
    });
  }

  /**
   * Find users by role
   */
  async findByRole(roleName: string): Promise<User[]> {
    return this.createQueryBuilder('user')
      .leftJoinAndSelect('user.roles', 'role')
      .where('role.name = :roleName', { roleName })
      .andWhere('user.deletedAt IS NULL')
      .getMany();
  }

  /**
   * Find active users with pagination
   */
  async findActiveUsersWithPagination(
    page: number = 1,
    limit: number = 10,
    searchTerm?: string,
  ) {
    const query = this.createQueryBuilder('user')
      .leftJoinAndSelect('user.roles', 'roles')
      .where('user.deletedAt IS NULL')
      .andWhere('user.isActive = :isActive', { isActive: true });

    if (searchTerm) {
      query.andWhere(
        '(user.email LIKE :searchTerm OR user.username LIKE :searchTerm OR user.firstName LIKE :searchTerm OR user.lastName LIKE :searchTerm)',
        { searchTerm: `%${searchTerm}%` }
      );
    }

    const [users, total] = await query
      .skip((page - 1) * limit)
      .take(limit)
      .getManyAndCount();

    return { data: users, total, page, limit };
  }

  /**
   * Find users with expired password reset tokens
   */
  async findUsersWithExpiredPasswordResetTokens(): Promise<User[]> {
    return this.createQueryBuilder('user')
      .where('user.passwordResetTokenExpiresAt < :now', { now: new Date() })
      .andWhere('user.passwordResetToken IS NOT NULL')
      .andWhere('user.deletedAt IS NULL')
      .getMany();
  }

  /**
   * Find locked users
   */
  async findLockedUsers(): Promise<User[]> {
    return this.createQueryBuilder('user')
      .where('user.lockedUntil > :now', { now: new Date() })
      .andWhere('user.deletedAt IS NULL')
      .getMany();
  }

  /**
   * Find users who should be unlocked (lock expired)
   */
  async findUsersToUnlock(): Promise<User[]> {
    return this.createQueryBuilder('user')
      .where('user.lockedUntil < :now', { now: new Date() })
      .andWhere('user.lockedUntil IS NOT NULL')
      .andWhere('user.deletedAt IS NULL')
      .getMany();
  }

  /**
   * Clean up expired tokens and unlock users
   */
  async cleanupExpiredTokensAndUnlockUsers(): Promise<void> {
    // Clean up expired password reset tokens
    await this.createQueryBuilder('user')
      .update()
      .set({
        passwordResetToken: null,
        passwordResetTokenExpiresAt: null,
      })
      .where('passwordResetTokenExpiresAt < :now', { now: new Date() })
      .execute();

    // Unlock users whose lock period has expired
    await this.createQueryBuilder('user')
      .update()
      .set({
        lockedUntil: null,
        loginAttempts: 0,
      })
      .where('lockedUntil < :now', { now: new Date() })
      .execute();
  }

  /**
   * Get user statistics
   */
  async getUserStatistics(): Promise<{
    total: number;
    active: number;
    inactive: number;
    verified: number;
    unverified: number;
    locked: number;
  }> {
    const query = this.createQueryBuilder('user')
      .where('user.deletedAt IS NULL');

    const [
      total,
      active,
      inactive,
      verified,
      unverified,
      locked,
    ] = await Promise.all([
      query.getCount(),
      query.clone().andWhere('user.isActive = :isActive', { isActive: true }).getCount(),
      query.clone().andWhere('user.isActive = :isActive', { isActive: false }).getCount(),
      query.clone().andWhere('user.emailVerified = :emailVerified', { emailVerified: true }).getCount(),
      query.clone().andWhere('user.emailVerified = :emailVerified', { emailVerified: false }).getCount(),
      query.clone().andWhere('user.lockedUntil > :now', { now: new Date() }).getCount(),
    ]);

    return {
      total,
      active,
      inactive,
      verified,
      unverified,
      locked,
    };
  }

  /**
   * Search users with advanced filters
   */
  async searchUsers(filters: {
    searchTerm?: string;
    role?: string;
    isActive?: boolean;
    emailVerified?: boolean;
    isLocked?: boolean;
    createdAfter?: Date;
    createdBefore?: Date;
    lastLoginAfter?: Date;
    lastLoginBefore?: Date;
  }, page: number = 1, limit: number = 10) {
    const query = this.createQueryBuilder('user')
      .leftJoinAndSelect('user.roles', 'roles')
      .where('user.deletedAt IS NULL');

    if (filters.searchTerm) {
      query.andWhere(
        '(user.email LIKE :searchTerm OR user.username LIKE :searchTerm OR user.firstName LIKE :searchTerm OR user.lastName LIKE :searchTerm)',
        { searchTerm: `%${filters.searchTerm}%` }
      );
    }

    if (filters.role) {
      query.andWhere('roles.name = :role', { role: filters.role });
    }

    if (filters.isActive !== undefined) {
      query.andWhere('user.isActive = :isActive', { isActive: filters.isActive });
    }

    if (filters.emailVerified !== undefined) {
      query.andWhere('user.emailVerified = :emailVerified', { emailVerified: filters.emailVerified });
    }

    if (filters.isLocked !== undefined) {
      if (filters.isLocked) {
        query.andWhere('user.lockedUntil > :now', { now: new Date() });
      } else {
        query.andWhere('(user.lockedUntil IS NULL OR user.lockedUntil <= :now)', { now: new Date() });
      }
    }

    if (filters.createdAfter) {
      query.andWhere('user.createdAt >= :createdAfter', { createdAfter: filters.createdAfter });
    }

    if (filters.createdBefore) {
      query.andWhere('user.createdAt <= :createdBefore', { createdBefore: filters.createdBefore });
    }

    if (filters.lastLoginAfter) {
      query.andWhere('user.lastLoginAt >= :lastLoginAfter', { lastLoginAfter: filters.lastLoginAfter });
    }

    if (filters.lastLoginBefore) {
      query.andWhere('user.lastLoginAt <= :lastLoginBefore', { lastLoginBefore: filters.lastLoginBefore });
    }

    const [users, total] = await query
      .skip((page - 1) * limit)
      .take(limit)
      .orderBy('user.createdAt', 'DESC')
      .getManyAndCount();

    return { data: users, total, page, limit };
  }
}
