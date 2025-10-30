import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, SelectQueryBuilder } from 'typeorm';
import { PostCategory } from '../../../../shared/entities/post-category.entity';
import { GetCategoriesDto } from '../../post/dtos/get-categories.dto';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Injectable()
export class PostCategoryService {
  constructor(
    @InjectRepository(PostCategory)
    private categoryRepository: Repository<PostCategory>,
  ) {}

  async findAll(dto: GetCategoriesDto) {
    const page = dto.page || 1;
    const limit = dto.limit || 10;
    const skip = (page - 1) * limit;

    const queryBuilder: SelectQueryBuilder<PostCategory> =
      this.categoryRepository
        .createQueryBuilder('category')
        .leftJoin('category.parent', 'parent')
        .addSelect(['parent.id', 'parent.name', 'parent.slug'])
        .leftJoin('category.children', 'children')
        .addSelect(['children.id', 'children.name', 'children.slug'])
        .where('category.deleted_at IS NULL');

    // Apply filters
    if (dto.search) {
      queryBuilder.andWhere(
        '(category.name LIKE :search OR category.description LIKE :search)',
        { search: `%${dto.search}%` },
      );
    }

    if (dto.parent_id !== undefined) {
      queryBuilder.andWhere('category.parent_id = :parent_id', {
        parent_id: dto.parent_id,
      });
    } else {
      // Only get top-level categories by default (parent_id is null)
      queryBuilder.andWhere('category.parent_id IS NULL');
    }

    if (dto.status) {
      queryBuilder.andWhere('category.status = :status', { status: dto.status });
    } else {
      queryBuilder.andWhere('category.status = :status', { status: 'active' });
    }

    // Apply sorting
    const sortBy = dto.sort_by || 'sort_order';
    const sortOrder = dto.sort_order || 'ASC';
    queryBuilder.orderBy(`category.${sortBy}`, sortOrder);

    // Get total count
    const total = await queryBuilder.getCount();

    // Apply pagination
    queryBuilder.skip(skip).take(limit);

    // Get categories
    const categories = await queryBuilder.getMany();

    return ResponseUtil.success(
      {
        data: categories,
        total,
        page,
        limit,
        totalPages: Math.ceil(total / limit),
      },
      'Lấy danh sách danh mục thành công.',
    );
  }

  async findOne(slug: string) {
    const category = await this.categoryRepository
      .createQueryBuilder('category')
      .leftJoin('category.parent', 'parent')
      .addSelect(['parent.id', 'parent.name', 'parent.slug'])
      .leftJoin('category.children', 'children')
      .addSelect(['children.id', 'children.name', 'children.slug'])
      .where('category.slug = :slug', { slug })
      .andWhere('category.status = :status', { status: 'active' })
      .andWhere('category.deleted_at IS NULL')
      .getOne();

    if (!category) {
      return ResponseUtil.notFound('Không tìm thấy danh mục.');
    }

    return ResponseUtil.success(category, 'Lấy thông tin danh mục thành công.');
  }
}

