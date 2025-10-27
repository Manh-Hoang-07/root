import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { PostTagsService } from '../services/post-tags.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/post-tags')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class PostTagsController {
  constructor(private readonly postTagsService: PostTagsService) {}

  @Get()
  async getPostTags(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.postTagsService.getPostTags(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getPostTag(@Param('id') id: string) {
    const result = await this.postTagsService.getPostTag(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Post tag not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post()
  async createPostTag(@Body() createPostTagDto: any) {
    const result = await this.postTagsService.createPostTag(createPostTagDto);
    return { data: result };
  }

  @Put(':id')
  async updatePostTag(@Param('id') id: string, @Body() updatePostTagDto: any) {
    const result = await this.postTagsService.updatePostTag(id, updatePostTagDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Post tag not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deletePostTag(@Param('id') id: string) {
    const result = await this.postTagsService.deletePostTag(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Post tag not found',
        data: null,
      };
    }

    return { message: 'Post tag deleted successfully' };
  }
}
