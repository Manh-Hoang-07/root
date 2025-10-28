import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';
import { PostTag } from '../../../entities/post-tag.entity';

@Injectable()
export class PostTagsService {
  constructor(
    @InjectRepository(PostTag)
    private readonly postTagRepository: Repository<PostTag>,
  ) {}

  // Generic list method
  private async list<T>(
    repository: Repository<T>,
    filters: any = {},
    perPage: number = 20,
    page: number = 1,
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

  // Post Tags
  async getPostTags(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.list(this.postTagRepository, filters, perPage, page);
  }

  async getPostTag(id: string) {
    return this.postTagRepository.findOne({
      where: { id: Number(id) } as any,
    });
  }

  async createPostTag(createPostTagDto: any) {
    const postTag = this.postTagRepository.create(createPostTagDto as any);
    return this.postTagRepository.save(postTag);
  }

  async updatePostTag(id: string, updatePostTagDto: any) {
    const existingPostTag = await this.postTagRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingPostTag) {
      return null;
    }

    const updatedPostTag = this.postTagRepository.merge(existingPostTag, updatePostTagDto as any);
    return this.postTagRepository.save(updatedPostTag);
  }

  async deletePostTag(id: string) {
    const existingPostTag = await this.postTagRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingPostTag) {
      return null;
    }

    if (this.hasProperty(this.postTagRepository, 'deleted_at')) {
      (existingPostTag as any).deleted_at = new Date();
      return this.postTagRepository.save(existingPostTag);
    } else {
      return this.postTagRepository.remove(existingPostTag);
    }
  }
}
