import { Controller, Get, Post, Put, Delete, Body, Param, ParseIntPipe } from '@nestjs/common';
import { UserPermissionService } from '../services/user-permission.service';

@Controller('admin/users')
export class UserPermissionController {
  constructor(private readonly service: UserPermissionService) {}

  /**
   * Sync roles cho user (thay thế toàn bộ)
   */
  @Put(':id/roles')
  async syncRoles(
    @Param('id', ParseIntPipe) userId: number,
    @Body() body: { role_ids: number[] }
  ) {
    return this.service.syncRoles(userId, body.role_ids || []);
  }

  /**
   * Thêm roles cho user (append)
   */
  @Post(':id/roles')
  async addRoles(
    @Param('id', ParseIntPipe) userId: number,
    @Body() body: { role_ids: number[] }
  ) {
    return this.service.addRoles(userId, body.role_ids || []);
  }

  /**
   * Xóa roles khỏi user
   */
  @Delete(':id/roles')
  async removeRoles(
    @Param('id', ParseIntPipe) userId: number,
    @Body() body: { role_ids: number[] }
  ) {
    return this.service.removeRoles(userId, body.role_ids || []);
  }

  /**
   * Sync permissions cho user (thay thế toàn bộ)
   */
  @Put(':id/permissions')
  async syncPermissions(
    @Param('id', ParseIntPipe) userId: number,
    @Body() body: { permission_ids: number[] }
  ) {
    return this.service.syncPermissions(userId, body.permission_ids || []);
  }

  /**
   * Thêm permissions cho user (append)
   */
  @Post(':id/permissions')
  async addPermissions(
    @Param('id', ParseIntPipe) userId: number,
    @Body() body: { permission_ids: number[] }
  ) {
    return this.service.addPermissions(userId, body.permission_ids || []);
  }

  /**
   * Xóa permissions khỏi user
   */
  @Delete(':id/permissions')
  async removePermissions(
    @Param('id', ParseIntPipe) userId: number,
    @Body() body: { permission_ids: number[] }
  ) {
    return this.service.removePermissions(userId, body.permission_ids || []);
  }

  /**
   * Lấy thông tin phân quyền của user
   */
  @Get(':id/permissions')
  async getUserPermissions(@Param('id', ParseIntPipe) userId: number) {
    return this.service.getUserPermissions(userId);
  }
}



