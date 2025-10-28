import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { PostCategory } from '../../../shared/entities/post-category.entity';
import { BaseService } from '../../../common/base/base-public.service';

@Injectable()
export class PostCategoryService extends BaseService<PostCategory> {
  constructor(
    @InjectRepository(PostCategory)
    categoryRepository: Repository<PostCategory>,
  ) {
    super(categoryRepository);
  }

  protected getAvailableRelations(): string[] {
    return [];
  }

  // Get simple list
  async getPostCategories() {
    return this.getSimpleList(
      { status: 'active' } as any,
      { name: 'ASC' } as any,
    );
  }

  async getPostCategory(id: string) {
    return this.repository.findOne({
      where: { 
        id: Number(id),
        status: 'active'
      } as any,
    });
  }
}

