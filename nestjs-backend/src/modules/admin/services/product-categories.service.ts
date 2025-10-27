import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';
import { ProductCategory } from '../../../entities/product-category.entity';

@Injectable()
export class ProductCategoriesService {
  constructor(
    @InjectRepository(ProductCategory)
    private readonly productCategoryRepository: Repository<ProductCategory>,
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

  // Product Categories
  async getProductCategories(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.list(this.productCategoryRepository, filters, perPage, page);
  }

  async getProductCategory(id: string) {
    return this.productCategoryRepository.findOne({
      where: { id: Number(id) } as any,
    });
  }

  async createProductCategory(createProductCategoryDto: any) {
    const productCategory = this.productCategoryRepository.create(createProductCategoryDto as any);
    return this.productCategoryRepository.save(productCategory);
  }

  async updateProductCategory(id: string, updateProductCategoryDto: any) {
    const existingProductCategory = await this.productCategoryRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingProductCategory) {
      return null;
    }

    const updatedProductCategory = this.productCategoryRepository.merge(existingProductCategory, updateProductCategoryDto as any);
    return this.productCategoryRepository.save(updatedProductCategory);
  }

  async deleteProductCategory(id: string) {
    const existingProductCategory = await this.productCategoryRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingProductCategory) {
      return null;
    }

    if (this.hasProperty(this.productCategoryRepository, 'deleted_at')) {
      (existingProductCategory as any).deleted_at = new Date();
      return this.productCategoryRepository.save(existingProductCategory);
    } else {
      return this.productCategoryRepository.remove(existingProductCategory);
    }
  }
}
