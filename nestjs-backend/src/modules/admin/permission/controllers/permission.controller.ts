import { Controller, Get, Post, Put, Delete, Body, Param, Query, ParseIntPipe, Req } from '@nestjs/common';
import { PermissionService } from '../services/permission.service';

@Controller('admin/permissions')
export class PermissionController {
  constructor(private readonly service: PermissionService) {}

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
}



