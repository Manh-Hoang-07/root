import { Injectable } from '@nestjs/common';
import { FindOptionsWhere } from 'typeorm';
import { BaseEntity } from '../entities/base.entity';
import { Filters, Options, PaginatedListResult } from '../interfaces/list.interface';
import { BaseRepository } from '../repositories/base.repository';

/**
 * List Service với các phương thức cơ bản cho việc lấy danh sách
 */
@Injectable()
export abstract class ListService<T extends BaseEntity> {
  constructor(protected readonly repository: BaseRepository<T>) {}

  /**
   * Tìm tất cả entities với phân trang
   */
  async findAll(
    filters?: Filters<T>,
    options?: Options,
  ): Promise<PaginatedListResult<T>> {
    const page = options?.page || 1;
    const limit = options?.limit || 10;
    const relations = options?.relations || [];

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
    // Prepare filters (override-able in child services)
    const prepared = this.prepareFilters(filters, options);
    if (prepared) {
      const queryBuilder = this.repository.createQueryBuilder('entity');
      // Normalize filters once to avoid repeated checks
      const whereFilters = prepared === true ? filters : prepared;
      if (whereFilters) {
        this.applyWhereConditions(queryBuilder, whereFilters);
      }
      // Apply select columns (always include primary keys)
      this.applySelectColumns(queryBuilder, options?.select);
      // Apply relations (default select all fields; allow per-relation select override)
      this.applyRelations(queryBuilder, relations);
      // Apply sorting
      this.applySorting(queryBuilder, options?.sort);
      // Apply pagination
      queryBuilder.skip((page - 1) * limit).take(limit);
      // Execute query (always include total)
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
   * Tìm một entity theo điều kiện
   */
  async findOne(where: FindOptionsWhere<T>): Promise<T | null> {
    return this.repository.findOne({ where });
  }

  /**
   * Đếm số lượng entities
   */
  async count(where?: FindOptionsWhere<T> | FindOptionsWhere<T>[]): Promise<number> {
    return this.repository.count({ where });
  }

  /**
   * Áp dụng điều kiện where vào query builder
   */
  private applyWhereConditions(
    queryBuilder: any,
    where: any,
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
    } else if (where && typeof where === 'object') {
      Object.entries(where).forEach(([key, value]) => {
        queryBuilder.andWhere(`entity.${key} = :${key}`, { [key]: value });
      });
    }
  }

  /**
   * Lấy các trường tìm kiếm mặc định
   * Override method này trong service con
   */
  protected getSearchFields(): string[] {
    return [];
  }

  /**
   * Áp dụng sắp xếp: nhận sort string | string[] | SortOptions[]
   */
  private applySorting(queryBuilder: any, sort?: any): void {
    const parsed = this.parseSort(sort);
    if (!parsed || parsed.length === 0) {
      const { field, direction } = this.getDefaultOrderField();
      queryBuilder.orderBy(`entity.${field}`, direction);
      return;
    }
    parsed.forEach((s: any, idx: number) => {
      const order: 'ASC' | 'DESC' = (s.direction || 'DESC').toUpperCase() === 'ASC' ? 'ASC' : 'DESC';
      const field = s.field;
      if (idx === 0) {
        queryBuilder.orderBy(`entity.${field}`, order);
      } else {
        queryBuilder.addOrderBy(`entity.${field}`, order);
      }
    });
  }

  private parseSort(sort?: any): Array<{ field: string; direction: 'ASC' | 'DESC' }> {
    if (!sort) return [];
    const asArray = Array.isArray(sort) ? sort : [sort];
    const map = new Map<string, 'ASC' | 'DESC'>();
    for (const item of asArray) {
      if (!item) continue;
      if (typeof item === 'string') {
        const [fieldRaw, dirRaw] = item.split(':');
        const field = (fieldRaw || '').trim();
        if (!field) continue;
        const direction: 'ASC' | 'DESC' = (dirRaw || 'DESC').toUpperCase() === 'ASC' ? 'ASC' : 'DESC';
        if (!map.has(field)) map.set(field, direction);
      } else if (typeof item === 'object' && 'field' in item) {
        const f = String((item as any).field || '').trim();
        if (!f) continue;
        const direction: 'ASC' | 'DESC' = ((item as any).direction || 'DESC').toUpperCase() === 'ASC' ? 'ASC' : 'DESC';
        if (!map.has(f)) map.set(f, direction);
      }
    }
    // If still empty, fallback to default order
    if (map.size === 0) {
      const { field, direction } = this.getDefaultOrderField();
      return [{ field, direction }];
    }
    return Array.from(map.entries()).map(([field, direction]) => ({ field, direction }));
  }

  private getDefaultOrderField(): { field: string; direction: 'ASC' | 'DESC' } {
    const meta = this.repository.metadata;
    const columnNames = new Set(meta.columns.map(c => c.propertyName));
    const indexedColumns = new Set(
      meta.indices.flatMap(i => i.columns?.map(c => (typeof c === 'string' ? c : c.propertyName)) || []),
    );
    // Prefer createdAt if exists (and ideally indexed)
    if (columnNames.has('createdAt')) return { field: 'createdAt', direction: 'DESC' };
    // Next prefer id/primary column
    if (columnNames.has('id')) return { field: 'id', direction: 'DESC' };
    if (meta.primaryColumns[0]) return { field: meta.primaryColumns[0].propertyName, direction: 'DESC' };
    // As a last resort, use the first column
    return { field: meta.columns[0]?.propertyName || 'createdAt', direction: 'DESC' };
  }

  /**
   * Apply base-entity select list while ensuring primary keys are included
   */
  private applySelectColumns(queryBuilder: any, select?: string[]): void {
    if (!Array.isArray(select) || select.length === 0) return;
    const primaryProps = this.repository.metadata.primaryColumns.map(c => c.propertyName);
    const uniq = new Set<string>();
    for (const p of primaryProps) uniq.add(p);
    for (const col of select) {
      if (typeof col === 'string' && col.trim()) uniq.add(col.trim());
    }
    const columns = Array.from(uniq).map(col => `entity.${col}`);
    queryBuilder.select(columns);
  }

  /**
   * Chuẩn hóa/merge filters trước khi build query
   * Override trong service con để thêm điều kiện mặc định hoặc chuyển đổi filters phức tạp
   */
  protected prepareFilters(
    filters?: Filters<T>,
    _options?: Options,
  ): boolean | any {
    // Return one of:
    // - false/null/undefined: stop query and return empty result
    // - true: proceed using original filters
    // - object/array: use as effective filters
    return filters as any;
  }

  /**
   * Apply relations into queryBuilder; supports string (all fields) or {name, select} for partial join
   */
  private applyRelations(queryBuilder: any, relations: Array<string | { name: string; select?: string[] }>) {
    if (!Array.isArray(relations) || relations.length === 0) return;
    for (const rel of relations) {
      if (typeof rel === 'string') {
        queryBuilder.leftJoinAndSelect(`entity.${rel}`, rel);
      } else if (rel && typeof rel === 'object' && rel.name) {
        const alias = rel.name;
        const cols: string[] = Array.isArray(rel.select) ? rel.select.filter((c: any) => typeof c === 'string' && c.trim()) : [];
        if (cols.length > 0) {
          // Ensure primary columns of relation are included
          const relationMeta = this.repository.manager.connection.getMetadata(
            this.repository.metadata.relations.find(r => r.propertyName === alias)?.type as any
          );
          const relPrimaryProps = relationMeta?.primaryColumns?.map((c: any) => c.propertyName) || [];
          const uniq = new Set<string>([...relPrimaryProps, ...cols]);
          queryBuilder.leftJoin(`entity.${alias}`, alias);
          queryBuilder.addSelect(Array.from(uniq).map(c => `${alias}.${c}`));
        } else {
          queryBuilder.leftJoinAndSelect(`entity.${alias}`, alias);
        }
      }
    }
  }
}
