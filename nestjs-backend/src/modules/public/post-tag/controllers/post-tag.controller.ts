import {
  Controller,
  Get,
  Query,
  Param,
  ValidationPipe,
} from '@nestjs/common';
import { Public } from '../../../../common/decorators/public.decorator';
import { PostTagService } from '../services/post-tag.service';
import { GetTagsDto } from '../dtos/get-tags.dto';
import { GetTagDto } from '../dtos/get-tag.dto';
import { prepareQuery } from '../../../../common/base/utils/list-query.helper';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Controller('public/post-tags')
@Public()
export class PostTagController {
  constructor(private readonly postTagService: PostTagService) {}

  @Get()
  async findList(@Query(ValidationPipe) query: GetTagsDto) {
    const { filters, options } = prepareQuery(query);
    const result = await this.postTagService.getList(filters as any, options as any);
    return ResponseUtil.transform(
      result.data,
      true,
      'Lấy danh sách thẻ thành công.',
      'SUCCESS',
      200,
      result.meta,
    );
  }

  @Get(':slug')
  async findBySlug(@Param(ValidationPipe) params: GetTagDto) {
    const tag = await this.postTagService.getOne(
      { slug: params.slug, status: 'active' } as any,
    );
    if (!tag || (tag as any).deletedAt) {
      return ResponseUtil.notFound('Không tìm thấy thẻ.');
    }
    return ResponseUtil.success(tag, 'Lấy thông tin thẻ thành công.');
  }
}

