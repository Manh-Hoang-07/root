import { Controller, Get, Put, Param, Body, Query, UseGuards } from '@nestjs/common';
import { OrdersService } from '../services/orders.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/orders')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class OrdersController {
  constructor(private readonly ordersService: OrdersService) {}

  @Get()
  async getOrders(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.ordersService.getOrders(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getOrder(@Param('id') id: string) {
    const result = await this.ordersService.getOrder(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Order not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Put(':id')
  async updateOrder(@Param('id') id: string, @Body() updateOrderDto: any) {
    const result = await this.ordersService.updateOrder(id, updateOrderDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Order not found',
        data: null,
      };
    }

    return { data: result };
  }
}
