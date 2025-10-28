import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, ILike } from 'typeorm';
import { Post } from '../../../shared/entities/post.entity';
import { PostStatus } from '../../../shared/enums/post-status.enum';
import { BaseService } from '../../../common/base/base-public.service';

@Injectable()
export class PostService extends BaseService<Post> {
  private readonly postRepo: Repository<Post>;

  constructor(
    @InjectRepository(Post)
    postRepository: Repository<Post>,
  ) {
    super(postRepository);
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
      where.name = ILike(`%${filters.search}%`);
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

  // Public API methods - Override to make public
  async getAll(filters: any = {}, perPage: number = 20, page: number = 1, relations?: string[]) {
    return super.getAll(filters, perPage, page, relations);
  }

  // Public API methods with additional logic
  async getPosts(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    return this.getAll(filters, perPage, page, relations.length > 0 ? relations : undefined);
  }

  async getPost(id: string, relations: string[] = []) {
    const post = await this.getOne(id, relations.length > 0 ? relations : undefined);
    if (post) {
      await super.incrementViewCount(post);
    }
    return post;
  }

  async getBySlug(slug: string, relations: string[] = []) {
    const post = await super.getBySlug(slug, relations.length > 0 ? relations : undefined);
    if (post) {
      await super.incrementViewCount(post);
    }
    return post;
  }
}

