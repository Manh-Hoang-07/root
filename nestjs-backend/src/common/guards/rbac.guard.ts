import { CanActivate, ExecutionContext, Injectable, HttpException, HttpStatus } from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import { ROLES_REQUIRED_KEY, PERMS_REQUIRED_KEY, PUBLIC_PERMISSION } from '../decorators/rbac.decorators';
import { RbacService } from '../../modules/rbac/services/rbac.service';
import { ResponseUtil } from '../utils/response.util';

@Injectable()
export class RbacGuard implements CanActivate {
  constructor(
    private reflector: Reflector,
    private rbac: RbacService,
  ) {}

  async canActivate(context: ExecutionContext): Promise<boolean> {
    const requiredRoles = this.reflector.getAllAndOverride<string[]>(ROLES_REQUIRED_KEY, [
      context.getHandler(),
      context.getClass(),
    ]) || [];
    const requiredPerms = this.reflector.getAllAndOverride<string[]>(PERMS_REQUIRED_KEY, [
      context.getHandler(),
      context.getClass(),
    ]) || [];

    // Nếu không có @Permission() hoặc @RolesRequired() → mặc định là public
    if (requiredRoles.length === 0 && requiredPerms.length === 0) return true;

    // Nếu có @Permission('public') → không cần check quyền
    if (requiredPerms.includes(PUBLIC_PERMISSION)) return true;

    const req = context.switchToHttp().getRequest();
    const user = req.user as { sub?: number; id?: number } | undefined;
    const userId = user?.id ?? user?.sub;
    
    if (!userId) {
      const response = ResponseUtil.unauthorized('Authentication required');
      throw new HttpException(response, response.httpStatus || HttpStatus.UNAUTHORIZED);
    }

    // Kiểm tra roles
    if (requiredRoles.length > 0) {
      const ok = await this.rbac.userHasRoles(userId, requiredRoles);
      if (!ok) {
        const response = ResponseUtil.forbidden(
          `Access denied. Required roles: ${requiredRoles.join(', ')}`
        );
        throw new HttpException(response, response.httpStatus || HttpStatus.FORBIDDEN);
      }
    }
    
    // Kiểm tra permissions
    if (requiredPerms.length > 0) {
      const ok = await this.rbac.userHasPermissions(userId, requiredPerms);
      if (!ok) {
        const response = ResponseUtil.forbidden(
          `Access denied. Required permissions: ${requiredPerms.join(', ')}`
        );
        throw new HttpException(response, response.httpStatus || HttpStatus.FORBIDDEN);
      }
    }
    
    return true;
  }
}

