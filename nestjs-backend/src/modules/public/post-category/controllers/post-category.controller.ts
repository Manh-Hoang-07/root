import {
  Controller,
  Get,
  Query,
  Param,
  ValidationPipe,
} from '@nestjs/common';
import { Public } from '../../../../common/decorators/public.decorator';
import { PostCategoryService } from '../services/post-category.service';
import { GetCategoriesDto } from '../../post/dtos/get-categories.dto';

@Controller('public/post-categories')
@Public()
export class PostCategoryController {
  constructor(private readonly postCategoryService: PostCategoryService) {}

  @Get()
  async findAll(@Query(ValidationPipe) query: GetCategoriesDto) {
    return this.postCategoryService.findAll(query);
  }

  @Get(':slug')
  async findOne(@Param('slug') slug: string) {
    return this.postCategoryService.findOne(slug);
  }
}

