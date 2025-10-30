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
import { prepareQuery } from '../../../../common/base/utils/list-query.helper';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Controller('public/post-categories')
@Public()
export class PostCategoryController {
  constructor(private readonly postCategoryService: PostCategoryService) {}

  @Get()
  async findList(@Query(ValidationPipe) query: GetCategoriesDto) {
    const { filters, options } = prepareQuery(query);
    const result = await this.postCategoryService.getList(filters as any, options as any);
    return ResponseUtil.transform(
      result.data,
      true,
      'Lấy danh sách danh mục thành công.',
      'SUCCESS',
      200,
      result.meta,
    );
  }

  @Get(':slug')
  async findBySlug(@Param('slug') slug: string) {
    const category = await this.postCategoryService.getOne(
      { slug, status: 'active' } as any,
      { relations: [
        { name: 'parent',   select: ['id', 'name', 'slug'] },
        { name: 'children', select: ['id', 'name', 'slug'] },
      ] } as any,
    );
    if (!category || (category as any).deletedAt) {
      return ResponseUtil.notFound('Không tìm thấy danh mục.');
    }
    return ResponseUtil.success(category, 'Lấy thông tin danh mục thành công.');
  }
}

