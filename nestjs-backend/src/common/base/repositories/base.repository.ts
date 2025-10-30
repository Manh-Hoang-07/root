import { Repository, FindManyOptions, FindOptionsWhere, SelectQueryBuilder, EntityTarget, DataSource } from 'typeorm';
import { BaseEntity } from '../entities/base.entity';

/**
 * Base Repository với các phương thức CRUD cơ bản
 * Chỉ có các method tổng quát, không có variants như findActive, findDeleted
 */
export class BaseRepository<T> extends Repository<T> {
  /**
   * Create a BaseRepository wrapper around a TypeORM Repository while preserving internals.
   */
  static from<T>(dataSource: DataSource, target: EntityTarget<T>): BaseRepository<T> {
    const repo = dataSource.getRepository<T>(target);
    // mixin instance methods from BaseRepository.prototype (excluding constructor)
    const proto = BaseRepository.prototype as any;
    const names = Object.getOwnPropertyNames(proto).filter((n) => n !== 'constructor');
    const methods: Record<string, any> = {};
    for (const n of names) {
      const desc = Object.getOwnPropertyDescriptor(proto, n);
      if (desc && typeof desc.value === 'function') methods[n] = desc.value;
    }
    return (repo as any).extend(methods) as BaseRepository<T>;
  }
  /**
  * Tìm tất cả entities với phân trang
  */
  async findAll(
    page: number = 1,
    limit: number = 10,
    options?: FindManyOptions<T> & {
      relationFields?: Record<string, string[]>; // { 'user': ['id', 'name'], 'profile': ['avatar'] }
    },
  ): Promise<{ data: T[]; total: number; page: number; limit: number }> {
    const queryBuilder = this.createQueryBuilder('entity');

    // Apply where conditions
    if (options?.where) {
      this.applyWhereConditions(queryBuilder, options.where);
    }

    // Apply relations with field selection
    if (options?.relations) {
      if (typeof options.relations === 'object' && !Array.isArray(options.relations)) {
        this.applyRelationsOld(queryBuilder, options.relations, options.relationFields);
      } else {
        this.applyRelations(queryBuilder, options.relations as any);
      }
    }

    // Apply sorting
    if (options?.order) {
      Object.entries(options.order).forEach(([key, direction]) => {
        queryBuilder.addOrderBy(`entity.${key}`, direction as 'ASC' | 'DESC');
      });
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
      .orderBy('entity.createdAt', 'DESC')
      .limit(limit);

    return queryBuilder.getMany();
  }

  /**
   * Áp dụng relations với field selection - support string or {name, select} format
   */
  applyRelations(queryBuilder: SelectQueryBuilder<T>, relations: Array<string | { name: string; select?: string[] }>): void {
    if (!Array.isArray(relations) || relations.length === 0) return;
    for (const rel of relations) {
      if (typeof rel === 'string') {
        queryBuilder.leftJoinAndSelect(`entity.${rel}`, rel);
      } else if (rel && typeof rel === 'object' && rel.name) {
        const alias = rel.name;
        const cols: string[] = Array.isArray(rel.select) ? rel.select.filter((c: any) => typeof c === 'string' && c.trim()) : [];
        if (cols.length > 0) {
          // Safely determine primary keys of related entity without accessing connection
          const relationDef = this.metadata.relations.find(r => r.propertyName === alias);
          const relPrimaryProps = relationDef?.inverseEntityMetadata?.primaryColumns?.map((c: any) => c.propertyName) || [];
          const uniq = new Set<string>([...relPrimaryProps, ...cols]);
          queryBuilder.leftJoin(`entity.${alias}`, alias);
          queryBuilder.addSelect(Array.from(uniq).map(c => `${alias}.${c}`));
        } else {
          queryBuilder.leftJoinAndSelect(`entity.${alias}`, alias);
        }
      }
    }
  }

  /**
   * Áp dụng điều kiện where vào query builder
   */
  applyWhereConditions(queryBuilder: SelectQueryBuilder<T>, where: any): void {
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
   * Apply select columns while ensuring primary keys are included
   */
  applySelectColumns(queryBuilder: SelectQueryBuilder<T>, select?: string[]): void {
    if (!Array.isArray(select) || select.length === 0) return;
    const primaryProps = this.metadata.primaryColumns.map(c => c.propertyName);
    const uniq = new Set<string>();
    for (const p of primaryProps) uniq.add(p);
    for (const col of select) {
      if (typeof col === 'string' && col.trim()) uniq.add(col.trim());
    }
    const columns = Array.from(uniq).map(col => `entity.${col}`);
    queryBuilder.select(columns);
  }

  /**
   * Parse sort string | string[] | {field, direction}[] into array
   */
  parseSort(sort?: any): Array<{ field: string; direction: 'ASC' | 'DESC' }> {
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

  /**
   * Apply sorting to query builder
   */
  applySorting(queryBuilder: SelectQueryBuilder<T>, sort?: any): void {
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

  /**
   * Get default order field (prefer createdAt, then id)
   */
  getDefaultOrderField(): { field: string; direction: 'ASC' | 'DESC' } {
    const meta = this.metadata;
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
   * Original applyRelations for backward compatibility with relationFields
   */
  applyRelationsOld(
    queryBuilder: SelectQueryBuilder<T>,
    relations: any,
    relationFields?: Record<string, string[]>,
  ): void {
    if (Array.isArray(relations)) {
      relations.forEach(relation => {
        const fields = relationFields?.[relation];
        if (fields && fields.length > 0) {
          // Select specific fields from relation
          queryBuilder.leftJoinAndSelect(`entity.${relation}`, relation);
          fields.forEach(field => {
            queryBuilder.addSelect(`${relation}.${field}`);
          });
        } else {
          // Select all fields from relation (default)
          queryBuilder.leftJoinAndSelect(`entity.${relation}`, relation);
        }
      });
    } else if (typeof relations === 'object' && relations !== null) {
      Object.entries(relations).forEach(([relation, select]) => {
        if (select) {
          const fields = relationFields?.[relation];
          if (fields && fields.length > 0) {
            // Select specific fields from relation
            queryBuilder.leftJoinAndSelect(`entity.${relation}`, relation);
            fields.forEach(field => {
              queryBuilder.addSelect(`${relation}.${field}`);
            });
          } else {
            // Select all fields from relation (default)
            queryBuilder.leftJoinAndSelect(`entity.${relation}`, relation);
          }
        }
      });
    }
  }

  /**
   * Original applyWhereConditions - kept for reference
   */
  applyWhereConditionsOld(
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
}

/**
 * Wrap a TypeORM Repository<T> with BaseRepository helpers using .extend()
 */
// Helper removed per design: services receive BaseRepository via module provider