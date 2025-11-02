import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, In, DeepPartial } from 'typeorm';
import { Post } from '../../../../shared/entities/post.entity';
import { PostCategory } from '../../../../shared/entities/post-category.entity';
import { PostTag } from '../../../../shared/entities/post-tag.entity';
import { CrudService } from '../../../../common/base/services/crud.service';

@Injectable()
export class PostService extends CrudService<Post> {
  private get categoryRepo(): Repository<PostCategory> {
    return this.repository.manager.getRepository(PostCategory);
  }

  private get tagRepo(): Repository<PostTag> {
    return this.repository.manager.getRepository(PostTag);
  }

  constructor(
    @InjectRepository(Post) repo: Repository<Post>,
  ) {
    super(repo);
  }

  /**
   * Override để load relations trong admin
   */
  protected override prepareOptions(queryOptions: any = {}) {
    const base = super.prepareOptions(queryOptions);
    return {
      ...base,
      relations: [
        { name: 'primary_category', select: ['id', 'name', 'slug'] },
        { name: 'categories', select: ['id', 'name', 'slug'] },
        { name: 'tags', select: ['id', 'name', 'slug'] },
      ],
    } as any;
  }

  /**
   * Override getOne để load relations
   */
  async getOne(
    where: any,
    options?: any,
  ) {
    // Đảm bảo load relations trong admin
    const adminOptions = {
      ...options,
      relations: [
        { name: 'primary_category', select: ['id', 'name', 'slug'] },
        { name: 'categories', select: ['id', 'name', 'slug'] },
        { name: 'tags', select: ['id', 'name', 'slug'] },
      ],
    };
    return super.getOne(where, adminOptions);
  }

  /**
   * Hook trước khi tạo - xử lý slug và quan hệ
   */
  protected async beforeCreate(entity: Post, createDto: DeepPartial<Post>): Promise<boolean> {
    await this.ensureSlug(createDto);
    
    // Tối ưu: Load tags và categories trước khi save để chỉ save một lần
    const tagIds = (createDto as any).tag_ids as number[] | undefined;
    const categoryIds = (createDto as any).category_ids as number[] | undefined;
    const hasTagIds = tagIds != null && Array.isArray(tagIds) && tagIds.length > 0;
    const hasCategoryIds = categoryIds != null && Array.isArray(categoryIds) && categoryIds.length > 0;

    if (hasTagIds || hasCategoryIds) {
      const [tags, categories] = await Promise.all([
        hasTagIds ? this.tagRepo.find({ where: { id: In(tagIds!) } }) : Promise.resolve([]),
        hasCategoryIds ? this.categoryRepo.find({ where: { id: In(categoryIds!) } }) : Promise.resolve([]),
      ]);
      
      // Gán quan hệ vào createDto để entity được tạo với relations đầy đủ
      (createDto as any).tags = tags;
      (createDto as any).categories = categories;
    }
    
    // Dọn dẹp trường quan hệ dạng IDs khỏi DTO trước khi persist
    delete (createDto as any).tag_ids;
    delete (createDto as any).category_ids;
    return true;
  }

  /**
   * Hook trước khi cập nhật - xử lý slug
   */
  protected async beforeUpdate(entity: Post, updateDto: DeepPartial<Post>): Promise<boolean> {
    await this.ensureSlug(updateDto, entity.id, entity.slug);
    if ('tag_ids' in (updateDto as any)) {
      delete (updateDto as any).tag_ids;
    }
    if ('category_ids' in (updateDto as any)) {
      delete (updateDto as any).category_ids;
    }
    return true;
  }

  /**
   * Sau khi tạo: không cần làm gì vì relations đã được set trong beforeCreate
   */
  protected async afterCreate(entity: Post, createDto: DeepPartial<Post>): Promise<void> {
    // Relations đã được set trong beforeCreate, không cần save lại
  }

  /**
   * Sau khi cập nhật: sync quan hệ nếu field ids được gửi lên
   */
  protected async afterUpdate(entity: Post, updateDto: DeepPartial<Post>): Promise<void> {
    const tagIdsProvided = (updateDto as any).tag_ids !== undefined;
    const categoryIdsProvided = (updateDto as any).category_ids !== undefined;
    if (!tagIdsProvided && !categoryIdsProvided) return;

    let tags = entity.tags || [];
    let categories = entity.categories || [];

    const promises: Promise<any>[] = [];

    if (tagIdsProvided) {
      const tagIds = (updateDto as any).tag_ids as number[] | null | undefined;
      if (tagIds != null && Array.isArray(tagIds) && tagIds.length > 0) {
        promises.push(this.tagRepo.find({ where: { id: In(tagIds) } }).then(res => { tags = res; }));
      } else {
        tags = [];
      }
    }

    if (categoryIdsProvided) {
      const categoryIds = (updateDto as any).category_ids as number[] | null | undefined;
      if (categoryIds != null && Array.isArray(categoryIds) && categoryIds.length > 0) {
        promises.push(this.categoryRepo.find({ where: { id: In(categoryIds) } }).then(res => { categories = res; }));
      } else {
        categories = [];
      }
    }

    if (promises.length > 0) await Promise.all(promises);

    if (tagIdsProvided) entity.tags = tags;
    if (categoryIdsProvided) entity.categories = categories;
    await this.repository.save(entity);
  }

}


