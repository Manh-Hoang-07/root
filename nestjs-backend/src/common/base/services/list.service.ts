import { Injectable } from '@nestjs/common';
import { Repository, FindManyOptions, FindOptionsWhere, SelectQueryBuilder } from 'typeorm';
import { BaseEntity } from '../entities/base.entity';
import { PaginationDto, PaginatedResultDto, PaginationUtils } from '../../../shared/dto/pagination.dto';
import { QueryOptions, SortOptions, FilterOptions } from '../../../typings/api-response.interface';
import { Filters, Options, FindAllOptions, PaginatedListResult } from '../interfaces/list.interface';


/**
 * List Service
 * Cung cấp các phương thức cơ bản cho việc lấy danh sách với phân trang, sắp xếp, lọc và tìm kiếm
 */
@Injectable()
export abstract class ListService<T extends BaseEntity> {
  constructor(protected readonly repository: Repository<T>) {}

  /**
   * Lấy danh sách với phân trang (Method chính - TỐI GIẢN)
   * 
   * @param filters - Điều kiện tìm kiếm (conditions): object key-value hoặc FilterOptions[]
   * @param options - Các tùy chọn: pagination, sorting, relations, search, etc.
   * 
   * Có thể truyền:
   * - findAll(filters, options) - Tách riêng
   * - findAll({ filters, ...options }) - Gộp lại
   * - findAll(paginationDto) - Từ query params
   * - findAll(filters) - Chỉ filters
   */
  async findAll(
    filters?: Filters<T> | PaginationDto | Options | FindAllOptions<T>,
    options?: Options,
  ): Promise<PaginatedListResult<T>> {
    // Nếu chỉ có 1 tham số
    if (options === undefined) {
      // Nếu là PaginationDto (có page/limit/search/sortBy)
      if (filters && typeof filters === 'object' && ('page' in filters || 'limit' in filters || 'search' in filters || 'sortBy' in filters)) {
        return this.executeQuery(filters as PaginationDto | Options, undefined);
      }

      // Nếu là Options (có page/limit/sortBy nhưng không phải PaginationDto)
      if (filters && typeof filters === 'object' && ('page' in filters || 'limit' in filters || 'relations' in filters || 'sortBy' in filters)) {
        return this.executeQuery(undefined, filters as Options);
      }

      // Còn lại -> là filters
      return this.executeQuery(filters as Filters<T>, undefined);
    }

    // Có 2 tham số: filters và options
    return this.executeQuery(filters as Filters<T>, options);
  }

  /**
   * Thực thi query - xử lý tất cả các trường hợp (Internal)
   */
  private async executeQuery(
    filters?: Filters<T> | PaginationDto,
    options?: Options,
  ): Promise<PaginatedListResult<T>> {
    // Nếu filters là PaginationDto -> merge vào options
    let finalFilters: Filters<T> | undefined;
    let finalOptions: Options;

    if (filters && typeof filters === 'object' && ('page' in filters || 'limit' in filters || 'search' in filters || 'sortBy' in filters)) {
      // filters thực ra là PaginationDto hoặc Options
      const paginationDto = filters as PaginationDto;
      finalOptions = {
        page: paginationDto.page || options?.page || 1,
        limit: paginationDto.limit || options?.limit || 10,
        search: paginationDto.search || options?.search,
        sortBy: paginationDto.sortBy || options?.sortBy,
        sortOrder: paginationDto.sortOrder || options?.sortOrder,
        relations: options?.relations,
        searchFields: options?.searchFields,
        sort: options?.sort,
        select: options?.select,
        includeDeleted: options?.includeDeleted || false,
      };
      finalFilters = undefined;
    } else {
      // filters là Filters thực sự
      finalFilters = filters as Filters<T> | undefined;
      finalOptions = options || {};
    }

    // Chuẩn hóa options (có thể từ FindAllOptions với filters bên trong)
    if ((filters as any)?.filters) {
      finalFilters = (filters as any).filters;
      finalOptions = { ...(filters as any), ...options };
      delete (finalOptions as any).filters;
    }

    // Set defaults
    finalOptions = {
      page: finalOptions?.page || 1,
      limit: finalOptions?.limit || 10,
      search: finalOptions?.search,
      searchFields: finalOptions?.searchFields,
      sortBy: finalOptions?.sortBy,
      sortOrder: finalOptions?.sortOrder,
      sort: finalOptions?.sort,
      relations: finalOptions?.relations,
      select: finalOptions?.select,
      includeDeleted: finalOptions?.includeDeleted || false,
    };

    const { page, limit } = finalOptions;
    PaginationUtils.validatePagination(page!, limit!);

    // Build query
    const queryBuilder = this.buildQueryFromOptions(finalFilters, finalOptions);

    // Apply sorting
    this.applySortingFromOptions(queryBuilder, finalOptions);

    // Apply pagination
    queryBuilder.skip((page! - 1) * limit!).take(limit!);

    // Execute
    const [data, totalItems] = await queryBuilder.getManyAndCount();
    return this.buildPaginatedResult(data, page!, limit!, totalItems);
  }

