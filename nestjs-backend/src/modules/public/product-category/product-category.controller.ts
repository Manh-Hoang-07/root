import { Controller, Get, Param } from '@nestjs/common';
import { ProductCategoryService } from './product-category.service';

@Controller('public/product-categories')
export class ProductCategoryController {
  constructor(private readonly productCategoryService: ProductCategoryService) {}

  @Get()
  async list() {
    const data = await this.productCategoryService.getProductCategories();
    return { data };
  }

  @Get(':id')
  async get(@Param('id') id: string) {
    const data = await this.productCategoryService.getProductCategory(id);
    return { data };
  }
}
