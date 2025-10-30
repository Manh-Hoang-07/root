import { Injectable } from '@nestjs/common';
import { FindOptionsWhere, Repository } from 'typeorm';
import { Filters, Options, PaginatedListResult } from '../interfaces/list.interface';
import {
  applySelectColumns,
  applyRelations,
  applySorting,
  applyWhereConditions,
} from '../utils/list-query.helper';

/**
 * List Service với các phương thức cơ bản cho việc lấy danh sách
 */
@Injectable()
export abstract class ListService<T> {
  constructor(protected readonly repository: Repository<T>) {}

  /**
   * Lấy danh sách entities (phân trang, lọc, select, quan hệ, sắp xếp...)
   */
  async getList(
    filters?: Filters<T>,
    options?: Options,
  ): Promise<PaginatedListResult<T>> {
    const normalizedOptions = this.prepareOptions(options || {});
    const page = normalizedOptions.page || 1;
    const limit = normalizedOptions.limit || 10;
    const relations = normalizedOptions.relations || [];

    let data: T[] = [];
    let meta: PaginatedListResult<T>["meta"] = {
      page,
      limit,
      totalItems: 0,
      totalPages: 0,
      hasNextPage: false,
      hasPreviousPage: page > 1,
      nextPage: undefined,
      previousPage: page > 1 ? page - 1 : undefined,
    };
    const prepared = this.prepareFilters(filters, normalizedOptions);
    if (prepared) {
      const queryBuilder = this.repository.createQueryBuilder('entity');
      const whereFilters = prepared === true ? filters : prepared;
      if (whereFilters) {
        applyWhereConditions(queryBuilder, whereFilters);
      }
      applySelectColumns(queryBuilder, (normalizedOptions as any)?.select, this.repository);
      applyRelations(queryBuilder, relations as any);
      applySorting(queryBuilder, normalizedOptions?.sort, this.repository);
      queryBuilder.skip((page - 1) * limit).take(limit);
      const [rows, total] = await queryBuilder.getManyAndCount();
      data = rows;
      const totalPages = Math.ceil(total / limit);
      meta = {
        page,
        limit,
        totalItems: total,
        totalPages,
        hasNextPage: page < totalPages,
        hasPreviousPage: page > 1,
        nextPage: page < totalPages ? page + 1 : undefined,
        previousPage: page > 1 ? page - 1 : undefined,
      };
    }
    return { data, meta };
  }

  /**
   * Lấy một entity theo điều kiện với các option giống getList (relations, select, sort)
   */
  async getOne(
    where: FindOptionsWhere<T>,
    options?: Options,
  ): Promise<T | null> {
    if (!options || (!options.relations && !options.sort && !options.select)) {
      return this.repository.findOne({ where });
    }
    const alias = 'entity';
    const qb = this.repository.createQueryBuilder(alias);
    applyWhereConditions(qb, where as any);
    applySelectColumns(qb, options?.select);
    applyRelations(qb, (options?.relations || []) as any);
    applySorting(qb, options?.sort as any);
    qb.limit(1);
    return qb.getOne();
  }

  /**
   * Chuẩn hóa/merge filters trước khi build query
   * Override trong service con để thêm điều kiện mặc định hoặc chuyển đổi filters phức tạp
   */
  protected prepareFilters(
    filters?: Filters<T>,
    _options?: Options,
  ): boolean | any {
    return filters as any;
  }

  /**
   * Chuẩn bị options mặc định trước khi query list/getOne.
   * Có thể override tại service con để sinh options riêng module.
   */
  protected prepareOptions(queryOptions: any = {}) {
    const page = Number(queryOptions.page) || 1;
    const limit = Number(queryOptions.limit) || 10;
    const sort = queryOptions.sort;
    return {
      page,
      limit,
      relations: [],
      sort: sort || 'id:DESC',
    };
  }

}
