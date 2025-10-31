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
import { PostService } from '../services/post.service';
import { CreatePostDto } from '../dtos/create-post.dto';
import { UpdatePostDto } from '../dtos/update-post.dto';
import { prepareQuery } from '../../../../common/base/utils/list-query.helper';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Controller('admin/posts')
export class PostController {
  constructor(private readonly postService: PostService) {}

  @Get()
  async getList(@Query(ValidationPipe) query: any) {
    const { filters, options } = prepareQuery(query);
    const result = await this.postService.getList(filters, options);
    return ResponseUtil.transform(
      result.data,
      true,
      'Lấy danh sách bài viết thành công.',
      'SUCCESS',
      200,
      result.meta,
    );
  }

  @Get(':id')
  async getOne(@Param('id', ParseIntPipe) id: number) {
    const post = await this.postService.getOne({ id } as any);
    if (!post) {
      return ResponseUtil.notFound('Không tìm thấy bài viết.');
    }
    return ResponseUtil.success(post, 'Lấy thông tin bài viết thành công.');
  }

  @Post()
  async create(@Body(ValidationPipe) createDto: CreatePostDto) {
    const result = await this.postService.create(createDto as any);
    
    if (result.success) {
      return ResponseUtil.created(result.data, result.message);
    } else {
      return ResponseUtil.error(result.message, result.code, 400);
    }
  }

  @Put(':id')
  async update(
    @Param('id', ParseIntPipe) id: number,
    @Body(ValidationPipe) updateDto: UpdatePostDto,
  ) {
    const result = await this.postService.update(id, updateDto as any);
    
    if (result.success) {
      return ResponseUtil.updated(result.data, result.message);
    } else {
      return ResponseUtil.error(result.message, result.code, 400);
    }
  }

  @Delete(':id')
  async delete(@Param('id', ParseIntPipe) id: number) {
    const result = await this.postService.delete(id);
    
    if (result.success) {
      return ResponseUtil.deleted(result.message);
    } else {
      return ResponseUtil.error(result.message, result.code, 400);
    }
  }
}

