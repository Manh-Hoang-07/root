import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { PostsService } from './posts.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/posts')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class PostsController extends BaseController<any> {
  protected service = this.postsService;
  
  constructor(private readonly postsService: PostsService) {
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

  protected getCreateMethodName(): string {
    return 'create';
  }

  protected getUpdateMethodName(): string {
    return 'update';
  }

  protected getDeleteMethodName(): string {
    return 'delete';
  }

  // Custom list method với relations support
  @Get()
  async list(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
    @Query('relations') relations?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    const relationsArray = relations ? relations.split(',') : [];

    const result = await this.postsService.list(
      filters,
      parseInt(perPage),
      parseInt(page),
      relationsArray,
    );
    return this.handleListResponse(result);
  }

  // Custom get method với relations support
  @Get(':id')
  async get(
    @Param('id') id: string,
    @Query('relations') relations?: string,
  ) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.postsService.get(id, relationsArray);
    return this.handleResponse(result, 'Post not found');
  }
}
