import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { ProductCategory } from '../../../shared/entities/product-category.entity';

@Injectable()
export class ProductCategoryService {
  constructor(
    @InjectRepository(ProductCategory)
    private readonly categoryRepository: Repository<ProductCategory>,
  ) {}

  async getProductCategories() {
    return this.categoryRepository.find({
      where: { status: true } as any,
      order: { name: 'ASC' },
    });
  }

  async getProductCategory(id: string) {
    return this.categoryRepository.findOne({
      where: { id: Number(id), status: true } as any,
    });
  }
}

