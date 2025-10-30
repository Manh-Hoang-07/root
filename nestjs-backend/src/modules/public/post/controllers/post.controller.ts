import {
  Controller,
  Get,
  Query,
  Param,
  ValidationPipe,
} from '@nestjs/common';
import { Public } from '../../../../common/decorators/public.decorator';
import { PostService } from '../services/post.service';
import { GetPostsDto } from '../dtos/get-posts.dto';
import { GetPostDto } from '../dtos/get-post.dto';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Controller('public/posts')
@Public()
export class PostController {
  constructor(private readonly postService: PostService) {}

  @Get()
  async findAll(@Query(ValidationPipe) query: GetPostsDto) {
    const filters: any = {
      ...(query.is_featured !== undefined && { is_featured: query.is_featured }),
      ...(query.is_pinned !== undefined && { is_pinned: query.is_pinned }),
    };

    const options: any = {
      page: query.page || 1,
      limit: query.limit || 10,
      relations: [
        { name: 'primary_category', select: ['id', 'name', 'slug', 'description'] },
        { name: 'categories', select: ['id', 'name', 'slug', 'description'] },
        { name: 'tags', select: ['id', 'name', 'slug', 'description'] }
      ],
      sort: query.sort_by ? `${query.sort_by}:${query.sort_order || 'DESC'}` : 'id:DESC',
    };

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
    const limitNum = limit ? parseInt(limit, 10) : 5;
    const result = await this.postService.getList(
      { is_featured: true } as any,
      { page: 1, limit: limitNum, relations: [
        { name: 'primary_category', select: ['id', 'name', 'slug', 'description'] },
        { name: 'categories', select: ['id', 'name', 'slug', 'description'] },
        { name: 'tags', select: ['id', 'name', 'slug', 'description'] }
      ], sort: 'id:DESC' },
    );
    return ResponseUtil.success(result.data, 'Lấy bài viết nổi bật thành công.');
  }

  @Get(':slug')
  async findOne(@Param(ValidationPipe) params: GetPostDto) {
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

