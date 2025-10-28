import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { PostTag } from '../../../shared/entities/post-tag.entity';
import { BaseService } from '../../../common/base/base-public.service';

@Injectable()
export class PostTagService extends BaseService<PostTag> {
  constructor(
    @InjectRepository(PostTag)
    tagRepository: Repository<PostTag>,
  ) {
    super(tagRepository);
  }

  protected getAvailableRelations(): string[] {
    return [];
  }

  async getPostTags() {
    return this.getSimpleList(
      { status: 'active' } as any,
      { name: 'ASC' } as any,
    );
  }

  async getPostTag(id: string) {
    return this.repository.findOne({
      where: { 
        id: Number(id),
        status: 'active'
      } as any,
    });
  }
}

