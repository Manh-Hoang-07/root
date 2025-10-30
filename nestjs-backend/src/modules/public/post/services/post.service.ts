import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Post } from '../../../../shared/entities/post.entity';
import { GetPostDto } from '../dtos/get-post.dto';
import { ResponseUtil } from '../../../../common/utils/response.util';
import { ListService } from '../../../../common/base/services/list.service';
import { Filters, Options } from '../../../../common/base/interfaces/list.interface';

@Injectable()
export class PostService extends ListService<Post> {
  constructor(
    @InjectRepository(Post) repo: Repository<Post>,
  ) {
    super(repo);
  }

  protected override prepareOptions(queryOptions: any = {}) {
    const base = super.prepareOptions(queryOptions);
    return {
      ...base,
      relations: [
        { name: 'primary_category', select: ['id', 'name', 'slug', 'description'], where: { status: 'active' } },
        { name: 'categories',       select: ['id', 'name', 'slug', 'description'], where: { status: 'active' } },
        { name: 'tags',             select: ['id', 'name', 'slug', 'description'], where: { status: 'active' } }
      ],
    } as any;
  }
}
