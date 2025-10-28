import { Injectable, NotFoundException } from '@nestjs/common';
import { Repository, FindOptionsWhere, DeepPartial, QueryDeepPartialEntity } from 'typeorm';
import { BaseEntity } from '../entities/base.entity';
import { ListService } from './list.service';
import { PaginatedListResult } from '../interfaces/list.interface';

/**
 * CRUD Service
 * Kế thừa từ ListService và thêm các phương thức Create, Update, Delete
 */
@Injectable()
export abstract class CrudService<T extends BaseEntity> extends ListService<T> {
  constructor(repository: Repository<T>) {
    super(repository);
  }

  /**
   * Tạo mới một entity
   */
  async create(
    createDto: DeepPartial<T>,
    createdBy?: string,
  ): Promise<T> {
    const entity = this.repository.create({
      ...createDto,
      createdBy,
    } as DeepPartial<T>);

    // Hook before create - override để thêm logic tùy chỉnh
    await this.beforeCreate(entity, createDto);

    const savedEntity = await this.repository.save(entity);

    // Hook after create - override để thêm logic tùy chỉnh
    await this.afterCreate(savedEntity, createDto);

    return savedEntity;
  }

  /**
   * Tạo nhiều entities cùng lúc
   */
  async createMany(
    createDtos: DeepPartial<T>[],
    createdBy?: string,
  ): Promise<T[]> {
    const entities = createDtos.map((dto) =>
      this.repository.create({
        ...dto,
        createdBy,
      } as DeepPartial<T>),
    );

    // Hook before create many
    await this.beforeCreateMany(entities, createDtos);

    const savedEntities = await this.repository.save(entities);

    // Hook after create many
    await this.afterCreateMany(savedEntities, createDtos);

    return savedEntities;
  }

  /**
   * Cập nhật một entity (tự động load relations nếu cần)
   */
  async update(
    id: string,
    updateDto: QueryDeepPartialEntity<T> | DeepPartial<T>,
    updatedBy?: string,
  ): Promise<T> {
    const entity = await this.findByIdOrFail(id);

    // Hook before update
    await this.beforeUpdate(entity, updateDto);

    // Nếu có relations trong DTO -> dùng save, ngược lại dùng update
    const hasRelations = Object.keys(updateDto).some(key => 
      typeof updateDto[key] === 'object' && updateDto[key] !== null && !Array.isArray(updateDto[key])
    );

    if (hasRelations) {
      // Update với relations - dùng save
      Object.assign(entity, {
        ...updateDto,
        updatedBy,
      });
      const updatedEntity = await this.repository.save(entity);
      await this.afterUpdate(updatedEntity, updateDto);
      return updatedEntity;
    } else {
      // Update đơn giản - dùng update (nhanh hơn)
      await this.repository.update(id, {
        ...updateDto,
        updatedBy,
      } as QueryDeepPartialEntity<T>);
      
      const updatedEntity = await this.findByIdOrFail(id);
      await this.afterUpdate(updatedEntity, updateDto);
      return updatedEntity;
    }
  }

  /**
   * Cập nhật nhiều entities cùng lúc (tối giản)
   */
  async updateMany(
    updates: Array<{ id: string; data: QueryDeepPartialEntity<T> | DeepPartial<T> }>,
    updatedBy?: string,
  ): Promise<T[]> {
    return Promise.all(
      updates.map(({ id, data }) => this.update(id, data, updatedBy))
    );
  }

  /**
   * Xóa mềm (soft delete) một entity
   */
  async softDelete(
    id: string,
    deletedBy?: string,
  ): Promise<void> {
    const entity = await this.findByIdOrFail(id);

    // Hook before soft delete
    await this.beforeSoftDelete(entity);

    await this.repository.update(id, {
      deletedAt: new Date(),
      deletedBy,
    } as QueryDeepPartialEntity<T>);

    // Hook after soft delete
    await this.afterSoftDelete(entity);
  }

  /**
   * Xóa mềm nhiều entities cùng lúc
   */
  async softDeleteMany(
    ids: string[],
    deletedBy?: string,
  ): Promise<void> {
    await this.repository.update(ids, {
      deletedAt: new Date(),
      deletedBy,
    } as QueryDeepPartialEntity<T>);
  }

  /**
   * Xóa cứng (hard delete) một entity
   */
  async delete(
    id: string,
  ): Promise<void> {
    const entity = await this.findByIdOrFail(id);

    // Hook before delete
    await this.beforeDelete(entity);

    await this.repository.remove(entity);

    // Hook after delete
    await this.afterDelete(entity);
  }

  /**
   * Xóa cứng nhiều entities cùng lúc
   */
  async deleteMany(
    ids: string[],
  ): Promise<void> {
    const entities = await this.repository.find({
      where: ids.map((id) => ({ id } as FindOptionsWhere<T>)),
    } as any);

    // Hook before delete many
    await this.beforeDeleteMany(entities);

    await this.repository.remove(entities);

    // Hook after delete many
    await this.afterDeleteMany(entities);
  }

