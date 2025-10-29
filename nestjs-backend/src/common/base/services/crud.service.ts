import { Injectable, NotFoundException } from '@nestjs/common';
import { Repository, FindOptionsWhere, DeepPartial } from 'typeorm';
import { BaseEntity } from '../entities/base.entity';
import { ListService } from './list.service';
import { ResponseUtil } from '../../utils/response.util';

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
   * Tìm entity theo ID hoặc throw NotFoundException
   */
  async findByIdOrFail(id: string): Promise<T> {
    const entity = await this.repository.findOne({
      where: { id } as FindOptionsWhere<T>,
    });
    
    if (!entity) {
      throw new NotFoundException(`Entity with ID ${id} not found`);
    }
    
    return entity;
  }

  /**
   * Tạo mới một entity
   */
  async create(
    createDto: DeepPartial<T>,
    createdBy?: string,
  ): Promise<{ data: T | null; message: string; code: string; success: boolean }> {
    try {
      const entity = this.repository.create({
        ...createDto,
        createdBy,
      } as DeepPartial<T>);

      // Hook before create - override để thêm logic tùy chỉnh
      await this.beforeCreate(entity, createDto);

      const savedEntity = await this.repository.save(entity);

      // Hook after create - override để thêm logic tùy chỉnh
      await this.afterCreate(savedEntity, createDto);

      return {
        data: savedEntity,
        message: 'Tạo mới thành công',
        code: 'CREATED',
        success: true,
      };
    } catch (error) {
      return {
        data: null,
        message: `Tạo mới thất bại: ${error.message}`,
        code: 'CREATE_FAILED',
        success: false,
      };
    }
  }

  /**
   * Tạo nhiều entities cùng lúc
   */
  async createMany(
    createDtos: DeepPartial<T>[],
    createdBy?: string,
  ): Promise<{ data: T[] | null; message: string; code: string; success: boolean }> {
    try {
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

      return {
        data: savedEntities,
        message: `Tạo mới ${savedEntities.length} bản ghi thành công`,
        code: 'CREATED',
        success: true,
      };
    } catch (error) {
      return {
        data: null,
        message: `Tạo mới thất bại: ${error.message}`,
        code: 'CREATE_FAILED',
        success: false,
      };
    }
  }

  /**
   * Cập nhật một entity
   */
  async update(
    id: string,
    updateDto: DeepPartial<T>,
    updatedBy?: string,
  ): Promise<{ data: T | null; message: string; code: string; success: boolean }> {
    try {
      const entity = await this.findByIdOrFail(id);

      // Hook before update
      await this.beforeUpdate(entity, updateDto);

      // Update entity
      Object.assign(entity, {
        ...updateDto,
        updatedBy,
      });
      
      const updatedEntity = await this.repository.save(entity);
      await this.afterUpdate(updatedEntity, updateDto);
      
      return {
        data: updatedEntity,
        message: 'Cập nhật thành công',
        code: 'UPDATED',
        success: true,
      };
    } catch (error) {
      return {
        data: null,
        message: `Cập nhật thất bại: ${error.message}`,
        code: 'UPDATE_FAILED',
        success: false,
      };
    }
  }

  /**
   * Cập nhật nhiều entities cùng lúc (tối giản)
   */
  async updateMany(
    updates: Array<{ id: string; data: DeepPartial<T> }>,
    updatedBy?: string,
  ): Promise<{ data: T[] | null; message: string; code: string; success: boolean }> {
    try {
      const updatedEntities = await Promise.all(
        updates.map(({ id, data }) => this.update(id, data, updatedBy))
      );

      // Check if any update failed
      const failedCount = updatedEntities.filter(result => !result.success).length;
      if (failedCount > 0) {
        return {
          data: null,
          message: `${failedCount}/${updates.length} bản ghi cập nhật thất bại`,
          code: 'UPDATE_PARTIAL_FAILED',
          success: false,
        };
      }

      return {
        data: updatedEntities.map(result => result.data!),
        message: `Cập nhật ${updatedEntities.length} bản ghi thành công`,
        code: 'UPDATED',
        success: true,
      };
    } catch (error) {
      return {
        data: null,
        message: `Cập nhật thất bại: ${error.message}`,
        code: 'UPDATE_FAILED',
        success: false,
      };
    }
  }

  /**
   * Xóa cứng (hard delete) một entity
   */
  async delete(
    id: string,
  ): Promise<{ data: null; message: string; code: string; success: boolean }> {
    try {
      const entity = await this.findByIdOrFail(id);

      // Hook before delete
      await this.beforeDelete(entity);

      await this.repository.remove(entity);

      // Hook after delete
      await this.afterDelete(entity);

      return {
        data: null,
        message: 'Xóa thành công',
        code: 'DELETED',
        success: true,
      };
    } catch (error) {
      return {
        data: null,
        message: `Xóa thất bại: ${error.message}`,
        code: 'DELETE_FAILED',
        success: false,
      };
    }
  }

  /**
   * Xóa cứng nhiều entities cùng lúc
   */
  async deleteMany(
    ids: string[],
  ): Promise<{ data: null; message: string; code: string; success: boolean }> {
    try {
      const entities = await this.repository.find({
        where: ids.map((id) => ({ id } as FindOptionsWhere<T>)),
      } as any);

      if (entities.length === 0) {
        return {
          data: null,
          message: 'Không tìm thấy bản ghi nào để xóa',
          code: 'DELETE_NOT_FOUND',
          success: false,
        };
      }

      // Hook before delete many
      await this.beforeDeleteMany(entities);

      await this.repository.remove(entities);

      // Hook after delete many
      await this.afterDeleteMany(entities);

      return {
        data: null,
        message: `Xóa ${entities.length} bản ghi thành công`,
        code: 'DELETED',
        success: true,
      };
    } catch (error) {
      return {
        data: null,
        message: `Xóa thất bại: ${error.message}`,
        code: 'DELETE_FAILED',
        success: false,
      };
    }
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
    updateDto: DeepPartial<T>,
  ): Promise<void> {
    // Override trong service con
  }

  /**
   * Hook được gọi sau khi cập nhật entity
   */
  protected async afterUpdate(
    entity: T,
    updateDto: DeepPartial<T>,
  ): Promise<void> {
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
}

