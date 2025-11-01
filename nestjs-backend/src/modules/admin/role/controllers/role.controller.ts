import { Controller, Get, Post, Put, Delete, Body, Param, Query, ParseIntPipe, Req } from '@nestjs/common';
import { RoleService } from '../services/role.service';

@Controller('admin/roles')
export class RoleController {
  constructor(private readonly service: RoleService) {}

  @Get()
  async getList(@Query() query: any) {
    return this.service.getList(query);
  }

  @Get(':id')
  async getOne(@Param('id', ParseIntPipe) id: number) {
    return this.service.getOne({ id } as any);
  }

  @Post()
  async create(@Body() dto: any, @Req() req: any) {
    const userId = req.user?.id || req.user?.sub;
    return this.service.create(dto, userId);
  }

  @Put(':id')
  async update(
    @Param('id', ParseIntPipe) id: number,
    @Body() dto: any,
    @Req() req: any
  ) {
    const userId = req.user?.id || req.user?.sub;
    return this.service.update(id, dto, userId);
  }

  @Delete(':id')
  async delete(@Param('id', ParseIntPipe) id: number) {
    return this.service.delete(id);
  }

  @Post(':id/permissions')
  async assignPermissions(
    @Param('id', ParseIntPipe) roleId: number,
    @Body() body: { permission_ids: number[] }
  ) {
    return this.service.assignPermissions(roleId, body.permission_ids || []);
  }

  @Get(':id/permissions')
  async getPermissions(@Param('id', ParseIntPipe) roleId: number) {
    const role = await this.service.getOne({ id: roleId } as any, {
      relations: ['permissions', 'parent', 'children'],
    });
    return role;
  }
}



