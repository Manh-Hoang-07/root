import { Controller, Get, Post, Put, Delete, Body, Param, ParseIntPipe } from '@nestjs/common';
import { RbacService } from '../services/rbac.service';

@Controller('admin/users')
export class UserPermissionController {
  constructor(private readonly service: RbacService) {}

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

  // Đã xóa các endpoint liên quan đến direct permissions
  // Phân quyền cho user chỉ được thực hiện qua roles, không phân trực tiếp permissions

  /**
   * Lấy thông tin phân quyền của user
   */
  @Get(':id/permissions')
  async getUserPermissions(@Param('id', ParseIntPipe) userId: number) {
    return this.service.getUserPermissions(userId);
  }
}



