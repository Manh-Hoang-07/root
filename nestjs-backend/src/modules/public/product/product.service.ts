import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, ILike } from 'typeorm';
import { Product } from '../../../entities/product.entity';
import { ProductStatus } from '../../../enums/product-status.enum';
import { BaseService } from '../base.service';

@Injectable()
export class ProductService extends BaseService<Product> {
  private readonly productRepo: Repository<Product>;

  constructor(
    @InjectRepository(Product)
    productRepository: Repository<Product>,
  ) {
    super(productRepository);
    this.productRepo = productRepository;
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

  async getOne(id: string, relations?: string[]) {
    return this.productRepo.findOne({
      where: { 
        id: Number(id),
        status: ProductStatus.ACTIVE
      },
      relations: relations || this.getShowRelations(),
    });
  }

  async getBySlug(slug: string, relations?: string[]) {
    return this.productRepo.findOne({
      where: { 
        slug,
        status: ProductStatus.ACTIVE
      },
      relations: relations || this.getShowRelations(),
    });
  }

  // Public API methods
  async getAll(filters: any = {}, perPage: number = 20, page: number = 1, relations?: string[]) {
    return super.getAll(filters, perPage, page, relations);
  }

  async getProducts(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    return this.getAll(filters, perPage, page, relations.length > 0 ? relations : undefined);
  }

  async getProduct(id: string, relations: string[] = []) {
    const product = await this.getOne(id, relations.length > 0 ? relations : undefined);
    if (product) {
      await super.incrementViewCount(product);
    }
    return product;
  }

  async getProductBySlug(slug: string, relations: string[] = []) {
    const product = await this.getBySlug(slug, relations.length > 0 ? relations : undefined);
    if (product) {
      await super.incrementViewCount(product);
    }
    return product;
  }
}

