import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { PostCategory } from '../../../../shared/entities/post-category.entity';
import { ListService } from '../../../../common/base/services/list.service';

@Injectable()
export class PostCategoryService extends ListService<PostCategory> {
  constructor(
    @InjectRepository(PostCategory) repo: Repository<PostCategory>,
  ) {
    super(repo);
  }

  protected override prepareFilters(filters: any = {}) {
    const prepared: any = { ...filters };
    if (prepared.status === undefined) prepared.status = 'active';
    return prepared;
  }

  protected override prepareOptions(queryOptions: any = {}) {
    const base = super.prepareOptions(queryOptions);
    return {
      ...base,
      select: ['id', 'name', 'slug', 'description', 'image', 'sort_order', 'createdAt'],
      relations: [
        { name: 'parent',   select: ['id', 'name', 'slug'] },
        { name: 'children', select: ['id', 'name', 'slug'] },
      ],
      sort: base.sort ?? (queryOptions.sort_by
        ? `${queryOptions.sort_by}:${queryOptions.sort_order || 'ASC'}`
        : 'sort_order:ASC'),
    } as any;
  }
}

