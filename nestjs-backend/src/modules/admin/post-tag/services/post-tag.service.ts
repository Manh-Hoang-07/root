import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, DeepPartial } from 'typeorm';
import { PostTag } from '../../../../shared/entities/post-tag.entity';
import { CrudService } from '../../../../common/base/services/crud.service';

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
   * Hook trước khi tạo - xử lý slug
   */
  protected async beforeCreate(entity: PostTag, createDto: DeepPartial<PostTag>): Promise<boolean> {
    await this.ensureSlug(createDto);
    return true;
  }

  /**
   * Hook trước khi cập nhật - xử lý slug
   */
  protected async beforeUpdate(entity: PostTag, updateDto: DeepPartial<PostTag>): Promise<boolean> {
    await this.ensureSlug(updateDto, entity.id, entity.slug);
    return true;
  }
}

