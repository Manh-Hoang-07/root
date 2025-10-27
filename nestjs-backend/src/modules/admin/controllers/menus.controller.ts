import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { MenusService } from '../services/menus.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/menus')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class MenusController {
  constructor(private readonly menusService: MenusService) {}

  @Get()
  async getMenus(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.menusService.getMenus(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getMenu(@Param('id') id: string) {
    const result = await this.menusService.getMenu(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Menu not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post()
  async createMenu(@Body() createMenuDto: any) {
    const result = await this.menusService.createMenu(createMenuDto);
    return { data: result };
  }

  @Put(':id')
  async updateMenu(@Param('id') id: string, @Body() updateMenuDto: any) {
    const result = await this.menusService.updateMenu(id, updateMenuDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Menu not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deleteMenu(@Param('id') id: string) {
    const result = await this.menusService.deleteMenu(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Menu not found',
        data: null,
      };
    }

    return { message: 'Menu deleted successfully' };
  }
}
