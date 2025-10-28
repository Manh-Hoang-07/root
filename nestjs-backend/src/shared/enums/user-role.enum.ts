export enum UserRole {
  SUPER_ADMIN = 'super_admin',
  ADMIN = 'admin',
  MODERATOR = 'moderator',
  EDITOR = 'editor',
  AUTHOR = 'author',
  USER = 'user',
  GUEST = 'guest',
}

export enum UserStatus {
  ACTIVE = 'active',
  INACTIVE = 'inactive',
  SUSPENDED = 'suspended',
  BANNED = 'banned',
  PENDING = 'pending',
}

export enum UserGender {
  MALE = 'male',
  FEMALE = 'female',
  OTHER = 'other',
}

/**
 * Role hierarchy levels for permission checking
 */
export const ROLE_HIERARCHY: Record<UserRole, number> = {
  [UserRole.SUPER_ADMIN]: 100,
  [UserRole.ADMIN]: 80,
  [UserRole.MODERATOR]: 60,
  [UserRole.EDITOR]: 40,
  [UserRole.AUTHOR]: 30,
  [UserRole.USER]: 10,
  [UserRole.GUEST]: 0,
};

/**
 * Default permissions for each role
 */
export const ROLE_PERMISSIONS: Record<UserRole, string[]> = {
  [UserRole.SUPER_ADMIN]: [
    'system:*',
    'users:*',
    'roles:*',
    'posts:*',
    'products:*',
    'orders:*',
    'settings:*',
  ],
  [UserRole.ADMIN]: [
    'users:read',
    'users:write',
    'roles:read',
    'posts:*',
    'products:*',
    'orders:*',
    'settings:read',
  ],
  [UserRole.MODERATOR]: [
    'users:read',
    'posts:*',
    'products:read',
    'orders:read',
  ],
  [UserRole.EDITOR]: [
    'posts:*',
    'products:read',
  ],
  [UserRole.AUTHOR]: [
    'posts:read',
    'posts:write',
    'posts:own',
  ],
  [UserRole.USER]: [
    'profile:read',
    'profile:write',
    'orders:own',
  ],
  [UserRole.GUEST]: [
    'public:read',
  ],
};

/**
 * Check if a role has higher privileges than another
 */
export function isHigherRole(role1: UserRole, role2: UserRole): boolean {
  return ROLE_HIERARCHY[role1] > ROLE_HIERARCHY[role2];
}

/**
 * Check if a role has permission for an action
 */
export function hasPermission(role: UserRole, permission: string): boolean {
  const permissions = ROLE_PERMISSIONS[role];
  
  // Check for exact match
  if (permissions.includes(permission)) {
    return true;
  }
  
  // Check for wildcard permissions
  const parts = permission.split(':');
  if (parts.length === 2) {
    const [resource, action] = parts;
    return permissions.includes(`${resource}:*`) || permissions.includes('*');
  }
  
  return permissions.includes('*');
}

/**
 * Get all permissions for a role
 */
export function getRolePermissions(role: UserRole): string[] {
  return ROLE_PERMISSIONS[role] || [];
}

/**
 * Get role display name
 */
export function getRoleDisplayName(role: UserRole): string {
  const displayNames: Record<UserRole, string> = {
    [UserRole.SUPER_ADMIN]: 'Super Administrator',
    [UserRole.ADMIN]: 'Administrator',
    [UserRole.MODERATOR]: 'Moderator',
    [UserRole.EDITOR]: 'Editor',
    [UserRole.AUTHOR]: 'Author',
    [UserRole.USER]: 'User',
    [UserRole.GUEST]: 'Guest',
  };
  
  return displayNames[role] || role;
}
