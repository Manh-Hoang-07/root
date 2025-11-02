import {
  Controller,
  Get,
  Query,
  Param,
  ValidationPipe,
} from '@nestjs/common';
import { PostService } from '../services/post.service';
import { prepareQuery } from '../../../../common/base/utils/list-query.helper';
import { GetPostsDto } from '../dtos/get-posts.dto';
import { GetPostDto } from '../dtos/get-post.dto';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Controller('public/posts')
export class PostController {
  constructor(private readonly postService: PostService) {}

  @Get()
  async getList(@Query(ValidationPipe) query: GetPostsDto) {
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

  @Get('featured')
  async getFeatured(@Query('limit') limit?: string) {
    const { filters, options } = prepareQuery({
      filters: { is_featured: true },
      options: { page: 1, limit: limit ? parseInt(limit, 10) : 5 }
    });
    const result = await this.postService.getList(filters, options);
    return ResponseUtil.success(result.data, 'Lấy bài viết nổi bật thành công.');
  }

  @Get(':slug')
  async getBySlug(@Param(ValidationPipe) params: GetPostDto) {
    const post = await this.postService.getOne(
      { slug: params.slug, status: 'published' } as any,
    );
    if (!post || (post as any).deletedAt) {
      return ResponseUtil.notFound('Không tìm thấy bài viết.');
    }
    
    // Tăng view count (không chặn response nếu lỗi)
    await this.postService.incrementViewCount((post as any).id);
    
    return ResponseUtil.success(post, 'Lấy thông tin bài viết thành công.');
  }
}

