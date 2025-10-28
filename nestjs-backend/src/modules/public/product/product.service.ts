import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Like } from 'typeorm';
import { Product } from '../../../shared/entities/product.entity';
import { ProductStatus } from '../../../shared/enums/product-status.enum';

@Injectable()
export class ProductService {
  private readonly productRepo: Repository<Product>;

  constructor(
    @InjectRepository(Product)
    productRepository: Repository<Product>,
  ) {
    this.productRepo = productRepository;
  }

  // Metadata declarations
  protected getRelations(): string[] {
    return ['category', 'variants'];
  }

  protected getAvailableRelations(): string[] {
    return ['category', 'variants'];
  }

  protected getIndexRelations(): string[] {
    return ['category'];
  }

  protected getShowRelations(): string[] {
    return ['category', 'variants'];
  }

  // Override: Add status filter to base query
  protected buildBaseQuery(filters: any = {}): any {
    const where: any = { status: ProductStatus.ACTIVE };
    
    if (filters.search) {
      where.name = Like(`%${filters.search}%`);
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

  async list(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    const [items, total] = await this.productRepo.findAndCount({
      where: this.buildBaseQuery(filters),
      relations: relations.length > 0 ? relations : this.getIndexRelations(),
      take: perPage,
      skip: (page - 1) * perPage,
      order: { id: 'DESC' },
    });
    return {
      data: items,
      meta: {
        total,
        per_page: perPage,
        current_page: page,
        last_page: Math.ceil(total / perPage),
      },
    };
  }

  async get(id: string, relations: string[] = []) {
    const product = await this.getOne(id, relations.length > 0 ? relations : undefined);
    if (product) {
      await this.productRepo.update({ id: product.id }, { view_count: (product.view_count || 0) + 1 });
    }
    return product;
  }

  async getProductBySlug(slug: string, relations: string[] = []) {
    const product = await this.getBySlug(slug, relations.length > 0 ? relations : undefined);
    if (product) {
      await this.productRepo.update({ id: product.id }, { view_count: (product.view_count || 0) + 1 });
    }
    return product;
  }
}

