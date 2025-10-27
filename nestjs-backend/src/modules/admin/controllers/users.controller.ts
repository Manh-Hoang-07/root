import { Controller, Get, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { UsersService } from '../services/users.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/users')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class UsersController {
  constructor(private readonly usersService: UsersService) {}

  @Get()
  async getUsers(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.usersService.getUsers(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getUser(@Param('id') id: string) {
    const result = await this.usersService.getUser(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'User not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Put(':id')
  async updateUser(@Param('id') id: string, @Body() updateUserDto: any) {
    const result = await this.usersService.updateUser(id, updateUserDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'User not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deleteUser(@Param('id') id: string) {
    const result = await this.usersService.deleteUser(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'User not found',
        data: null,
      };
    }

    return { message: 'User deleted successfully' };
  }
}