  /**
   * Xây dựng query từ filters và options (Internal)
   */
  private buildQueryFromOptions(
    filters?: Filters<T>,
    options?: Options,
  ): SelectQueryBuilder<T> {
    const {
      search,
      searchFields = [],
      relations = [],
      select,
      includeDeleted = false,
    } = options || {};

    // Relations
    const finalRelations = relations.length > 0 
      ? relations 
      : this.getDefaultRelations();

    const queryBuilder = this.buildBaseQuery(finalRelations, includeDeleted);

    // Apply filters (hỗ trợ nhiều format)
    if (filters) {
      this.applyFiltersFromOptions(queryBuilder, filters);
    }

    // Apply search
    if (search) {
      const finalSearchFields = searchFields.length > 0
        ? searchFields
        : this.getDefaultSearchFields().map(f => String(f));
      
      if (finalSearchFields.length > 0) {
        this.applySearch(queryBuilder, search, finalSearchFields as (keyof T)[]);
      }
    }

    // Apply select
    if (select && select.length > 0) {
      queryBuilder.select(select.map((field) => `entity.${field}`));
    }

    return queryBuilder;
  }

  /**
   * Áp dụng filters (hỗ trợ nhiều format)
   */
  private applyFiltersFromOptions(
    queryBuilder: SelectQueryBuilder<T>,
    filters: Filters<T>,
  ): void {
    // Nếu là FilterOptions[] (nâng cao)
    if (Array.isArray(filters) && filters.length > 0 && 'field' in filters[0] && 'operator' in filters[0]) {
      this.applyFilters(queryBuilder, filters as FilterOptions[]);
      return;
    }

    // Nếu là FindOptionsWhere[] (array)
    if (Array.isArray(filters)) {
      this.applyWhereConditions(queryBuilder, filters as FindOptionsWhere<T>[]);
      return;
    }

    // Nếu là object đơn giản (key-value)
    if (typeof filters === 'object' && filters !== null) {
      // Convert object thành FilterOptions với operator 'eq' mặc định
      const filterOptions: FilterOptions[] = Object.entries(filters)
        .filter(([_, value]) => value !== undefined && value !== null)
        .map(([field, value]) => ({
          field,
          operator: 'eq' as const,
          value,
        }));
      
      if (filterOptions.length > 0) {
        this.applyFilters(queryBuilder, filterOptions);
      }
    }
  }

  /**
   * Áp dụng sorting từ options
   */
  private applySortingFromOptions(
    queryBuilder: SelectQueryBuilder<T>,
    options?: Options,
  ): void {
    const { sortBy, sortOrder, sort } = options || {};

    // Nếu có sort array (nâng cao)
    if (sort && sort.length > 0) {
      this.applySorting(queryBuilder, sort);
      return;
    }

    // Nếu có sortBy đơn giản
    if (sortBy) {
      const order = (sortOrder || 'DESC').toUpperCase() as 'ASC' | 'DESC';
      queryBuilder.orderBy(`entity.${sortBy}`, order);
      return;
    }

    // Default: createdAt DESC
    queryBuilder.orderBy('entity.createdAt', 'DESC');
  }

