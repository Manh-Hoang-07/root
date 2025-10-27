import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { PostCategoriesService } from '../services/post-categories.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/post-categories')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class PostCategoriesController {
  constructor(private readonly postCategoriesService: PostCategoriesService) {}

  @Get()
  async getPostCategories(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.postCategoriesService.getPostCategories(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getPostCategory(@Param('id') id: string) {
    const result = await this.postCategoriesService.getPostCategory(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Post category not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post()
  async createPostCategory(@Body() createPostCategoryDto: any) {
    const result = await this.postCategoriesService.createPostCategory(createPostCategoryDto);
    return { data: result };
  }

  @Put(':id')
  async updatePostCategory(@Param('id') id: string, @Body() updatePostCategoryDto: any) {
    const result = await this.postCategoriesService.updatePostCategory(id, updatePostCategoryDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Post category not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete(':id')
  async deletePostCategory(@Param('id') id: string) {
    const result = await this.postCategoriesService.deletePostCategory(id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Post category not found',
        data: null,
      };
    }

    return { message: 'Post category deleted successfully' };
  }
}
