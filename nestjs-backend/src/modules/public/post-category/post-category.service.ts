import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { PostCategory } from '../../../shared/entities/post-category.entity';

@Injectable()
export class PostCategoryService {
  constructor(
    @InjectRepository(PostCategory)
    private readonly categoryRepository: Repository<PostCategory>,
  ) {}

  async getPostCategories() {
    return this.categoryRepository.find({
      where: { status: 'active' } as any,
      order: { name: 'ASC' },
    });
  }

  async getPostCategory(id: string) {
    return this.categoryRepository.findOne({
      where: { id: Number(id), status: 'active' } as any,
    });
  }
}

