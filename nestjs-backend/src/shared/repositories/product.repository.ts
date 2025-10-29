import { Injectable } from '@nestjs/common';
import { DataSource, FindOptionsWhere } from 'typeorm';
import { BaseRepository } from '../../common/base/repositories/base.repository';
import { Product, ProductStatus } from '../entities/product.entity';

@Injectable()
export class ProductRepository extends BaseRepository<Product> {
  constructor(dataSource: DataSource) {
    super(Product, dataSource);
  }

  /**
   * Find product by slug
   */
  async findBySlug(slug: string): Promise<Product | null> {
    return this.findOne({
      where: { slug, deletedAt: null } as FindOptionsWhere<Product>,
    });
  }

  /**
   * Find product by SKU
   */
  async findBySku(sku: string): Promise<Product | null> {
    return this.findOne({
      where: { sku, deletedAt: null } as FindOptionsWhere<Product>,
    });
  }

  /**
   * Find active products with pagination
   */
  async findActiveProducts(
    page: number = 1,
    limit: number = 10,
    filters?: {
      category?: string;
      tag?: string;
      brand?: string;
      minPrice?: number;
      maxPrice?: number;
      searchTerm?: string;
      isFeatured?: boolean;
      inStock?: boolean;
    }
  ) {
    const query = this.createQueryBuilder('product')
      .where('product.deletedAt IS NULL')
      .andWhere('product.status = :status', { status: ProductStatus.ACTIVE });

    if (filters?.category) {
      query.andWhere('JSON_CONTAINS(product.categories, JSON_QUOTE(:category))', { 
        category: filters.category 
      });
    }

    if (filters?.tag) {
      query.andWhere('JSON_CONTAINS(product.tags, JSON_QUOTE(:tag))', { 
        tag: filters.tag 
      });
    }

    if (filters?.brand) {
      query.andWhere('product.brand = :brand', { brand: filters.brand });
    }

    if (filters?.minPrice !== undefined) {
      query.andWhere('product.price >= :minPrice', { minPrice: filters.minPrice });
    }

    if (filters?.maxPrice !== undefined) {
      query.andWhere('product.price <= :maxPrice', { maxPrice: filters.maxPrice });
    }

    if (filters?.searchTerm) {
      query.andWhere(
        '(product.name LIKE :searchTerm OR product.shortDescription LIKE :searchTerm OR product.description LIKE :searchTerm)',
        { searchTerm: `%${filters.searchTerm}%` }
      );
    }

    if (filters?.isFeatured !== undefined) {
      query.andWhere('product.isFeatured = :isFeatured', { isFeatured: filters.isFeatured });
    }

    if (filters?.inStock !== undefined && filters.inStock) {
      query.andWhere('(product.trackStock = false OR product.stock > 0 OR product.allowBackorder = true)');
    }

    const [products, total] = await query
      .skip((page - 1) * limit)
      .take(limit)
      .orderBy('product.sortOrder', 'ASC')
      .addOrderBy('product.createdAt', 'DESC')
      .getManyAndCount();

    return { data: products, total, page, limit };
  }

  /**
   * Find featured products
   */
  async findFeaturedProducts(limit: number = 10): Promise<Product[]> {
    return this.createQueryBuilder('product')
      .where('product.deletedAt IS NULL')
      .andWhere('product.status = :status', { status: ProductStatus.ACTIVE })
      .andWhere('product.isFeatured = :isFeatured', { isFeatured: true })
      .orderBy('product.sortOrder', 'ASC')
      .addOrderBy('product.createdAt', 'DESC')
      .take(limit)
      .getMany();
  }

  /**
   * Find products on sale
   */
  async findSaleProducts(limit?: number): Promise<Product[]> {
    const query = this.createQueryBuilder('product')
      .where('product.deletedAt IS NULL')
      .andWhere('product.status = :status', { status: ProductStatus.ACTIVE })
      .andWhere('product.comparePrice > product.price')
      .orderBy('((product.comparePrice - product.price) / product.comparePrice)', 'DESC'); // Order by discount percentage

    if (limit) {
      query.take(limit);
    }

    return query.getMany();
  }

  /**
   * Find low stock products
   */
  async findLowStockProducts(): Promise<Product[]> {
    return this.createQueryBuilder('product')
      .where('product.deletedAt IS NULL')
      .andWhere('product.trackStock = :trackStock', { trackStock: true })
      .andWhere('product.stock <= product.lowStockThreshold')
      .andWhere('product.lowStockThreshold IS NOT NULL')
      .orderBy('product.stock', 'ASC')
      .getMany();
  }

  /**
   * Find out of stock products
   */
  async findOutOfStockProducts(): Promise<Product[]> {
    return this.createQueryBuilder('product')
      .where('product.deletedAt IS NULL')
      .andWhere('product.trackStock = :trackStock', { trackStock: true })
      .andWhere('product.stock <= 0')
      .andWhere('product.allowBackorder = :allowBackorder', { allowBackorder: false })
      .orderBy('product.updatedAt', 'DESC')
      .getMany();
  }

