import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { ProductsService } from '../services/products.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/products')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class ProductsController {
  constructor(private readonly productsService: ProductsService) {}

  @Get()
  async getProducts(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.productsService.getProducts(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getProduct(@Param('id') id: string) {
    const result = await this.productsService.getProduct(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Product not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post()
  async createProduct(@Body() createProductDto: any) {
    const result = await this.productsService.createProduct(createProductDto);
    return { data: result };
  }

  @Put(':id')
  async updateProduct(@Param('id') id: string, @Body() updateProductDto: any) {
    const result = await this.productsService.updateProduct(id, updateProductDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Product not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deleteProduct(@Param('id') id: string) {
    const result = await this.productsService.deleteProduct(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Product not found',
        data: null,
      };
    }

    return { message: 'Product deleted successfully' };
  }
}
