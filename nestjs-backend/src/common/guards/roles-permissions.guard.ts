import { CanActivate, ExecutionContext, Injectable } from '@nestjs/common';
import { Reflector } from '@nestjs/core';
import { ROLES_REQUIRED_KEY, PERMS_REQUIRED_KEY } from '../decorators/rbac.decorators';
import { RbacService } from '../../modules/rbac/rbac.service';

@Injectable()
export class RolesPermissionsGuard implements CanActivate {
  constructor(private reflector: Reflector, private rbac: RbacService) {}

  async canActivate(context: ExecutionContext): Promise<boolean> {
    const requiredRoles = this.reflector.getAllAndOverride<string[]>(ROLES_REQUIRED_KEY, [
      context.getHandler(),
      context.getClass(),
    ]) || [];
    const requiredPerms = this.reflector.getAllAndOverride<string[]>(PERMS_REQUIRED_KEY, [
      context.getHandler(),
      context.getClass(),
    ]) || [];

    if (requiredRoles.length === 0 && requiredPerms.length === 0) return true;

    const req = context.switchToHttp().getRequest();
    const user = req.user as { sub?: number; id?: number } | undefined;
    const userId = user?.id ?? user?.sub;
    if (!userId) return false;

    if (requiredRoles.length > 0) {
      const ok = await this.rbac.userHasRoles(userId, requiredRoles);
      if (!ok) return false;
    }
    if (requiredPerms.length > 0) {
      const ok = await this.rbac.userHasPermissions(userId, requiredPerms);
      if (!ok) return false;
    }
    return true;
  }
}




