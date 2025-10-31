import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Body,
  Param,
  Query,
  ParseIntPipe,
  ValidationPipe,
} from '@nestjs/common';
import { PostCategoryService } from '../services/post-category.service';
import { CreatePostCategoryDto } from '../dtos/create-post-category.dto';
import { UpdatePostCategoryDto } from '../dtos/update-post-category.dto';
import { prepareQuery } from '../../../../common/base/utils/list-query.helper';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Controller('admin/post-categories')
export class PostCategoryController {
  constructor(private readonly postCategoryService: PostCategoryService) {}

  @Get()
  async getList(@Query(ValidationPipe) query: any) {
    const { filters, options } = prepareQuery(query);
    const result = await this.postCategoryService.getList(filters, options);
    return ResponseUtil.transform(
      result.data,
      true,
      'Lấy danh sách danh mục thành công.',
      'SUCCESS',
      200,
      result.meta,
    );
  }

  @Get(':id')
  async getOne(@Param('id', ParseIntPipe) id: number) {
    const category = await this.postCategoryService.getOne({ id } as any);
    if (!category) {
      return ResponseUtil.notFound('Không tìm thấy danh mục.');
    }
    return ResponseUtil.success(category, 'Lấy thông tin danh mục thành công.');
  }

  @Post()
  async create(@Body(ValidationPipe) createDto: CreatePostCategoryDto) {
    const result = await this.postCategoryService.create(createDto as any);
    
    if (result.success) {
      return ResponseUtil.created(result.data, result.message);
    } else {
      return ResponseUtil.error(result.message, result.code, 400);
    }
  }

  @Put(':id')
  async update(
    @Param('id', ParseIntPipe) id: number,
    @Body(ValidationPipe) updateDto: UpdatePostCategoryDto,
  ) {
    const result = await this.postCategoryService.update(id, updateDto as any);
    
    if (result.success) {
      return ResponseUtil.updated(result.data, result.message);
    } else {
      return ResponseUtil.error(result.message, result.code, 400);
    }
  }

  @Delete(':id')
  async delete(@Param('id', ParseIntPipe) id: number) {
    const result = await this.postCategoryService.delete(id);
    
    if (result.success) {
      return ResponseUtil.deleted(result.message);
    } else {
      return ResponseUtil.error(result.message, result.code, 400);
    }
  }
}

