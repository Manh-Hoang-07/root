import { Controller, Get, Param } from '@nestjs/common';
import { PostCategoryService } from './post-category.service';

@Controller('public/post-categories')
export class PostCategoryController {
  constructor(private readonly postCategoryService: PostCategoryService) {}

  @Get()
  async list() {
    const data = await this.postCategoryService.getPostCategories();
    return { data };
  }

  @Get(':id')
  async get(@Param('id') id: string) {
    const data = await this.postCategoryService.getPostCategory(id);
    return { data };
  }
}
