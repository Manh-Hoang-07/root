import { createParamDecorator, ExecutionContext } from '@nestjs/common';

export interface AuthUser {
  id: string;
  email: string;
  username?: string;
  roles: string[];
  permissions?: string[];
  isActive: boolean;
  [key: string]: any;
}

/**
 * Decorator to extract user from request
 * Can be used to get the entire user object or specific properties
 */
export const User = createParamDecorator(
  (data: keyof AuthUser | undefined, ctx: ExecutionContext): AuthUser | any => {
    const request = ctx.switchToHttp().getRequest();
    const user = request.user;

    return data ? user?.[data] : user;
  },
);

/**
 * Decorator to extract user ID from request
 */
export const UserId = createParamDecorator(
  (data: unknown, ctx: ExecutionContext): string => {
    const request = ctx.switchToHttp().getRequest();
    return request.user?.id;
  },
);

/**
 * Decorator to extract user email from request
 */
export const UserEmail = createParamDecorator(
  (data: unknown, ctx: ExecutionContext): string => {
    const request = ctx.switchToHttp().getRequest();
    return request.user?.email;
  },
);

/**
 * Decorator to extract user roles from request
 */
export const UserRoles = createParamDecorator(
  (data: unknown, ctx: ExecutionContext): string[] => {
    const request = ctx.switchToHttp().getRequest();
    return request.user?.roles || [];
  },
);

/**
 * Decorator to check if user has specific role
 */
export const HasRole = createParamDecorator(
  (role: string, ctx: ExecutionContext): boolean => {
    const request = ctx.switchToHttp().getRequest();
    const userRoles = request.user?.roles || [];
    return userRoles.includes(role);
  },
);

/**
 * Decorator to check if user has any of the specified roles
 */
export const HasAnyRole = createParamDecorator(
  (roles: string[], ctx: ExecutionContext): boolean => {
    const request = ctx.switchToHttp().getRequest();
    const userRoles = request.user?.roles || [];
    return roles.some(role => userRoles.includes(role));
  },
);

/**
 * Decorator to check if user has all of the specified roles
 */
export const HasAllRoles = createParamDecorator(
  (roles: string[], ctx: ExecutionContext): boolean => {
    const request = ctx.switchToHttp().getRequest();
    const userRoles = request.user?.roles || [];
    return roles.every(role => userRoles.includes(role));
  },
);
