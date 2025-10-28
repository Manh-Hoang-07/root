import { Controller, Get, Param, Query } from '@nestjs/common';
import { PostService } from './post.service';
import { GetPostDto } from './dtos/get-post.dto';

@Controller('public/posts')
export class PostController {
  constructor(private readonly postService: PostService) {}

  // List posts with optional relations
  @Get()
  async list(@Query() query: GetPostDto) {
    const filters: any = {};
    if (query.status) filters.status = query.status;
    if (query.search) filters.search = query.search;

    const relationsArray = query.relations ? query.relations.split(',') : [];

    const result = await this.postService.list(
      filters,
      parseInt(query.per_page) || 20,
      parseInt(query.page) || 1,
      relationsArray,
    );
    return { data: result.data, meta: result.meta };
  }

  // Get by id with optional relations
  @Get(':id')
  async get(@Param('id') id: string, @Query('relations') relations?: string) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.postService.get(id, relationsArray);
    if (!result) {
      return { data: null, message: 'Post not found' };
    }
    return { data: result };
  }

  // Get by slug with optional relations
  @Get('slug/:slug')
  async getBySlug(@Param('slug') slug: string, @Query('relations') relations?: string) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.postService.getBySlug(slug, relationsArray);
    if (!result) {
      return { data: null, message: 'Post not found' };
    }
    return { data: result };
  }
}
