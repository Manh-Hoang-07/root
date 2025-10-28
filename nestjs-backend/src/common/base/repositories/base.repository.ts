import { Repository, FindManyOptions, FindOptionsWhere, SelectQueryBuilder } from 'typeorm';
import { BaseEntity } from '../entities/base.entity';
import { PaginationDto, PaginationUtils } from '../../../shared/dto/pagination.dto';
import { SortOptions, FilterOptions } from '../../../typings/api-response.interface';

/**
 * Base Repository với các phương thức chung
 * Extends TypeORM Repository và thêm các tính năng:
 * - Pagination
 * - Search
 * - Soft delete filtering
 * - Active/Deleted entities
 */
export class BaseRepository<T extends BaseEntity> extends Repository<T> {
  /**
   * Tìm tất cả entities với phân trang
   */
  async findWithPagination(
    paginationDto: PaginationDto,
    options?: FindManyOptions<T>,
  ): Promise<{ data: T[]; total: number; page: number; limit: number }> {
    const { page = 1, limit = 10, search, sortBy, sortOrder } = paginationDto;

    PaginationUtils.validatePagination(page, limit);

    const queryBuilder = this.createQueryBuilder('entity');

    // Apply where conditions
    if (options?.where) {
      this.applyWhereConditions(queryBuilder, options.where);
    }

    // Apply search
    if (search) {
      this.applySearch(queryBuilder, search);
    }

    // Apply sorting
    if (sortBy) {
      const order = (sortOrder || 'DESC').toUpperCase() as 'ASC' | 'DESC';
      queryBuilder.orderBy(`entity.${sortBy}`, order);
    } else {
      queryBuilder.orderBy('entity.createdAt', 'DESC');
    }

    // Apply pagination
    queryBuilder.skip((page - 1) * limit).take(limit);

    // Execute query
    const [data, total] = await queryBuilder.getManyAndCount();

    return {
      data,
      total,
      page,
      limit,
    };
  }

  /**
   * Tìm tất cả entities đang hoạt động (không bị xóa mềm)
   */
  async findActive(options?: FindManyOptions<T>): Promise<T[]> {
    return this.find({
      ...options,
      withDeleted: false,
    });
  }

  /**
   * Tìm tất cả entities đã bị xóa mềm
   */
  async findDeleted(options?: FindManyOptions<T>): Promise<T[]> {
    return this.find({
      ...options,
      withDeleted: true,
    });
  }

  /**
   * Tìm một entity đang hoạt động theo ID
   */
  async findActiveById(id: string): Promise<T | null> {
    return this.findOne({
      where: { id } as FindOptionsWhere<T>,
      withDeleted: false,
    });
  }

  /**
   * Tìm một entity đã bị xóa mềm theo ID
   */
  async findDeletedById(id: string): Promise<T | null> {
    return this.findOne({
      where: { id } as FindOptionsWhere<T>,
      withDeleted: true,
    });
  }

  /**
   * Đếm số lượng entities đang hoạt động
   */
  async countActive(options?: FindManyOptions<T>): Promise<number> {
    return this.count({
      ...options,
      withDeleted: false,
    });
  }

  /**
   * Đếm số lượng entities đã bị xóa mềm
   */
  async countDeleted(options?: FindManyOptions<T>): Promise<number> {
    return this.count({
      ...options,
      withDeleted: true,
    });
  }

  /**
   * Tìm kiếm entities với từ khóa
   */
  async search(
    keyword: string,
    fields: string[] = ['name', 'description'],
    limit: number = 10,
  ): Promise<T[]> {
    const queryBuilder = this.createQueryBuilder('entity');

    // Build search conditions
    const searchConditions = fields
      .map((field, index) => {
        const paramName = `search_${index}`;
        return `entity.${field} LIKE :${paramName}`;
      })
      .join(' OR ');

    const searchParams = fields.reduce((acc, field, index) => {
      acc[`search_${index}`] = `%${keyword}%`;
      return acc;
    }, {} as Record<string, string>);

    queryBuilder
      .where(`(${searchConditions})`, searchParams)
      .andWhere('entity.deletedAt IS NULL')
      .orderBy('entity.createdAt', 'DESC')
      .limit(limit);

    return queryBuilder.getMany();
  }

  /**
   * Áp dụng điều kiện where vào query builder
   */
  private applyWhereConditions(
    queryBuilder: SelectQueryBuilder<T>,
    where: FindOptionsWhere<T> | FindOptionsWhere<T>[],
  ): void {
    if (Array.isArray(where)) {
      where.forEach((condition, index) => {
        const conditions = Object.keys(condition)
          .map((key) => `entity.${key} = :${key}_${index}`)
          .join(' AND ');
        if (index === 0) {
          queryBuilder.andWhere(`(${conditions})`, condition);
        } else {
          queryBuilder.orWhere(`(${conditions})`, condition);
        }
      });
    } else {
      Object.entries(where).forEach(([key, value]) => {
        queryBuilder.andWhere(`entity.${key} = :${key}`, { [key]: value });
      });
    }
  }

  /**
   * Áp dụng tìm kiếm vào query builder
   */
  private applySearch(
    queryBuilder: SelectQueryBuilder<T>,
    searchTerm: string,
  ): void {
    // Override method này trong repository con để chỉ định các trường tìm kiếm
    const searchFields = this.getSearchFields();
    
    if (searchFields.length === 0) return;

    const searchConditions = searchFields
      .map((field, index) => {
        const paramName = `search_${index}`;
        return `entity.${field} LIKE :${paramName}`;
      })
      .join(' OR ');

    const searchParams = searchFields.reduce((acc, field, index) => {
      acc[`search_${index}`] = `%${searchTerm}%`;
      return acc;
    }, {} as Record<string, string>);

    queryBuilder.andWhere(`(${searchConditions})`, searchParams);
  }

  /**
   * Lấy các trường tìm kiếm mặc định
   * Override method này trong repository con
   */
  protected getSearchFields(): string[] {
    return [];
  }
}

