import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { PostsService } from '../services/posts.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/posts')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class PostsController {
  constructor(private readonly postsService: PostsService) {}

  @Get()
  async getPosts(
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

    return this.postsService.getPosts(
      filters,
      parseInt(perPage),
      parseInt(page),
      relationsArray,
    );
  }

  @Get(':id')
  async getPost(
    @Param('id') id: string,
    @Query('relations') relations?: string,
  ) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.postsService.getPost(id, relationsArray);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Post not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post()
  async createPost(@Body() createPostDto: any) {
    const result = await this.postsService.createPost(createPostDto);
    return { data: result };
  }

  @Put(':id')
  async updatePost(@Param('id') id: string, @Body() updatePostDto: any) {
    const result = await this.postsService.updatePost(id, updatePostDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Post not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deletePost(@Param('id') id: string) {
    const result = await this.postsService.deletePost(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Post not found',
        data: null,
      };
    }

    return { message: 'Post deleted successfully' };
  }
}