  /**
   * Tìm kiếm đơn giản (chỉ cần keyword)
   */
  async search(
    keyword: string,
    limit: number = 10,
  ): Promise<T[]> {
    const result = await this.findAll(undefined, { search: keyword, limit });
    return result.data;
  }

  /**
   * Đếm số lượng bản ghi (tối giản)
   */
  async count(
    filters?: Filters<T>,
  ): Promise<number> {
    const queryBuilder = this.repository.createQueryBuilder('entity');
    queryBuilder.where('entity.deletedAt IS NULL');

    if (filters) {
      this.applyFiltersFromOptions(queryBuilder, filters);
    }

    return queryBuilder.getCount();
  }

  /**
   * Áp dụng điều kiện where vào query builder (Helper)
   */
  protected applyWhereConditions(
    queryBuilder: SelectQueryBuilder<T>,
    where: FindOptionsWhere<T> | FindOptionsWhere<T>[],
  ): void {
    if (Array.isArray(where)) {
      where.forEach((filter, index) => {
        const conditions = Object.keys(filter)
          .map((key) => `entity.${key} = :${key}_${index}`)
          .join(' AND ');
        if (index === 0) {
          queryBuilder.andWhere(`(${conditions})`, filter);
        } else {
          queryBuilder.orWhere(`(${conditions})`, filter);
        }
      });
    } else {
      Object.entries(where).forEach(([key, value]) => {
        queryBuilder.andWhere(`entity.${key} = :${key}`, { [key]: value });
      });
    }
  }

  /**
   * Tìm một bản ghi theo điều kiện (tự động dùng default relations)
   */
  async findOne(
    where: FindOptionsWhere<T> | FindOptionsWhere<T>[] | string,
  ): Promise<T | null> {
    // Nếu là string -> tìm theo ID
    if (typeof where === 'string') {
      return this.findById(where);
    }

    return this.repository.findOne({
      where,
      relations: this.getDefaultRelations(),
    });
  }

  /**
   * Tìm một bản ghi theo ID (tự động dùng default relations)
   */
  async findById(
    id: string,
  ): Promise<T | null> {
    return this.repository.findOne({
      where: { id } as FindOptionsWhere<T>,
      relations: this.getDefaultRelations(),
    });
  }

  /**
   * Tìm một bản ghi theo ID hoặc throw exception (tự động dùng default relations)
   */
  async findByIdOrFail(
    id: string,
  ): Promise<T> {
    const entity = await this.findById(id);
    
    if (!entity) {
      throw new Error(`Entity with ID ${id} not found`);
    }

    return entity;
  }

  /**
   * Xây dựng query builder cơ bản
   */
  protected buildBaseQuery(
    relations: string[] = [],
    includeDeleted: boolean = false,
  ): SelectQueryBuilder<T> {
    const queryBuilder = this.repository.createQueryBuilder('entity');

    // Add relations
    relations.forEach((relation) => {
      queryBuilder.leftJoinAndSelect(`entity.${relation}`, relation);
    });

    // Exclude soft deleted records by default
    if (!includeDeleted) {
      queryBuilder.where('entity.deletedAt IS NULL');
    }

    return queryBuilder;
  }

  /**
   * Xây dựng query với filters, search, relations
   */
  protected buildQuery(options: {
    filters?: FilterOptions[];
    search?: string;
    searchFields?: string[];
    relations?: string[];
    select?: string[];
    includeDeleted?: boolean;
  }): SelectQueryBuilder<T> {
    const {
      filters = [],
      search,
      searchFields = [],
      relations = [],
      select,
      includeDeleted = false,
    } = options;

    const queryBuilder = this.buildBaseQuery(relations, includeDeleted);

    // Apply filters
    this.applyFilters(queryBuilder, filters);

    // Apply search
    if (search && searchFields.length > 0) {
      this.applySearch(queryBuilder, search, searchFields as (keyof T)[]);
    }

    // Apply select
    if (select && select.length > 0) {
      queryBuilder.select(select.map((field) => `entity.${field}`));
    }

    return queryBuilder;
  }

