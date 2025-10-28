import { SetMetadata } from '@nestjs/common';

export const IS_PUBLIC_KEY = 'isPublic';

/**
 * Decorator to mark routes as public (bypassing authentication)
 * Use this decorator on controllers or individual route handlers
 * that should be accessible without authentication
 */
export const Public = () => SetMetadata(IS_PUBLIC_KEY, true);

/**
 * Decorator to mark routes as requiring authentication (default behavior)
 * This is mainly for explicit declaration and documentation purposes
 */
export const Protected = () => SetMetadata(IS_PUBLIC_KEY, false);

/**
 * Decorator to allow both authenticated and unauthenticated access
 * User information will be populated if available, but route won't fail if not authenticated
 */
export const Optional = () => SetMetadata('isOptional', true);
