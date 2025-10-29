import { Repository, FindManyOptions, FindOptionsWhere, SelectQueryBuilder } from 'typeorm';
import { BaseEntity } from '../entities/base.entity';

/**
 * Base Repository với các phương thức CRUD cơ bản
 * Chỉ có các method tổng quát, không có variants như findActive, findDeleted
 */
export class BaseRepository<T extends BaseEntity> extends Repository<T> {
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
      this.applyRelations(queryBuilder, options.relations, options.relationFields);
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
   * Áp dụng relations với field selection
   */
  private applyRelations(
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
}