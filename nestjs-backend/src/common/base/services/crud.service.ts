import { Injectable } from '@nestjs/common';
import { FindOptionsWhere, DeepPartial } from 'typeorm';
import { BaseEntity } from '../entities/base.entity';
import { ListService } from './list.service';
import { ResponseUtil, ApiResponse } from '../../utils/response.util';
import { BaseRepository } from '../repositories/base.repository';

/**
 * CRUD Service
 * Kế thừa từ ListService và thêm các phương thức Create, Update, Delete
 */
@Injectable()
export abstract class CrudService<T extends BaseEntity> extends ListService<T> {
  constructor(repository: BaseRepository<T>) {
    super(repository);
  }

  /**
   * Tạo mới một entity
   */
  async create(
    createDto: DeepPartial<T>,
    createdBy?: string,
  ): Promise<ApiResponse<T | null>> {
    let result: ApiResponse<T | null>;
    try {
      const entity = this.repository.create({
        ...createDto,
        createdBy,
      } as DeepPartial<T>);

      // Hook before create - override để thêm logic tùy chỉnh (must return true to proceed)
      const canProceed = await this.beforeCreate(entity, createDto);
      if (!canProceed) {
        result = ResponseUtil.error('Điều kiện tiền xử lý không đạt', 'PRECONDITION_FAILED');
      } else {
        const savedEntity = await this.repository.save(entity);
        // Hook after create - chỉ gọi khi thao tác thành công
        await this.afterCreate(savedEntity, createDto);
        result = ResponseUtil.created(savedEntity);
      }
    } catch (error) {
      result = ResponseUtil.error(`Tạo mới thất bại: ${error.message}`, 'CREATE_FAILED');
    }
    return result;
  }

  /**
   * Tạo nhiều entities cùng lúc
   */
  async createMany(
    createDtos: DeepPartial<T>[],
    createdBy?: string,
  ): Promise<ApiResponse<(T[] | null)>> {
    let result: ApiResponse<T[] | null>;
    try {
      const entities = createDtos.map((dto) =>
        this.repository.create({
          ...dto,
          createdBy,
        } as DeepPartial<T>),
      );

      // Hook before create many (must return true to proceed)
      const canProceed = await this.beforeCreateMany(entities, createDtos);
      if (!canProceed) {
        result = ResponseUtil.error('Điều kiện tiền xử lý không đạt', 'PRECONDITION_FAILED');
      } else {
        const savedEntities = await this.repository.save(entities);
        // Hook after create many - chỉ gọi khi thao tác thành công
        await this.afterCreateMany(savedEntities, createDtos);
        result = ResponseUtil.created(savedEntities, `Tạo mới ${savedEntities.length} bản ghi thành công`);
      }
    } catch (error) {
      result = ResponseUtil.error(`Tạo mới thất bại: ${error.message}`, 'CREATE_FAILED');
    }
    return result;
  }

  /**
   * Cập nhật một entity
   */
  async update(
    id: string,
    updateDto: DeepPartial<T>,
    updatedBy?: string,
  ): Promise<ApiResponse<T | null>> {
    let result: ApiResponse<T | null>;
    try {
      const entity = await this.repository.findOne({ where: { id } as FindOptionsWhere<T> });
      if (!entity) {
        result = ResponseUtil.notFound(`Entity with ID ${id} not found`);
      } else {
        // Hook before update (must return true to proceed)
        const canProceed = await this.beforeUpdate(entity, updateDto);
        if (!canProceed) {
          result = ResponseUtil.error('Điều kiện tiền xử lý không đạt', 'PRECONDITION_FAILED');
        } else {
          // Update entity
          Object.assign(entity, {
            ...updateDto,
            updatedBy,
          });
          
          const updatedEntity = await this.repository.save(entity);
          await this.afterUpdate(updatedEntity, updateDto);
          result = ResponseUtil.updated(updatedEntity);
        }
      }
    } catch (error) {
      result = ResponseUtil.error(`Cập nhật thất bại: ${error.message}`, 'UPDATE_FAILED');
    }
    return result;
  }

