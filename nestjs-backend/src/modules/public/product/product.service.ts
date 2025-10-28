import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Like } from 'typeorm';
import { Product } from '../../../shared/entities/product.entity';
import { ProductStatus } from '../../../shared/enums/product-status.enum';

@Injectable()
export class ProductService {
  private readonly productRepo: Repository<Product>;
  private readonly baseSelect = {
    id: true,
    name: true,
    slug: true,
  } as const;

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
    const where: any = {};
    
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
      select: this.baseSelect as any,
      relations: relations || this.getShowRelations(),
    });
  }

  async getBySlug(slug: string, relations?: string[]) {
    return this.productRepo.findOne({
      where: { 
        slug,
        status: ProductStatus.ACTIVE
      },
      select: this.baseSelect as any,
      relations: relations || this.getShowRelations(),
    });
  }

  async list(filters: any = {}, perPage: number = 20, page: number = 1, _relations: string[] = []) {
    try {
      const offset = (page - 1) * perPage;
      const clauses: string[] = [];
      const params: any[] = [];

      if (filters.search) {
        clauses.push('name LIKE ?');
        params.push(`%${filters.search}%`);
      }
      // NOTE: add more filters here when needed

      const whereSql = clauses.length ? `WHERE ${clauses.join(' AND ')}` : '';
      const rows = await this.productRepo.manager.query(
        `SELECT id, name, slug, sku, price, sale_price, image, status, created_at
         FROM products ${whereSql}
         ORDER BY id DESC
         LIMIT ? OFFSET ?`,
        [...params, perPage, offset],
      );
      const countRows = await this.productRepo.manager.query(
        `SELECT COUNT(*) as cnt FROM products ${whereSql}`,
        params,
      );
      const total = Number(countRows?.[0]?.cnt || 0);

      return {
        data: rows,
        meta: {
          total,
          per_page: perPage,
          current_page: page,
          last_page: Math.ceil(total / perPage),
        },
      } as any;
    } catch (_e) {
      return { data: [], meta: { total: 0, per_page: perPage, current_page: page, last_page: 0 } } as any;
    }
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

