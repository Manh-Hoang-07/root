import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { RolesService } from '../services/roles.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/roles')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class RolesController {
  constructor(private readonly rolesService: RolesService) {}

  @Get()
  async getRoles(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.rolesService.getRoles(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getRole(@Param('id') id: string) {
    const result = await this.rolesService.getRole(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Role not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post()
  async createRole(@Body() createRoleDto: any) {
    const result = await this.rolesService.createRole(createRoleDto);
    return { data: result };
  }

  @Put(':id')
  async updateRole(@Param('id') id: string, @Body() updateRoleDto: any) {
    const result = await this.rolesService.updateRole(id, updateRoleDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Role not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deleteRole(@Param('id') id: string) {
    const result = await this.rolesService.deleteRole(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Role not found',
        data: null,
      };
    }

    return { message: 'Role deleted successfully' };
  }
}
