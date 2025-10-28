import { Entity, Column, ManyToMany, JoinTable, OneToMany } from 'typeorm';
import { BaseEntity } from '../../common/base/base.entity';
import { Role } from './role.entity';
import { Post } from './post.entity';

@Entity('users')
export class User extends BaseEntity {
  @Column({
    type: 'varchar',
    length: 100,
    unique: true,
  })
  email: string;

  @Column({
    type: 'varchar',
    length: 50,
    unique: true,
    nullable: true,
  })
  username?: string;

  @Column({
    type: 'varchar',
    length: 255,
    select: false, // Don't select password by default
  })
  password: string;

  @Column({
    type: 'varchar',
    length: 50,
    nullable: true,
  })
  firstName?: string;

  @Column({
    type: 'varchar',
    length: 50,
    nullable: true,
  })
  lastName?: string;

  @Column({
    type: 'varchar',
    length: 15,
    nullable: true,
  })
  phone?: string;

  @Column({
    type: 'text',
    nullable: true,
  })
  avatar?: string;

  @Column({
    type: 'date',
    nullable: true,
  })
  dateOfBirth?: Date;

  @Column({
    type: 'enum',
    enum: ['male', 'female', 'other'],
    nullable: true,
  })
  gender?: 'male' | 'female' | 'other';

  @Column({
    type: 'text',
    nullable: true,
  })
  address?: string;

  @Column({
    type: 'varchar',
    length: 50,
    nullable: true,
  })
  city?: string;

  @Column({
    type: 'varchar',
    length: 50,
    nullable: true,
  })
  country?: string;

  @Column({
    type: 'varchar',
    length: 10,
    nullable: true,
  })
  zipCode?: string;

  @Column({
    type: 'boolean',
    default: true,
  })
  isActive: boolean;

  @Column({
    type: 'boolean',
    default: false,
  })
  emailVerified: boolean;

  @Column({
    type: 'timestamp',
    nullable: true,
  })
  emailVerifiedAt?: Date;

  @Column({
    type: 'varchar',
    length: 255,
    nullable: true,
  })
  emailVerificationToken?: string;

  @Column({
    type: 'varchar',
    length: 255,
    nullable: true,
  })
  passwordResetToken?: string;

  @Column({
    type: 'timestamp',
    nullable: true,
  })
  passwordResetTokenExpiresAt?: Date;

  @Column({
    type: 'timestamp',
    nullable: true,
  })
  lastLoginAt?: Date;

  @Column({
    type: 'varchar',
    length: 45,
    nullable: true,
  })
  lastLoginIp?: string;

  @Column({
    type: 'int',
    default: 0,
  })
  loginAttempts: number;

  @Column({
    type: 'timestamp',
    nullable: true,
  })
  lockedUntil?: Date;

  // Relationships
  @ManyToMany(() => Role, (role) => role.users, {
    cascade: true,
    onDelete: 'CASCADE',
  })
  @JoinTable({
    name: 'user_roles',
    joinColumn: {
      name: 'user_id',
      referencedColumnName: 'id',
    },
    inverseJoinColumn: {
      name: 'role_id',
      referencedColumnName: 'id',
    },
  })
  roles: Role[];

  @OneToMany(() => Post, (post) => post.author)
  posts: Post[];

  // Virtual properties
  get fullName(): string {
    if (this.firstName && this.lastName) {
      return `${this.firstName} ${this.lastName}`;
    }
    return this.firstName || this.lastName || this.username || this.email;
  }

  get isLocked(): boolean {
    return !!(this.lockedUntil && this.lockedUntil > new Date());
  }

  get roleNames(): string[] {
    return this.roles?.map(role => role.name) || [];
  }

  // Helper methods
  toJSON(): any {
    const { password, passwordResetToken, emailVerificationToken, ...result } = super.toJSON();
    return result;
  }

  hasRole(roleName: string): boolean {
    return this.roleNames.includes(roleName);
  }

  hasAnyRole(roleNames: string[]): boolean {
    return roleNames.some(role => this.hasRole(role));
  }

  hasAllRoles(roleNames: string[]): boolean {
    return roleNames.every(role => this.hasRole(role));
  }

  lock(duration: number = 30): void {
    this.lockedUntil = new Date(Date.now() + duration * 60 * 1000); // duration in minutes
  }

  unlock(): void {
    this.lockedUntil = null;
    this.loginAttempts = 0;
  }

  incrementLoginAttempts(): void {
    this.loginAttempts += 1;
    
    // Lock account after 5 failed attempts
    if (this.loginAttempts >= 5) {
      this.lock(30); // Lock for 30 minutes
    }
  }

  resetLoginAttempts(): void {
    this.loginAttempts = 0;
    this.lockedUntil = null;
  }

  updateLastLogin(ip?: string): void {
    this.lastLoginAt = new Date();
    this.lastLoginIp = ip;
    this.resetLoginAttempts();
  }
}
