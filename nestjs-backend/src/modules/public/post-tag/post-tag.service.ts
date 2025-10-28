import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { PostTag } from '../../../shared/entities/post-tag.entity';

@Injectable()
export class PostTagService {
  constructor(
    @InjectRepository(PostTag)
    private readonly tagRepository: Repository<PostTag>,
  ) {}

  async getPostTags() {
    return this.tagRepository.find({
      where: { status: 'active' } as any,
      order: { name: 'ASC' },
    });
  }

  async getPostTag(id: string) {
    return this.tagRepository.findOne({
      where: { id: Number(id), status: 'active' } as any,
    });
  }
}

