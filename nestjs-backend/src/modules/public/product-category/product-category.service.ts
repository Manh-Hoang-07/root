import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { ProductCategory } from '../../../shared/entities/product-category.entity';
import { BaseService } from '../../../common/base/base-public.service';

@Injectable()
export class ProductCategoryService extends BaseService<ProductCategory> {
  constructor(
    @InjectRepository(ProductCategory)
    categoryRepository: Repository<ProductCategory>,
  ) {
    super(categoryRepository);
  }

  protected getAvailableRelations(): string[] {
    return [];
  }

  // Get simple list
  async getProductCategories() {
    return this.getSimpleList(
      { status: 'active' } as any,
      { name: 'ASC' } as any,
    );
  }

  async getProductCategory(id: string) {
    return this.getOne(id);
  }

  async getOne(id: string) {
    return this.repository.findOne({
      where: { 
        id: Number(id),
        status: 'active'
      } as any,
    });
  }
}

