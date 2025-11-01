import { SetMetadata } from '@nestjs/common';

export const ROLES_REQUIRED_KEY = 'roles_required';
export const PERMS_REQUIRED_KEY = 'perms_required';

export const RolesRequired = (...roles: string[]) => SetMetadata(ROLES_REQUIRED_KEY, roles);
export const PermissionsRequired = (...permissions: string[]) => SetMetadata(PERMS_REQUIRED_KEY, permissions);




