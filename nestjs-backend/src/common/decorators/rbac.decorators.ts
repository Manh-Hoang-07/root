import { SetMetadata } from '@nestjs/common';

export const ROLES_REQUIRED_KEY = 'roles_required';
export const PERMS_REQUIRED_KEY = 'perms_required';
export const IS_PUBLIC_KEY = 'isPublic';
export const IS_OPTIONAL_KEY = 'isOptional';

// Permission constant để đánh dấu route public
export const PUBLIC_PERMISSION = 'public';

export const RolesRequired = (...roles: string[]) => SetMetadata(ROLES_REQUIRED_KEY, roles);
export const PermissionsRequired = (...permissions: string[]) => SetMetadata(PERMS_REQUIRED_KEY, permissions);

/**
 * Decorator đơn giản để kiểm tra permission
 * 
 * @example
 * ```typescript
 * @Permission('post.create')
 * @Post()
 * createPost() { ... }
 * 
 * @Permission('post.update', 'post.delete')  // Có thể truyền nhiều permissions
 * @Put(':id')
 * updatePost() { ... }
 * 
 * // Route public (không bắt buộc authentication)
 * @Permission('public')
 * @Get('public')
 * getPublic() { ... }
 * 
 * // Hoặc không cần khai báo gì - mặc định là public
 * @Get('posts')
 * getPosts() { ... }
 * ```
 */
export const Permission = (...permissions: string[]) => SetMetadata(PERMS_REQUIRED_KEY, permissions);

/**
 * Decorator để đánh dấu route là public (backward compatibility)
 * @deprecated Sử dụng @Permission('public') hoặc không khai báo gì (mặc định public)
 */
export const Public = () => SetMetadata(PERMS_REQUIRED_KEY, [PUBLIC_PERMISSION]);

/**
 * Decorator để đánh dấu route cần authentication (backward compatibility)
 * @deprecated Mặc định route có @Permission() sẽ cần authentication
 */
export const Protected = () => SetMetadata(IS_PUBLIC_KEY, false);

/**
 * Decorator để cho phép cả authenticated và unauthenticated access
 * User information sẽ được populate nếu có, nhưng route không fail nếu không authenticated
 */
export const Optional = () => SetMetadata(IS_OPTIONAL_KEY, true);




