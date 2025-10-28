import { Controller, Get, Param, Query } from '@nestjs/common';
import { PostService } from './post.service';
import { GetPostDto } from './dtos/get-post.dto';
import { BaseController } from '../../../common/base/base.controller';

@Controller('public/posts')
export class PostController extends BaseController<any> {
  protected service = this.postService;
  
  constructor(private readonly postService: PostService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Post';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'list';
  }

  protected getGetMethodName(): string {
    return 'get';
  }

  // Disable các method khác
  protected getCreateMethodName(): string {
    return null; // Disable create
  }

  protected getUpdateMethodName(): string {
    return null; // Disable update
  }

  protected getDeleteMethodName(): string {
    return null; // Disable delete
  }

  // Custom list method với relations support
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

    return this.handleListResponse(result);
  }

  // Custom get method với relations support
  @Get(':id')
  async get(@Param('id') id: string, @Query('relations') relations?: string) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.postService.get(id, relationsArray);
    return this.handleResponse(result, 'Post not found');
  }

  // Custom slug endpoint
  @Get('slug/:slug')
  async getBySlug(@Param('slug') slug: string, @Query('relations') relations?: string) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.postService.getBySlug(slug, relationsArray);
    return this.handleResponse(result, 'Post not found');
  }
}
