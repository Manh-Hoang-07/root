import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { PostTag } from '../../../../shared/entities/post-tag.entity';
import { CrudService } from '../../../../common/base/services/crud.service';
import { DeepPartial } from 'typeorm';

@Injectable()
export class PostTagService extends CrudService<PostTag> {
  constructor(
    @InjectRepository(PostTag) repo: Repository<PostTag>,
  ) {
    super(repo);
  }

  /**
   * Override để load relations trong admin (PostTag không có relations chính, chỉ có posts không cần)
   */
  protected override prepareOptions(queryOptions: any = {}) {
    const base = super.prepareOptions(queryOptions);
    return {
      ...base,
      relations: [], // PostTag không có relations cần thiết cho admin
    } as any;
  }

  /**
   * Override getOne để đảm bảo consistent
   */
  async getOne(
    where: any,
    options?: any,
  ) {
    // PostTag không có relations cần thiết
    const adminOptions = {
      ...options,
      relations: [],
    };
    return super.getOne(where, adminOptions);
  }

  /**
   * Tạo mới tag
   */
  async create(
    createDto: DeepPartial<PostTag>,
    createdBy?: number,
  ) {
    const data = { ...createDto };
    this.ensureSlug(data);
    return super.create(data, createdBy);
  }

  /**
   * Cập nhật tag
   */
  async update(
    id: number,
    updateDto: DeepPartial<PostTag>,
    updatedBy?: number,
  ) {
    const data = { ...updateDto };
    this.ensureSlug(data);
    return super.update(id, data, updatedBy);
  }
}

