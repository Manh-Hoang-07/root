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
      applyRelations(queryBuilder, relations as any);
      // enforce final selected columns after relations
      applySelectColumns(queryBuilder, (normalizedOptions as any)?.select, this.repository);
      applySorting(queryBuilder, normalizedOptions?.sort, this.repository);
      queryBuilder.skip((page - 1) * limit).take(limit);
      const [rows, total] = await queryBuilder.getManyAndCount();
      const selected = (normalizedOptions as any)?.select as string[] | undefined;
      if (Array.isArray(rows) && Array.isArray(selected) && selected.length > 0) {
        const primaryProps = this.repository.metadata.primaryColumns.map((c: any) => c.propertyName);
        const allowed = new Set<string>([...primaryProps, ...selected]);
        data = rows.map((row: any) => {
          const pruned: any = {};
          for (const key of allowed) {
            if (row[key] !== undefined) pruned[key] = row[key];
          }
          // keep loaded relation objects if any
          for (const key of Object.keys(row)) {
            const value = row[key];
            if (typeof value === 'object' && value !== null && !(key in pruned)) {
              pruned[key] = value;
            }
          }
          return pruned as T;
        });
      } else {
        data = rows;
      }
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
    applyRelations(qb, (options?.relations || []) as any);
    // enforce select after relations
    applySelectColumns(qb, options?.select, this.repository);
    applySorting(qb, options?.sort as any, this.repository);
    qb.limit(1);
    const one = await qb.getOne();
    if (!one) return one;
    if (Array.isArray(options?.select) && options!.select!.length > 0) {
      const primaryProps = this.repository.metadata.primaryColumns.map((c: any) => c.propertyName);
      const allowed = new Set<string>([...primaryProps, ...options!.select!]);
      const row: any = one as any;
      const pruned: any = {};
      for (const key of allowed) {
        if (row[key] !== undefined) pruned[key] = row[key];
      }
      for (const key of Object.keys(row)) {
        const value = row[key];
        if (typeof value === 'object' && value !== null && !(key in pruned)) {
          pruned[key] = value;
        }
      }
      return pruned as any;
    }
    return one;
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
