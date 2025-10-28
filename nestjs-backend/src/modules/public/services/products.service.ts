import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, SelectQueryBuilder } from 'typeorm';
import { Product } from '../../../entities/product.entity';
import { BaseService } from './base.service';

@Injectable()
export class ProductsService extends BaseService<Product> {
  constructor(
    @InjectRepository(Product)
    productRepository: Repository<Product>,
  ) {
    super(productRepository);
  }

  protected getAvailableRelations(): string[] {
    return ['categories', 'variants', 'createdUser'];
  }

  protected getIndexRelations(): string[] {
    return [];
  }

  protected getShowRelations(): string[] {
    return ['categories', 'variants'];
  }

  protected buildBaseQuery(filters: any = {}): SelectQueryBuilder<Product> {
    const queryBuilder = super.buildBaseQuery(filters);
    queryBuilder.where('entity.status = :status', { status: 'published' });
    
    if (filters.category) {
      queryBuilder.andWhere('entity.category_id = :category', { category: filters.category });
    }

    return queryBuilder;
  }

  async getProducts(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.getAll(filters, perPage, page);
  }

  async getProduct(id: string) {
    const product = await this.getOne(id);
    if (product) {
      await this.incrementViewCount(product);
    }
    return product;
  }

  async getProductBySlug(slug: string) {
    const product = await this.getBySlug(slug);
    if (product) {
      await this.incrementViewCount(product);
    }
    return product;
  }
}
