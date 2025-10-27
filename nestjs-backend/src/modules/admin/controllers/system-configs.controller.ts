import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { SystemConfigsService } from '../services/system-configs.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/system-configs')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class SystemConfigsController {
  constructor(private readonly systemConfigsService: SystemConfigsService) {}

  @Get()
  async getSystemConfigs(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.systemConfigsService.getSystemConfigs(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getSystemConfig(@Param('id') id: string) {
    const result = await this.systemConfigsService.getSystemConfig(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'System config not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post()
  async createSystemConfig(@Body() createSystemConfigDto: any) {
    const result = await this.systemConfigsService.createSystemConfig(createSystemConfigDto);
    return { data: result };
  }

  @Put(':id')
  async updateSystemConfig(@Param('id') id: string, @Body() updateSystemConfigDto: any) {
    const result = await this.systemConfigsService.updateSystemConfig(id, updateSystemConfigDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'System config not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deleteSystemConfig(@Param('id') id: string) {
    const result = await this.systemConfigsService.deleteSystemConfig(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'System config not found',
        data: null,
      };
    }

    return { message: 'System config deleted successfully' };
  }
}
