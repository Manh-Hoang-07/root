import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { PostTag } from '../../../entities/post-tag.entity';
import { BaseService } from '../base.service';

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

  async list() {
    return this.getSimpleList(
      { status: 'active' } as any,
      { name: 'ASC' } as any,
    );
  }

  async getOne(id: string) {
    return this.repository.findOne({
      where: { 
        id: Number(id),
        status: 'active'
      } as any,
    });
  }
}

