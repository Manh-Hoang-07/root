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

  // Force default filters for public posts
  protected prepareFilters(filters?: Filters<Post>, options?: Options): any {
    // Always filter for published posts that are not soft-deleted
    const baseFilters = {
      status: 'published',
      ...(filters || {}),
    };
    
    return baseFilters;
  }
}