  /**
   * Find related products by category and tags
   */
  async findRelatedProducts(productId: string, limit: number = 5): Promise<Product[]> {
    const product = await this.findById(productId);
    if (!product || !product.categories?.length) {
      return [];
    }

    const query = this.createQueryBuilder('product')
      .where('product.deletedAt IS NULL')
      .andWhere('product.status = :status', { status: ProductStatus.ACTIVE })
      .andWhere('product.id != :productId', { productId });

    // Find products with matching categories
    const categoryConditions = product.categories.map((category, index) => 
      `JSON_CONTAINS(product.categories, JSON_QUOTE(:category${index}))`
    ).join(' OR ');

    if (categoryConditions) {
      query.andWhere(`(${categoryConditions})`);
      
      product.categories.forEach((category, index) => {
        query.setParameter(`category${index}`, category);
      });
    }

    return query
      .orderBy('RAND()') // Random order for variety
      .take(limit)
      .getMany();
  }

  /**
   * Get all unique categories
   */
  async getAllCategories(): Promise<string[]> {
    const result = await this.createQueryBuilder('product')
      .select('DISTINCT JSON_UNQUOTE(JSON_EXTRACT(product.categories, "$[*]"))', 'categories')
      .where('product.deletedAt IS NULL')
      .andWhere('product.categories IS NOT NULL')
      .getRawMany();

    const categories = new Set<string>();
    result.forEach(row => {
      if (row.categories) {
        try {
          const categoryArray = JSON.parse(`[${row.categories}]`);
          categoryArray.forEach((category: string) => categories.add(category));
        } catch {
          // Handle single category case
          categories.add(row.categories);
        }
      }
    });

    return Array.from(categories).sort();
  }

  /**
   * Get all unique tags
   */
  async getAllTags(): Promise<string[]> {
    const result = await this.createQueryBuilder('product')
      .select('DISTINCT JSON_UNQUOTE(JSON_EXTRACT(product.tags, "$[*]"))', 'tags')
      .where('product.deletedAt IS NULL')
      .andWhere('product.tags IS NOT NULL')
      .getRawMany();

    const tags = new Set<string>();
    result.forEach(row => {
      if (row.tags) {
        try {
          const tagArray = JSON.parse(`[${row.tags}]`);
          tagArray.forEach((tag: string) => tags.add(tag));
        } catch {
          // Handle single tag case
          tags.add(row.tags);
        }
      }
    });

    return Array.from(tags).sort();
  }

  /**
   * Get all unique brands
   */
  async getAllBrands(): Promise<string[]> {
    const result = await this.createQueryBuilder('product')
      .select('DISTINCT product.brand', 'brand')
      .where('product.deletedAt IS NULL')
      .andWhere('product.brand IS NOT NULL')
      .orderBy('product.brand', 'ASC')
      .getRawMany();

    return result.map(row => row.brand);
  }

  /**
   * Get product statistics
   */
  async getProductStatistics(): Promise<{
    total: number;
    active: number;
    inactive: number;
    outOfStock: number;
    lowStock: number;
    featured: number;
    onSale: number;
  }> {
    const query = this.createQueryBuilder('product')
      .where('product.deletedAt IS NULL');

    const [
      total,
      active,
      inactive,
      outOfStock,
      lowStock,
      featured,
      onSale,
    ] = await Promise.all([
      query.getCount(),
      query.clone().andWhere('product.status = :status', { status: ProductStatus.ACTIVE }).getCount(),
      query.clone().andWhere('product.status = :status', { status: ProductStatus.INACTIVE }).getCount(),
      query.clone()
        .andWhere('product.trackStock = :trackStock', { trackStock: true })
        .andWhere('product.stock <= 0')
        .andWhere('product.allowBackorder = :allowBackorder', { allowBackorder: false })
        .getCount(),
      query.clone()
        .andWhere('product.trackStock = :trackStock', { trackStock: true })
        .andWhere('product.stock <= product.lowStockThreshold')
        .andWhere('product.lowStockThreshold IS NOT NULL')
        .getCount(),
      query.clone().andWhere('product.isFeatured = :isFeatured', { isFeatured: true }).getCount(),
      query.clone().andWhere('product.comparePrice > product.price').getCount(),
    ]);

    return {
      total,
      active,
      inactive,
      outOfStock,
      lowStock,
      featured,
      onSale,
    };
  }

  /**
   * Update stock for multiple products
   */
  async updateMultipleProductStock(updates: { id: string; stock: number }[]): Promise<void> {
    const queryRunner = this.manager.connection.createQueryRunner();
    await queryRunner.connect();
    await queryRunner.startTransaction();

    try {
      for (const update of updates) {
        await queryRunner.manager.update(Product, update.id, { stock: update.stock });
      }
      await queryRunner.commitTransaction();
    } catch (error) {
      await queryRunner.rollbackTransaction();
      throw error;
    } finally {
      await queryRunner.release();
    }
  }

  /**
   * Bulk update product prices
   */
  async bulkUpdatePrices(updates: { id: string; price: number; comparePrice?: number }[]): Promise<void> {
    const queryRunner = this.manager.connection.createQueryRunner();
    await queryRunner.connect();
    await queryRunner.startTransaction();

    try {
      for (const update of updates) {
        const updateData: any = { price: update.price };
        if (update.comparePrice !== undefined) {
          updateData.comparePrice = update.comparePrice;
        }
        await queryRunner.manager.update(Product, update.id, updateData);
      }
      await queryRunner.commitTransaction();
    } catch (error) {
      await queryRunner.rollbackTransaction();
      throw error;
    } finally {
      await queryRunner.release();
    }
  }
}
