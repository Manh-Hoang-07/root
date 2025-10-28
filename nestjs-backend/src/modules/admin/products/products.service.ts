import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Product } from '../../../shared/entities/product.entity';
import { BaseService } from '../../../common/base/base.service';

@Injectable()
export class ProductsService extends BaseService<Product> {
  constructor(
    @InjectRepository(Product)
    productRepository: Repository<Product>,
  ) {
    super(productRepository, 'Product');
  }

  // Override buildBaseQuery for admin - no status filter
  protected buildBaseQuery(filters: any = {}): any {
    const where: any = {};
    
    if (filters.search) {
      where.name = ILike(`%${filters.search}%`);
    }
    
    if (filters.status) {
      where.status = filters.status;
    }
    
    return where;
  }

  // Admin API methods
  async getProducts(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.getAll(filters, perPage, page);
  }

  async getProduct(id: string) {
    return this.getOne(id);
  }

  async createProduct(createProductDto: any) {
    return this.create(createProductDto);
  }

  async updateProduct(id: string, updateProductDto: any) {
    return this.update(Number(id), updateProductDto);
  }

  async deleteProduct(id: string) {
    return this.remove(Number(id));
  }
}
