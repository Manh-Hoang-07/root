import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, Like } from 'typeorm';
import { Post } from '../../../shared/entities/post.entity';
import { PostStatus } from '../../../shared/enums/post-status.enum';

@Injectable()
export class PostService {
  private readonly postRepo: Repository<Post>;

  constructor(
    @InjectRepository(Post)
    postRepository: Repository<Post>,
  ) {
    this.postRepo = postRepository;
  }

  // Metadata declarations
  protected getRelations(): string[] {
    return ['categories', 'tags'];
  }

  protected getAvailableRelations(): string[] {
    return ['categories', 'tags', 'createdUser', 'updatedUser'];
  }

  protected getIndexRelations(): string[] {
    return ['categories', 'tags'];
  }

  protected getShowRelations(): string[] {
    return ['categories', 'tags', 'createdUser', 'updatedUser'];
  }

  // Override: Add status filter to base query
  protected buildBaseQuery(filters: any = {}): any {
    const where: any = { status: PostStatus.PUBLISHED };
    
    if (filters.search) {
      where.name = Like(`%${filters.search}%`);
    }
    
    return where;
  }

  async getOne(id: string, relations?: string[]) {
    return this.postRepo.findOne({
      where: { 
        id: Number(id),
        status: PostStatus.PUBLISHED
      },
      relations: relations || this.getShowRelations(),
    });
  }

  async getBySlug(slug: string, relations?: string[]) {
    return this.postRepo.findOne({
      where: { 
        slug,
        status: PostStatus.PUBLISHED
      },
      relations: relations || this.getShowRelations(),
    });
  }

  async list(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    const [items, total] = await this.postRepo.findAndCount({
      where: this.buildBaseQuery(filters),
      relations: relations.length > 0 ? relations : this.getIndexRelations(),
      take: perPage,
      skip: (page - 1) * perPage,
      order: { id: 'DESC' },
    });
    return {
      data: items,
      meta: {
        total,
        per_page: perPage,
        current_page: page,
        last_page: Math.ceil(total / perPage),
      },
    };
  }

  async get(id: string, relations: string[] = []) {
    const post = await this.getOne(id, relations.length > 0 ? relations : undefined);
    if (post) {
      await this.postRepo.update({ id: post.id }, { view_count: (post.view_count || 0) + 1 });
    }
    return post;
  }
}

