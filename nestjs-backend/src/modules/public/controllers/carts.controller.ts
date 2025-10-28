import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { CartService } from '../cart/cart.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';

@Controller('public/carts')
@UseGuards(JwtAuthGuard)
export class CartsController {
  constructor(private readonly cartsService: CartService) {}

  @Get()
  async getCarts(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
  ) {
    return this.cartsService.getCarts(
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getCart(@Param('id') id: string) {
    const result = await this.cartsService.getCart(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Cart not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post()
  async createCart(@Body() createCartDto: any) {
    const result = await this.cartsService.createCart(createCartDto);
    return { data: result };
  }

  @Put(':id')
  async updateCart(@Param('id') id: string, @Body() updateCartDto: any) {
    const result = await this.cartsService.updateCart(id, updateCartDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Cart not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deleteCart(@Param('id') id: string) {
    const result = await this.cartsService.deleteCart(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Cart not found',
        data: null,
      };
    }

    return { message: 'Cart deleted successfully' };
  }
}
