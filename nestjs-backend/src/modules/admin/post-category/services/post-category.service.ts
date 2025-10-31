import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { PostCategory } from '../../../../shared/entities/post-category.entity';
import { CrudService } from '../../../../common/base/services/crud.service';
import { DeepPartial } from 'typeorm';

@Injectable()
export class PostCategoryService extends CrudService<PostCategory> {
  constructor(
    @InjectRepository(PostCategory) repo: Repository<PostCategory>,
  ) {
    super(repo);
  }

  /**
   * Override để load relations trong admin
   */
  protected override prepareOptions(queryOptions: any = {}) {
    const base = super.prepareOptions(queryOptions);
    return {
      ...base,
      relations: [
        { name: 'parent', select: ['id', 'name', 'slug'] },
        { name: 'children', select: ['id', 'name', 'slug'] },
      ],
    } as any;
  }

  /**
   * Override getOne để load relations
   */
  async getOne(
    where: any,
    options?: any,
  ) {
    // Đảm bảo load relations trong admin
    const adminOptions = {
      ...options,
      relations: [
        { name: 'parent', select: ['id', 'name', 'slug'] },
        { name: 'children', select: ['id', 'name', 'slug'] },
      ],
    };
    return super.getOne(where, adminOptions);
  }

  /**
   * Tạo mới category
   */
  async create(
    createDto: DeepPartial<PostCategory>,
    createdBy?: number,
  ) {
    const data = { ...createDto };
    this.ensureSlug(data);
    return super.create(data, createdBy);
  }

  /**
   * Cập nhật category
   */
  async update(
    id: number,
    updateDto: DeepPartial<PostCategory>,
    updatedBy?: number,
  ) {
    const data = { ...updateDto };
    this.ensureSlug(data);
    return super.update(id, data, updatedBy);
  }
}

