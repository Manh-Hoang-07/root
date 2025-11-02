import { createParamDecorator, ExecutionContext } from '@nestjs/common';

export interface AuthUser {
  id: number;
  username?: string | null;
  email?: string | null;
  phone?: string | null;
  status: string;
  email_verified_at?: Date | null;
  phone_verified_at?: Date | null;
  last_login_at?: Date | null;
  created_at: Date;
  updated_at: Date;
  [key: string]: any;
}

/**
 * Decorator to extract user from request
 * Tương tự Laravel's Auth::user() và Auth::id()
 * 
 * @example
 * ```typescript
 * // Lấy toàn bộ user object
 * @Get('profile')
 * getProfile(@User() user: AuthUser) {
 *   return user;
 * }
 * 
 * // Lấy user ID (tương tự Auth::id())
 * @Post('posts')
 * createPost(@User('id') userId: number) {
 *   return this.postService.create({ userId, ... });
 * }
 * 
 * // Lấy email
 * @Get('email')
 * getEmail(@User('email') email: string) {
 *   return { email };
 * }
 * 
 * // Lấy bất kỳ property nào của user
 * @Get('status')
 * getStatus(@User('status') status: string) {
 *   return { status };
 * }
 * ```
 */
export const User = createParamDecorator(
  (data: keyof AuthUser | undefined, ctx: ExecutionContext): AuthUser | any => {
    const request = ctx.switchToHttp().getRequest();
    const user = request.user;

    return data ? user?.[data] : user;
  },
);


