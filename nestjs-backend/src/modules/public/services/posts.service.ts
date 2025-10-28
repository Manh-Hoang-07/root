import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, SelectQueryBuilder, ILike } from 'typeorm';
import { Post } from '../../../entities/post.entity';
import { PostStatus } from '../../../enums/post-status.enum';
import { BaseService } from './base.service';

@Injectable()
export class PostsService extends BaseService<Post> {
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

  // Override: Add ManyToMany relation handling
  protected addRelations(queryBuilder: SelectQueryBuilder<Post>, relations: string[]) {
    const availableRelations = this.getAvailableRelations();
    const validRelations = relations.filter(r => availableRelations.includes(r));
    
    console.log('addRelations called with:', { requested: relations, valid: validRelations });
    
    for (const relation of validRelations) {
      if (relation === 'categories') {
        console.log('Adding categories relation');
        queryBuilder
          .leftJoin('post_postcategory', 'ppc', 'ppc.post_id = entity.id')
          .leftJoinAndSelect('postcategory', 'categories', 'ppc.postcategory_id = categories.id');
      } else if (relation === 'tags') {
        console.log('Adding tags relation');
        queryBuilder
          .leftJoin('post_posttag', 'ppt', 'ppt.post_id = entity.id')
          .leftJoinAndSelect('posttag', 'tags', 'ppt.posttag_id = tags.id');
      } else {
        console.log('Adding relation:', relation);
        queryBuilder.leftJoinAndSelect(`entity.${relation}`, relation);
      }
    }
  }

  // Override: Custom list items with status filter
  protected async listItems(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    try {
      console.log('listItems started');
      const validPerPage = Math.min(perPage, 100);
      const skip = (page - 1) * validPerPage;

      // Use find() method for ManyToMany relations
      const where: any = { status: PostStatus.PUBLISHED };
      
      const findOptions: any = {
        where,
        order: { created_at: 'DESC' },
        relations: relations,
        skip,
        take: validPerPage,
      };

      if (filters.search) {
        where.name = ILike(`%${filters.search}%`);
      }
      
      console.log('findOptions:', JSON.stringify(findOptions, null, 2));
      
      const [data, total] = await this.postRepo.findAndCount(findOptions);
      
      console.log('Query executed, got', total, 'total items');
      console.log('First post data:', data[0] ? Object.keys(data[0]) : 'no data');

      return {
        data,
        meta: {
          total,
          per_page: validPerPage,
          current_page: page,
          last_page: Math.ceil(total / validPerPage),
          from: skip + 1,
          to: Math.min(skip + validPerPage, total),
        },
      };
    } catch (error) {
      console.error('Error in listItems:', error);
      throw error;
    }
  }

  // Public API methods - Override to make public
  async getAll(filters: any = {}, perPage: number = 20, page: number = 1, relations?: string[]) {
    return this.listItems(filters, perPage, page, relations || this.getIndexRelations());
  }

  async getOne(id: string, relations?: string[]) {
    const queryBuilder = this.postRepo
      .createQueryBuilder('entity')
      .where('entity.id = :id', { id: Number(id) })
      .andWhere('entity.status = :status', { status: PostStatus.PUBLISHED });

    this.addRelations(queryBuilder, relations || this.getShowRelations());
    
    return queryBuilder.getOne();
  }

  async getBySlug(slug: string, relations?: string[]) {
    const queryBuilder = this.postRepo
      .createQueryBuilder('entity')
      .where('entity.slug = :slug', { slug })
      .andWhere('entity.status = :status', { status: PostStatus.PUBLISHED });

    this.addRelations(queryBuilder, relations || this.getShowRelations());
    
    return queryBuilder.getOne();
  }

  // Public API methods with additional logic
  async getPosts(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    console.log('getPosts called with:', { filters, perPage, page, relations });
    const result = await this.getAll(filters, perPage, page, relations.length > 0 ? relations : undefined);
    console.log('getPosts result data:', result.data?.length, 'posts');
    if (result.data && result.data.length > 0) {
      console.log('First post relations:', Object.keys(result.data[0]));
      if (result.data[0].categories) {
        console.log('Categories loaded:', result.data[0].categories.length);
      }
      if (result.data[0].tags) {
        console.log('Tags loaded:', result.data[0].tags.length);
      }
    }
    return result;
  }

  async getPost(id: string, relations: string[] = []) {
    const post = await this.getOne(id, relations.length > 0 ? relations : undefined);
    if (post) {
      await super.incrementViewCount(post);
    }
    return post;
  }

  async getPostBySlug(slug: string, relations: string[] = []) {
    const post = await this.getBySlug(slug, relations.length > 0 ? relations : undefined);
    if (post) {
      await super.incrementViewCount(post);
    }
    return post;
  }
}
