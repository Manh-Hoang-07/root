import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { PermissionsService } from '../services/permissions.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/permissions')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class PermissionsController {
  constructor(private readonly permissionsService: PermissionsService) {}

  @Get()
  async getPermissions(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.permissionsService.getPermissions(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getPermission(@Param('id') id: string) {
    const result = await this.permissionsService.getPermission(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Permission not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post()
  async createPermission(@Body() createPermissionDto: any) {
    const result = await this.permissionsService.createPermission(createPermissionDto);
    return { data: result };
  }

  @Put(':id')
  async updatePermission(@Param('id') id: string, @Body() updatePermissionDto: any) {
    const result = await this.permissionsService.updatePermission(id, updatePermissionDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Permission not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deletePermission(@Param('id') id: string) {
    const result = await this.permissionsService.deletePermission(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Permission not found',
        data: null,
      };
    }

    return { message: 'Permission deleted successfully' };
  }
}
