import { Controller, Get, Post, Put, Delete, Body, Param, Query, ParseIntPipe } from '@nestjs/common';
import { User } from '../../../../common/decorators/user.decorator';
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
  async create(@Body() dto: any, @User('id') userId: number) {
    return this.service.create(dto, userId);
  }

  @Put(':id')
  async update(
    @Param('id', ParseIntPipe) id: number,
    @Body() dto: any,
    @User('id') userId: number
  ) {
    return this.service.update(id, dto, userId);
  }

  @Delete(':id')
  async delete(@Param('id', ParseIntPipe) id: number) {
    return this.service.delete(id);
  }
}



