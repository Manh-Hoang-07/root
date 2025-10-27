import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';
import { Post } from '../../../entities/post.entity';
import { PostCategory } from '../../../entities/post-category.entity';
import { PostTag } from '../../../entities/post-tag.entity';

@Injectable()
export class PostsService {
  constructor(
    @InjectRepository(Post)
    private readonly postRepository: Repository<Post>,
    @InjectRepository(PostCategory)
    private readonly postCategoryRepository: Repository<PostCategory>,
    @InjectRepository(PostTag)
    private readonly postTagRepository: Repository<PostTag>,
  ) {}

  // Generic list method
  private async list<T>(
    repository: Repository<T>,
    filters: any = {},
    perPage: number = 20,
    page: number = 1,
    relations: string[] = [],
  ) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const where: FindOptionsWhere<T> = {};

    if (filters.status) {
      (where as any)['status'] = filters.status;
    }

    if (filters.search) {
      const searchFields = ['name', 'title', 'email'];
      for (const field of searchFields) {
        if (this.hasProperty(repository, field)) {
          (where as any)[field] = ILike(`%${filters.search}%`);
          break;
        }
      }
    }

    const findOptions: FindManyOptions<T> = {
      where,
      order: { created_at: 'DESC' } as any,
      skip,
      take: validPerPage,
    };

    if (relations.length > 0) {
      (findOptions as any).relations = relations;
    }

    const [data, total] = await repository.findAndCount(findOptions);

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
  }

  // Check if entity has a specific property
  private hasProperty<T>(repository: Repository<T>, property: string): boolean {
    const metadata = repository.metadata;
    return metadata.columns.some(column => column.propertyName === property);
  }

  // Posts
  async getPosts(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    return this.list(this.postRepository, filters, perPage, page, relations);
  }

  async getPost(id: string, relations: string[] = []) {
    const findOptions: any = {
      where: { id: Number(id) },
    };

    if (relations.length > 0) {
      findOptions.relations = relations;
    }

    return this.postRepository.findOne(findOptions);
  }

  async createPost(createPostDto: any) {
    const { category_ids, tag_ids, ...postData } = createPostDto;
    
    const post = this.postRepository.create(postData as any);
    const savedPost = await this.postRepository.save(post);

    // Handle categories and tags if provided
    if (category_ids && category_ids.length > 0) {
      await this.postRepository.manager
        .createQueryBuilder()
        .relation(Post, 'categories')
        .of((savedPost as any).id)
        .add(category_ids);
    }

    if (tag_ids && tag_ids.length > 0) {
      await this.postRepository.manager
        .createQueryBuilder()
        .relation(Post, 'tags')
        .of((savedPost as any).id)
        .add(tag_ids);
    }

    return savedPost;
  }

  async updatePost(id: string, updatePostDto: any) {
    const { category_ids, tag_ids, ...postData } = updatePostDto;
    
    const existingPost = await this.postRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingPost) {
      return null;
    }

    const updatedPost = this.postRepository.merge(existingPost, postData as any);
    const savedPost = await this.postRepository.save(updatedPost);

    // Handle categories and tags if provided
    if (category_ids !== undefined) {
      await this.postRepository.manager
        .createQueryBuilder()
        .relation(Post, 'categories')
        .of((savedPost as any).id)
        .addAndRemove(category_ids, []);
    }

    if (tag_ids !== undefined) {
      await this.postRepository.manager
        .createQueryBuilder()
        .relation(Post, 'tags')
        .of((savedPost as any).id)
        .addAndRemove(tag_ids, []);
    }

    return savedPost;
  }

  async deletePost(id: string) {
    const existingPost = await this.postRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingPost) {
      return null;
    }

    if (this.hasProperty(this.postRepository, 'deleted_at')) {
      (existingPost as any).deleted_at = new Date();
      return this.postRepository.save(existingPost);
    } else {
      return this.postRepository.remove(existingPost);
    }
  }
}
