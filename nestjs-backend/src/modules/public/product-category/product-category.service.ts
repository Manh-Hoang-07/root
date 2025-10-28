import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { ProductCategory } from '../../../entities/product-category.entity';
import { BaseService } from '../base.service';

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
  async list() {
    return this.getSimpleList(
      { status: 'active' } as any,
      { name: 'ASC' } as any,
    );
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

