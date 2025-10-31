import { Injectable } from '@nestjs/common';
import { DeepPartial, Repository, ObjectLiteral } from 'typeorm';
import { ListService } from './list.service';
import { ResponseUtil, ApiResponse } from '../../utils/response.util';
import { StringUtil } from '../../../core/utils/string.util';
import { ResponseRef, handleResponseRef } from '../utils/response-ref.helper';

/**
 * CRUD Service
 * Kế thừa từ ListService và thêm các phương thức Create, Update, Delete
 */
@Injectable()
export abstract class CrudService<T extends ObjectLiteral> extends ListService<T> {
  constructor(protected readonly repository: Repository<T>) {
    super(repository);
  }

  /**
   * Tạo mới một entity
   */
  async create(
    createDto: DeepPartial<T>,
    createdBy?: number,
  ): Promise<ApiResponse<T | null>> {
    let result: ApiResponse<T | null>;
    try {
      // Clone createDto để không mutate original
      const dto = { ...createDto } as any;
      // Hook before create - cho phép xử lý và loại bỏ các field không phải entity columns
      const tempEntity = this.repository.create({} as DeepPartial<T>);
      const responseRef: ResponseRef<T | null> = {};
      const canProceed = await this.beforeCreate(tempEntity, dto, responseRef);
      if (!canProceed) {
        result = handleResponseRef(responseRef);
      } else {
        // Sau khi beforeCreate xử lý, tạo entity từ dto đã được clean
        const entity = this.repository.create({
          ...dto,
          createdBy,
        } as DeepPartial<T>);
        const savedEntity = await this.repository.save(entity);
        await this.afterCreate(savedEntity, createDto);
        result = ResponseUtil.created(savedEntity);
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
    id: number,
    updateDto: DeepPartial<T>,
    updatedBy?: number,
  ): Promise<ApiResponse<T | null>> {
    let result: ApiResponse<T | null>;
    try {
      const entity = await this.repository.findOne({ where: { id } as any });
      if (!entity) {
        result = ResponseUtil.notFound(`Entity with ID ${id} not found`);
      } else {
        // Clone updateDto để không mutate original
        const dto = { ...updateDto } as any;
        // Hook before update - cho phép xử lý và loại bỏ các field không phải entity columns
        const responseRef: ResponseRef<T | null> = {};
        const canProceed = await this.beforeUpdate(entity, dto, responseRef);
        if (!canProceed) {
          result = handleResponseRef(responseRef);
        } else {
          Object.assign(entity, {
            ...dto,
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
   * Xóa cứng (hard delete) một entity
   */
  async delete(
    id: number,
  ): Promise<ApiResponse<null>> {
    let result: ApiResponse<null>;
    try {
      const entity = await this.repository.findOne({ where: { id } as any });
      if (!entity) {
        result = ResponseUtil.notFound(`Entity with ID ${id} not found`);
      } else {
        // Hook before delete (must return true to proceed)
        const responseRef: ResponseRef<null> = {};
        const canProceed = await this.beforeDelete(entity, responseRef);
        if (!canProceed) {
          result = handleResponseRef(responseRef);
        } else {
          await this.repository.remove(entity);
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
   * Hook được gọi trước khi tạo entity
   * Override method này để thêm validation hoặc logic tùy chỉnh
   * @param entity - Entity tạm
   * @param createDto - DTO để tạo (có thể mutate để xử lý dữ liệu)
   * @param response - Object tham chiếu để set response tùy chỉnh
   * @returns true để tiếp tục, false để dừng
   */
  protected async beforeCreate(
    entity: T, 
    createDto: DeepPartial<T>,
    response?: ResponseRef<T | null>
  ): Promise<boolean> {
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
   * Hook được gọi trước khi cập nhật entity
   * @param entity - Entity hiện tại
   * @param updateDto - DTO để cập nhật (có thể mutate để xử lý dữ liệu)
   * @param response - Object tham chiếu để set response tùy chỉnh
   * @returns true để tiếp tục, false để dừng
   */
  protected async beforeUpdate(
    entity: T,
    updateDto: DeepPartial<T>,
    response?: ResponseRef<T | null>
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
   * @param entity - Entity cần xóa
   * @param response - Object tham chiếu để set response tùy chỉnh
   * @returns true để tiếp tục, false để dừng
   */
  protected async beforeDelete(
    entity: T,
    response?: ResponseRef<null>
  ): Promise<boolean> {
    return true;
  }

  /**
   * Hook được gọi sau khi xóa cứng entity
   */
  protected async afterDelete(entity: T): Promise<void> {
    // Override trong service con
  }

  /**
   * Đảm bảo slug được tạo từ name nếu chưa có
   * @param data - Data object chứa name hoặc slug
   * @param excludeId - ID của record cần loại trừ khi check (dùng khi update)
   * @param currentSlug - Slug hiện tại của entity (khi update, để so sánh trực tiếp)
   * @returns Data object đã được xử lý slug
   */
  protected async ensureSlug(data: any, excludeId?: number, currentSlug?: string): Promise<any> {
    // Nếu chưa có slug → tạo từ name
    if (data.name && !data.slug) {
      data.slug = StringUtil.toSlug(data.name);
      return data;
    }
    // Nếu có slug → kiểm tra có trùng với chính nó không (khi update)
    if (data.slug && excludeId) {
      const normalizedSlug = StringUtil.toSlug(data.slug);
      const normalizedCurrentSlug = currentSlug ? StringUtil.toSlug(currentSlug) : null;
      // So sánh trực tiếp với slug hiện tại (nhanh hơn, không cần query)
      if (normalizedCurrentSlug && normalizedSlug === normalizedCurrentSlug) {
        // Đảm bảo xóa slug khỏi data
        delete data.slug;
        // Kiểm tra lại để chắc chắn
        if ('slug' in data) {
          delete data.slug;
        }
        return data;
      }
      // Nếu không có currentSlug, query để check (fallback)
      if (!normalizedCurrentSlug) {
        const existing = await this.repository.findOne({ 
          where: { slug: normalizedSlug } as any 
        });
        // Nếu trùng với chính nó → unset slug (không cập nhật slug)
        if (existing && (existing as any).id === excludeId) {
          delete data.slug;
          if ('slug' in data) {
            delete data.slug;
          }
          return data;
        }
      }
    }
    // Nếu có slug, normalize nó
    if (data.slug) {
      data.slug = StringUtil.toSlug(data.slug);
    }
    return data;
  }
}

