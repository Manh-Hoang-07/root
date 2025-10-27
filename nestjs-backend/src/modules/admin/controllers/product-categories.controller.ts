import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { ProductCategoriesService } from '../services/product-categories.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/product-categories')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class ProductCategoriesController {
  constructor(private readonly productCategoriesService: ProductCategoriesService) {}

  @Get()
  async getProductCategories(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.productCategoriesService.getProductCategories(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getProductCategory(@Param('id') id: string) {
    const result = await this.productCategoriesService.getProductCategory(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Product category not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post()
  async createProductCategory(@Body() createProductCategoryDto: any) {
    const result = await this.productCategoriesService.createProductCategory(createProductCategoryDto);
    return { data: result };
  }

  @Put(':id')
  async updateProductCategory(@Param('id') id: string, @Body() updateProductCategoryDto: any) {
    const result = await this.productCategoriesService.updateProductCategory(id, updateProductCategoryDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Product category not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deleteProductCategory(@Param('id') id: string) {
    const result = await this.productCategoriesService.deleteProductCategory(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Product category not found',
        data: null,
      };
    }

    return { message: 'Product category deleted successfully' };
  }
}
