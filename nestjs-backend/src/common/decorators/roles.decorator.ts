import { SetMetadata } from '@nestjs/common';

export const ROLES_KEY = 'roles';

/**
 * Decorator to set required roles for a route or controller
 * @param roles - Array of role names required to access the resource
 */
// Temporarily no-op: roles metadata not used while role checks are disabled
export const Roles = (..._roles: string[]) => SetMetadata(ROLES_KEY, []);

/**
 * Common role constants
 */
export const ROLE = {
  SUPER_ADMIN: 'super_admin',
  ADMIN: 'admin',
  MODERATOR: 'moderator',
  USER: 'user',
  GUEST: 'guest',
} as const;

export type RoleType = typeof ROLE[keyof typeof ROLE];
