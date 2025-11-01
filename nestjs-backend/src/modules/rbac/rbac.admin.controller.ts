import { Body, Controller, Param, ParseIntPipe, Post } from '@nestjs/common';
import { RbacService } from './rbac.service';

@Controller('admin/rbac')
export class RbacAdminController {
  constructor(private readonly rbac: RbacService) {}

  @Post('roles')
  createRole(@Body() body: { code: string; name?: string; parent_id?: number | null; }) {
    return this.rbac.createRole(body);
  }

  @Post('permissions')
  createPermission(@Body() body: { code: string; name?: string; parent_id?: number | null; }) {
    return this.rbac.createPermission(body);
  }

  @Post('roles/:id/permissions')
  assignPermsToRole(
    @Param('id', ParseIntPipe) id: number,
    @Body() body: { permission_ids: number[] },
  ) {
    return this.rbac.assignPermissionsToRole(id, body.permission_ids);
  }

  @Post('users/:id/roles')
  assignRolesToUser(
    @Param('id', ParseIntPipe) id: number,
    @Body() body: { role_ids: number[] },
  ) {
    return this.rbac.assignRolesToUser(id, body.role_ids);
  }

  @Post('users/:id/permissions')
  assignPermsToUser(
    @Param('id', ParseIntPipe) id: number,
    @Body() body: { permission_ids: number[] },
  ) {
    return this.rbac.assignPermissionsToUser(id, body.permission_ids);
  }
}


