import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';
import { Product } from '../../../entities/product.entity';

@Injectable()
export class ProductsService {
  constructor(
    @InjectRepository(Product)
    private readonly productRepository: Repository<Product>,
  ) {}

  // Generic list method
  private async list<T>(
    repository: Repository<T>,
    filters: any = {},
    perPage: number = 20,
    page: number = 1,
  ) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const where: FindOptionsWhere<T> = {};

    if (filters.status) {
      (where as any)['status'] = filters.status;
    }

    if (filters.search) {
      const searchFields = ['name', 'title', 'email'];
      for (const field of searchFields) {
        if (this.hasProperty(repository, field)) {
          (where as any)[field] = ILike(`%${filters.search}%`);
          break;
        }
      }
    }

    const findOptions: FindManyOptions<T> = {
      where,
      order: { created_at: 'DESC' } as any,
      skip,
      take: validPerPage,
    };

    const [data, total] = await repository.findAndCount(findOptions);

    return {
      data,
      meta: {
        total,
        per_page: validPerPage,
        current_page: page,
        last_page: Math.ceil(total / validPerPage),
        from: skip + 1,
        to: Math.min(skip + validPerPage, total),
      },
    };
  }

  // Check if entity has a specific property
  private hasProperty<T>(repository: Repository<T>, property: string): boolean {
    const metadata = repository.metadata;
    return metadata.columns.some(column => column.propertyName === property);
  }

  // Products
  async getProducts(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.list(this.productRepository, filters, perPage, page);
  }

  async getProduct(id: string) {
    return this.productRepository.findOne({
      where: { id: Number(id) } as any,
    });
  }

  async createProduct(createProductDto: any) {
    const product = this.productRepository.create(createProductDto as any);
    return this.productRepository.save(product);
  }

  async updateProduct(id: string, updateProductDto: any) {
    const existingProduct = await this.productRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingProduct) {
      return null;
    }

    const updatedProduct = this.productRepository.merge(existingProduct, updateProductDto as any);
    return this.productRepository.save(updatedProduct);
  }

  async deleteProduct(id: string) {
    const existingProduct = await this.productRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingProduct) {
      return null;
    }

    if (this.hasProperty(this.productRepository, 'deleted_at')) {
      (existingProduct as any).deleted_at = new Date();
      return this.productRepository.save(existingProduct);
    } else {
      return this.productRepository.remove(existingProduct);
    }
  }
}
