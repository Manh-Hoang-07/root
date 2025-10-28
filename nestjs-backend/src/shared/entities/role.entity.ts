import { Entity, Column, ManyToMany } from 'typeorm';
import { BaseEntity } from '../../common/base/base.entity';
import { User } from './user.entity';

@Entity('roles')
export class Role extends BaseEntity {
  @Column({
    type: 'varchar',
    length: 50,
    unique: true,
  })
  name: string;

  @Column({
    type: 'varchar',
    length: 100,
  })
  displayName: string;

  @Column({
    type: 'text',
    nullable: true,
  })
  description?: string;

  @Column({
    type: 'json',
    nullable: true,
  })
  permissions?: string[];

  @Column({
    type: 'int',
    default: 0,
  })
  level: number; // For role hierarchy (0 = lowest, higher numbers = more privileged)

  @Column({
    type: 'boolean',
    default: true,
  })
  isActive: boolean;

  @Column({
    type: 'boolean',
    default: false,
  })
  isSystem: boolean; // System roles cannot be deleted

  @Column({
    type: 'varchar',
    length: 20,
    nullable: true,
  })
  color?: string; // For UI display

  @Column({
    type: 'varchar',
    length: 50,
    nullable: true,
  })
  icon?: string; // For UI display

  // Relationships
  @ManyToMany(() => User, (user) => user.roles)
  users: User[];

  // Virtual properties
  get userCount(): number {
    return this.users?.length || 0;
  }

  // Helper methods
  hasPermission(permission: string): boolean {
    return this.permissions?.includes(permission) || false;
  }

  hasAnyPermission(permissions: string[]): boolean {
    if (!this.permissions) return false;
    return permissions.some(permission => this.permissions.includes(permission));
  }

  hasAllPermissions(permissions: string[]): boolean {
    if (!this.permissions) return false;
    return permissions.every(permission => this.permissions.includes(permission));
  }

  addPermission(permission: string): void {
    if (!this.permissions) {
      this.permissions = [];
    }
    if (!this.permissions.includes(permission)) {
      this.permissions.push(permission);
    }
  }

  removePermission(permission: string): void {
    if (this.permissions) {
      this.permissions = this.permissions.filter(p => p !== permission);
    }
  }

  addPermissions(permissions: string[]): void {
    permissions.forEach(permission => this.addPermission(permission));
  }

  removePermissions(permissions: string[]): void {
    permissions.forEach(permission => this.removePermission(permission));
  }

  isHigherThan(otherRole: Role): boolean {
    return this.level > otherRole.level;
  }

  isLowerThan(otherRole: Role): boolean {
    return this.level < otherRole.level;
  }

  isSameLevel(otherRole: Role): boolean {
    return this.level === otherRole.level;
  }
}