  /**
   * Áp dụng filters vào query builder
   */
  protected applyFilters(
    queryBuilder: SelectQueryBuilder<T>,
    filters: FilterOptions[],
  ): void {
    filters.forEach((filter, index) => {
      const { field, operator, value } = filter;
      const paramName = `filter_${index}`;

      switch (operator) {
        case 'eq':
          queryBuilder.andWhere(`entity.${field} = :${paramName}`, { [paramName]: value });
          break;
        case 'ne':
          queryBuilder.andWhere(`entity.${field} != :${paramName}`, { [paramName]: value });
          break;
        case 'gt':
          queryBuilder.andWhere(`entity.${field} > :${paramName}`, { [paramName]: value });
          break;
        case 'gte':
          queryBuilder.andWhere(`entity.${field} >= :${paramName}`, { [paramName]: value });
          break;
        case 'lt':
          queryBuilder.andWhere(`entity.${field} < :${paramName}`, { [paramName]: value });
          break;
        case 'lte':
          queryBuilder.andWhere(`entity.${field} <= :${paramName}`, { [paramName]: value });
          break;
        case 'in':
          queryBuilder.andWhere(`entity.${field} IN (:...${paramName})`, { [paramName]: Array.isArray(value) ? value : [value] });
          break;
        case 'nin':
          queryBuilder.andWhere(`entity.${field} NOT IN (:...${paramName})`, { [paramName]: Array.isArray(value) ? value : [value] });
          break;
        case 'like':
          queryBuilder.andWhere(`entity.${field} LIKE :${paramName}`, { [paramName]: `%${value}%` });
          break;
        case 'between':
          if (Array.isArray(value) && value.length === 2) {
            queryBuilder.andWhere(
              `entity.${field} BETWEEN :${paramName}_start AND :${paramName}_end`,
              { [`${paramName}_start`]: value[0], [`${paramName}_end`]: value[1] },
            );
          }
          break;
      }
    });
  }

  /**
   * Áp dụng search vào query builder
   */
  protected applySearch(
    queryBuilder: SelectQueryBuilder<T>,
    searchTerm: string,
    searchFields: (keyof T)[],
  ): void {
    if (searchFields.length === 0) return;

    const searchConditions = searchFields
      .map((field, index) => {
        const paramName = `search_${index}`;
        return `entity.${String(field)} LIKE :${paramName}`;
      })
      .join(' OR ');

    const searchParams = searchFields.reduce((acc, field, index) => {
      acc[`search_${index}`] = `%${searchTerm}%`;
      return acc;
    }, {} as Record<string, string>);

    queryBuilder.andWhere(`(${searchConditions})`, searchParams);
  }

  /**
   * Áp dụng sorting vào query builder
   */
  protected applySorting(
    queryBuilder: SelectQueryBuilder<T>,
    sort: SortOptions[],
  ): void {
    if (sort.length === 0) {
      // Default sorting by createdAt DESC
      queryBuilder.orderBy('entity.createdAt', 'DESC');
      return;
    }

    sort.forEach((sortOption, index) => {
      const { field, direction = 'DESC' } = sortOption;
      if (index === 0) {
        queryBuilder.orderBy(`entity.${field}`, direction);
      } else {
        queryBuilder.addOrderBy(`entity.${field}`, direction);
      }
    });
  }

  /**
   * Xây dựng kết quả phân trang
   */
  protected buildPaginatedResult(
    data: T[],
    page: number,
    limit: number,
    totalItems: number,
  ): PaginatedListResult<T> {
    const totalPages = PaginationUtils.calculateTotalPages(totalItems, limit);
    const hasNextPage = page < totalPages;
    const hasPreviousPage = page > 1;

    return {
      data,
      meta: {
        page,
        limit,
        totalItems,
        totalPages,
        hasNextPage,
        hasPreviousPage,
        nextPage: hasNextPage ? page + 1 : undefined,
        previousPage: hasPreviousPage ? page - 1 : undefined,
      },
    };
  }

  /**
   * Lấy các trường mặc định để tìm kiếm
   * Override method này trong service con để chỉ định các trường tìm kiếm
   */
  protected getDefaultSearchFields(): (keyof T)[] {
    return [];
  }

  /**
   * Lấy các relations mặc định
   * Override method này trong service con để chỉ định các relations mặc định
   */
  protected getDefaultRelations(): string[] {
    return [];
  }
}

