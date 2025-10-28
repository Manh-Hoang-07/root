import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, ILike } from 'typeorm';
import { Product } from '../../../shared/entities/product.entity';
import { ProductStatus } from '../../../shared/enums/product-status.enum';
import { BaseService } from '../../../common/base/base.service';

@Injectable()
export class ProductService extends BaseService<Product> {
  constructor(
    @InjectRepository(Product)
    productRepository: Repository<Product>,
  ) {
    super(productRepository, 'Product');
  }

  // Metadata declarations
  protected getRelations(): string[] {
    return ['categories', 'variants'];
  }

  protected getAvailableRelations(): string[] {
    return ['categories', 'variants', 'createdUser', 'updatedUser'];
  }

  protected getIndexRelations(): string[] {
    return ['categories'];
  }

  protected getShowRelations(): string[] {
    return ['categories', 'variants', 'createdUser'];
  }

  // Override: Add status filter to base query
  protected buildBaseQuery(filters: any = {}): any {
    const where: any = { status: ProductStatus.ACTIVE };
    
    if (filters.search) {
      where.name = ILike(`%${filters.search}%`);
    }
    
    if (filters.category) {
      where.category_id = filters.category;
    }
    
    return where;
  }

  // Public API methods
  async getProducts(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    return this.getAll(filters, perPage, page, relations.length > 0 ? relations : undefined);
  }

  async getProduct(id: string, relations: string[] = []) {
    const product = await this.getOne(id, relations.length > 0 ? relations : undefined);
    if (product) {
      await this.incrementViewCount(product);
    }
    return product;
  }

  async getProductBySlug(slug: string, relations: string[] = []) {
    const product = await this.getBySlug(slug, relations.length > 0 ? relations : undefined);
    if (product) {
      await this.incrementViewCount(product);
    }
    return product;
  }
}

