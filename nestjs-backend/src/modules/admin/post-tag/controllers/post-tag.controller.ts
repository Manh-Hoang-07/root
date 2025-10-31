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
import { PostTagService } from '../services/post-tag.service';
import { CreatePostTagDto } from '../dtos/create-post-tag.dto';
import { UpdatePostTagDto } from '../dtos/update-post-tag.dto';
import { prepareQuery } from '../../../../common/base/utils/list-query.helper';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Controller('admin/post-tags')
export class PostTagController {
  constructor(private readonly postTagService: PostTagService) {}

  @Get()
  async getList(@Query(ValidationPipe) query: any) {
    const { filters, options } = prepareQuery(query);
    const result = await this.postTagService.getList(filters, options);
    return ResponseUtil.transform(
      result.data,
      true,
      'Lấy danh sách thẻ thành công.',
      'SUCCESS',
      200,
      result.meta,
    );
  }

  @Get(':id')
  async getOne(@Param('id', ParseIntPipe) id: number) {
    const tag = await this.postTagService.getOne({ id } as any);
    if (!tag) {
      return ResponseUtil.notFound('Không tìm thấy thẻ.');
    }
    return ResponseUtil.success(tag, 'Lấy thông tin thẻ thành công.');
  }

  @Post()
  async create(@Body(ValidationPipe) createDto: CreatePostTagDto) {
    const result = await this.postTagService.create(createDto as any);
    
    if (result.success) {
      return ResponseUtil.created(result.data, result.message);
    } else {
      return ResponseUtil.error(result.message, result.code, 400);
    }
  }

  @Put(':id')
  async update(
    @Param('id', ParseIntPipe) id: number,
    @Body(ValidationPipe) updateDto: UpdatePostTagDto,
  ) {
    const result = await this.postTagService.update(id, updateDto as any);
    
    if (result.success) {
      return ResponseUtil.updated(result.data, result.message);
    } else {
      return ResponseUtil.error(result.message, result.code, 400);
    }
  }

  @Delete(':id')
  async delete(@Param('id', ParseIntPipe) id: number) {
    const result = await this.postTagService.delete(id);
    
    if (result.success) {
      return ResponseUtil.deleted(result.message);
    } else {
      return ResponseUtil.error(result.message, result.code, 400);
    }
  }
}

