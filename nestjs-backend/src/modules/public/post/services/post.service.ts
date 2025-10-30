import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Post } from '../../../../shared/entities/post.entity';
import { ListService } from '../../../../common/base/services/list.service';

@Injectable()
export class PostService extends ListService<Post> {
  constructor(
    @InjectRepository(Post) repo: Repository<Post>,
  ) {
    super(repo);
  }

  protected override prepareFilters(filters: any = {}) {
    return {
      ...filters,
      status: 'published',
    };
  }

  protected override prepareOptions(queryOptions: any = {}) {
    const base = super.prepareOptions(queryOptions);
    return {
      ...base,
      select: [
        'id',
        'name',
        'slug',
        'excerpt',
        'image',
        'cover_image',
        'published_at',
        'view_count',
        'createdAt',
      ],
      relations: [
        { name: 'primary_category', select: ['id', 'name', 'slug', 'description'], where: { status: 'active' } },
        { name: 'categories',       select: ['id', 'name', 'slug', 'description'], where: { status: 'active' } },
        { name: 'tags',             select: ['id', 'name', 'slug', 'description'], where: { status: 'active' } }
      ],
    } as any;
  }
}
