import {
  Controller,
  Get,
  Query,
  Param,
  ValidationPipe,
} from '@nestjs/common';
import { Public } from '../../../../common/decorators/public.decorator';
import { PostService } from '../services/post.service';
import { prepareQuery } from '../../../../common/base/utils/list-query.helper';
import { GetPostsDto } from '../dtos/get-posts.dto';
import { GetPostDto } from '../dtos/get-post.dto';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Controller('public/posts')
@Public()
export class PostController {
  constructor(private readonly postService: PostService) {}

  @Get()
  async findList(@Query(ValidationPipe) query: GetPostsDto) {
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
  async findBySlug(@Param(ValidationPipe) params: GetPostDto) {
    const post = await this.postService.getOne(
      { slug: params.slug, status: 'published' } as any,
    );
    if (!post || (post as any).deletedAt) {
      return ResponseUtil.notFound('Không tìm thấy bài viết.');
    }
    // tăng view
    await (this.postService as any)['repository']
      .createQueryBuilder()
      .update('posts')
      .set({ view_count: () => 'view_count + 1' })
      .where('id = :id', { id: (post as any).id })
      .execute()
      .catch(() => undefined);
    return ResponseUtil.success(post, 'Lấy thông tin bài viết thành công.');
  }
}