  /**
   * Khôi phục một entity đã bị xóa mềm
   */
  async restore(
    id: string,
  ): Promise<T> {
    const entity = await this.repository.findOne({
      where: { id } as FindOptionsWhere<T>,
      withDeleted: true,
    } as any);

    if (!entity || !entity.deletedAt) {
      throw new NotFoundException(`Entity with ID ${id} not found or not deleted`);
    }

    // Hook before restore
    await this.beforeRestore(entity);

    await this.repository.update(id, {
      deletedAt: null,
      deletedBy: null,
    } as QueryDeepPartialEntity<T>);

    const restoredEntity = await this.findByIdOrFail(id);

    // Hook after restore
    await this.afterRestore(restoredEntity);

    return restoredEntity;
  }

  /**
   * Khôi phục nhiều entities đã bị xóa mềm
   */
  async restoreMany(
    ids: string[],
  ): Promise<T[]> {
    const restoredEntities: T[] = [];

    for (const id of ids) {
      try {
        const entity = await this.restore(id);
        restoredEntities.push(entity);
      } catch (error) {
        // Continue với các entity khác nếu một entity lỗi
        console.error(`Failed to restore entity with ID ${id}:`, error);
      }
    }

    return restoredEntities;
  }

  /**
   * Tìm hoặc tạo entity (tối giản)
   */
  async findOrCreate(
    where: FindOptionsWhere<T> | string,
    createDto?: DeepPartial<T>,
    createdBy?: string,
  ): Promise<{ entity: T; created: boolean }> {
    // Nếu where là string -> tìm theo ID
    const whereCondition = typeof where === 'string' 
      ? ({ id: where } as FindOptionsWhere<T>)
      : where;

    const existingEntity = await this.findOne(whereCondition);

    if (existingEntity) {
      return { entity: existingEntity, created: false };
    }

    const newEntity = await this.create(
      createDto ? { ...createDto, ...whereCondition } : (whereCondition as DeepPartial<T>),
      createdBy
    );
    return { entity: newEntity, created: true };
  }

  /**
   * Tạo hoặc cập nhật entity (upsert - tối giản)
   */
  async createOrUpdate(
    where: FindOptionsWhere<T>,
    data: DeepPartial<T>,
    userId?: string,
  ): Promise<{ entity: T; created: boolean }> {
    const existingEntity = await this.findOne(where);

    if (existingEntity) {
      const updatedEntity = await this.update(existingEntity.id, data, userId);
      return { entity: updatedEntity, created: false };
    }

    const newEntity = await this.create(data, userId);
    return { entity: newEntity, created: true };
  }

  /**
   * Kiểm tra entity có tồn tại không (tối giản)
   */
  async exists(
    where: FindOptionsWhere<T> | FindOptionsWhere<T>[] | string,
  ): Promise<boolean> {
    // Nếu là string -> tìm theo ID
    const whereCondition = typeof where === 'string' 
      ? ({ id: where } as FindOptionsWhere<T>)
      : where;

    const count = await this.count(whereCondition);
    return count > 0;
  }

  // ==================== Hooks - Override trong service con để thêm logic tùy chỉnh ====================

  /**
   * Hook được gọi trước khi tạo entity
   * Override method này để thêm validation hoặc logic tùy chỉnh
   */
  protected async beforeCreate(entity: T, createDto: DeepPartial<T>): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi sau khi tạo entity
   * Override method này để thêm logic sau khi tạo
   */
  protected async afterCreate(entity: T, createDto: DeepPartial<T>): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi trước khi tạo nhiều entities
   */
  protected async beforeCreateMany(
    entities: T[],
    createDtos: DeepPartial<T>[],
  ): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi sau khi tạo nhiều entities
   */
  protected async afterCreateMany(
    entities: T[],
    createDtos: DeepPartial<T>[],
  ): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi trước khi cập nhật entity
   */
  protected async beforeUpdate(
    entity: T,
    updateDto: QueryDeepPartialEntity<T> | DeepPartial<T>,
  ): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi sau khi cập nhật entity
   */
  protected async afterUpdate(
    entity: T,
    updateDto: QueryDeepPartialEntity<T> | DeepPartial<T>,
  ): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi trước khi xóa mềm entity
   */
  protected async beforeSoftDelete(entity: T): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi sau khi xóa mềm entity
   */
  protected async afterSoftDelete(entity: T): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi trước khi xóa cứng entity
   */
  protected async beforeDelete(entity: T): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi sau khi xóa cứng entity
   */
  protected async afterDelete(entity: T): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi trước khi xóa cứng nhiều entities
   */
  protected async beforeDeleteMany(entities: T[]): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi sau khi xóa cứng nhiều entities
   */
  protected async afterDeleteMany(entities: T[]): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi trước khi khôi phục entity
   */
  protected async beforeRestore(entity: T): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi sau khi khôi phục entity
   */
  protected async afterRestore(entity: T): Promise<void> {
    // Override trong service con
  }
}

