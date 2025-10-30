import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, SelectQueryBuilder } from 'typeorm';
import { PostTag } from '../../../../shared/entities/post-tag.entity';
import { GetTagsDto } from '../../post/dtos/get-tags.dto';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Injectable()
export class PostTagService {
  constructor(
    @InjectRepository(PostTag)
    private tagRepository: Repository<PostTag>,
  ) {}

  async findAll(dto: GetTagsDto) {
    const page = dto.page || 1;
    const limit = dto.limit || 10;
    const skip = (page - 1) * limit;

    const queryBuilder: SelectQueryBuilder<PostTag> = this.tagRepository
      .createQueryBuilder('tag')
      .where('tag.deleted_at IS NULL');

    // Apply filters
    if (dto.search) {
      queryBuilder.andWhere(
        '(tag.name LIKE :search OR tag.description LIKE :search)',
        { search: `%${dto.search}%` },
      );
    }

    if (dto.status) {
      queryBuilder.andWhere('tag.status = :status', { status: dto.status });
    } else {
      queryBuilder.andWhere('tag.status = :status', { status: 'active' });
    }

    // Apply sorting
    const sortBy = dto.sort_by || 'created_at';
    const sortOrder = dto.sort_order || 'DESC';
    queryBuilder.orderBy(`tag.${sortBy}`, sortOrder);

    // Get total count
    const total = await queryBuilder.getCount();

    // Apply pagination
    queryBuilder.skip(skip).take(limit);

    // Get tags
    const tags = await queryBuilder.getMany();

    return ResponseUtil.success(
      {
        data: tags,
        total,
        page,
        limit,
        totalPages: Math.ceil(total / limit),
      },
      'Lấy danh sách thẻ thành công.',
    );
  }

  async findOne(slug: string) {
    const tag = await this.tagRepository
      .createQueryBuilder('tag')
      .where('tag.slug = :slug', { slug })
      .andWhere('tag.status = :status', { status: 'active' })
      .andWhere('tag.deleted_at IS NULL')
      .getOne();

    if (!tag) {
      return ResponseUtil.notFound('Không tìm thấy thẻ.');
    }

    return ResponseUtil.success(tag, 'Lấy thông tin thẻ thành công.');
  }
}