  /**
   * Cập nhật nhiều entities cùng lúc (tối giản)
   */
  async updateMany(
    updates: Array<{ id: string; data: DeepPartial<T> }>,
    updatedBy?: string,
  ) {
    try {
      const updatedEntities = await Promise.all(
        updates.map(({ id, data }) => this.update(id, data, updatedBy))
      );

      // Check if any update failed
      const failedCount = updatedEntities.filter((result: any) => result.code !== 'UPDATED').length;
      if (failedCount > 0) {
        return ResponseUtil.error(`${failedCount}/${updates.length} bản ghi cập nhật thất bại`, 'UPDATE_PARTIAL_FAILED');
      }

      return ResponseUtil.updated(
        updatedEntities.map((result: any) => result.data),
        `Cập nhật ${updatedEntities.length} bản ghi thành công`
      );
    } catch (error) {
      return ResponseUtil.error(`Cập nhật thất bại: ${error.message}`, 'UPDATE_FAILED');
    }
  }

  /**
   * Xóa cứng (hard delete) một entity
   */
  async delete(
    id: string,
  ): Promise<ApiResponse<null>> {
    let result: ApiResponse<null>;
    try {
      const entity = await this.repository.findOne({ where: { id } as FindOptionsWhere<T> });
      if (!entity) {
        result = ResponseUtil.notFound(`Entity with ID ${id} not found`);
      } else {
        // Hook before delete (must return true to proceed)
        const canProceed = await this.beforeDelete(entity);
        if (!canProceed) {
          result = ResponseUtil.error('Điều kiện tiền xử lý không đạt', 'PRECONDITION_FAILED');
        } else {
          await this.repository.remove(entity);
          // Hook after delete
          await this.afterDelete(entity);
          result = ResponseUtil.deleted();
        }
      }
    } catch (error) {
      result = ResponseUtil.error(`Xóa thất bại: ${error.message}`, 'DELETE_FAILED');
    }
    return result;
  }

  /**
   * Xóa cứng nhiều entities cùng lúc
   */
  async deleteMany(
    ids: string[],
  ): Promise<ApiResponse<null>> {
    try {
      const entities = await this.repository.find({
        where: ids.map((id) => ({ id } as FindOptionsWhere<T>)),
      } as any);

      let result: ApiResponse<null>;
      if (entities.length === 0) {
        result = ResponseUtil.error('Không tìm thấy bản ghi nào để xóa', 'DELETE_NOT_FOUND');
      } else if (await this.beforeDeleteMany(entities)) {
        await this.repository.remove(entities);
        await this.afterDeleteMany(entities);
        result = ResponseUtil.deleted(`Xóa ${entities.length} bản ghi thành công`);
      } else {
        result = ResponseUtil.error('Điều kiện tiền xử lý không đạt', 'PRECONDITION_FAILED');
      }
      return result;
    } catch (error) {
      return ResponseUtil.error(`Xóa thất bại: ${error.message}`, 'DELETE_FAILED');
    }
  }
  
  /**
   * Hook được gọi trước khi tạo entity
   * Override method này để thêm validation hoặc logic tùy chỉnh
   */
  protected async beforeCreate(entity: T, createDto: DeepPartial<T>): Promise<boolean> {
    return true;
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
  ): Promise<boolean> {
    return true;
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
  ): Promise<boolean> {
    return true;
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
  protected async beforeDelete(entity: T): Promise<boolean> {
    return true;
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
  protected async beforeDeleteMany(entities: T[]): Promise<boolean> {
    return true;
  }

  /**
   * Hook được gọi sau khi xóa cứng nhiều entities
   */
  protected async afterDeleteMany(entities: T[]): Promise<void> {
    // Override trong service con
  }
}

