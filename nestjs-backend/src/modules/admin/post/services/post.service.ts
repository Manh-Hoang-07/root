import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, In } from 'typeorm';
import { Post } from '../../../../shared/entities/post.entity';
import { PostCategory } from '../../../../shared/entities/post-category.entity';
import { PostTag } from '../../../../shared/entities/post-tag.entity';
import { CrudService } from '../../../../common/base/services/crud.service';
import { DeepPartial } from 'typeorm';
import { ApiResponse } from '../../../../common/utils/response.util';

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
   * Tạo mới post với xử lý tags và categories
   */
  async create(
    createDto: DeepPartial<Post> & { tag_ids?: number[]; category_ids?: number[] },
    createdBy?: number,
  ): Promise<ApiResponse<Post | null>> {
    const data = { ...createDto };
    const tagIds = data.tag_ids;
    const categoryIds = data.category_ids;
    
    // Xóa tag_ids và category_ids khỏi data trước khi tạo entity
    delete data.tag_ids;
    delete data.category_ids;

    // Đảm bảo slug
    this.ensureSlug(data);

    // Gọi parent create
    const result = await super.create(data as DeepPartial<Post>, createdBy);

    // Xử lý ManyToMany relations sau khi tạo thành công
    if (result.success && result.data && (tagIds || categoryIds)) {
      const post = result.data as Post;
      
      // Kiểm tra và xử lý tagIds và categoryIds (null, undefined, hoặc mảng rỗng)
      const hasTagIds = tagIds != null && Array.isArray(tagIds) && tagIds.length > 0;
      const hasCategoryIds = categoryIds != null && Array.isArray(categoryIds) && categoryIds.length > 0;
      
      if (hasTagIds || hasCategoryIds) {
        // Query tags và categories song song
        const [tags, categories] = await Promise.all([
          hasTagIds
            ? this.tagRepo.find({ where: { id: In(tagIds) } })
            : Promise.resolve([]),
          hasCategoryIds
            ? this.categoryRepo.find({ where: { id: In(categoryIds) } })
            : Promise.resolve([]),
        ]);

        post.tags = tags;
        post.categories = categories;
        await this.repository.save(post);
      }
    }

    return result;
  }

  /**
   * Cập nhật post với xử lý tags và categories
   */
  async update(
    id: number,
    updateDto: DeepPartial<Post> & { tag_ids?: number[]; category_ids?: number[] },
    updatedBy?: number,
  ): Promise<ApiResponse<Post | null>> {
    const data = { ...updateDto };
    const tagIds = data.tag_ids;
    const categoryIds = data.category_ids;
    
    // Xóa tag_ids và category_ids khỏi data
    delete data.tag_ids;
    delete data.category_ids;

    // Đảm bảo slug
    this.ensureSlug(data);

    // Gọi parent update
    const result = await super.update(id, data as DeepPartial<Post>, updatedBy);

    // Xử lý ManyToMany relations sau khi cập nhật thành công
    if (result.success && result.data && (tagIds !== undefined || categoryIds !== undefined)) {
      const post = await this.repository.findOne({ 
        where: { id } as any,
        relations: ['tags', 'categories'],
      });

      if (post) {
        // Xác định có cần xử lý tags và categories không
        const shouldUpdateTags = tagIds !== undefined;
        const shouldUpdateCategories = categoryIds !== undefined;
        
        // Chuẩn bị promises chỉ cho những gì thực sự cần query
        const promises: Promise<any>[] = [];
        
        let tags: PostTag[] = post.tags || [];
        let categories: PostCategory[] = post.categories || [];
        
        // Chỉ query tags nếu cần update và có giá trị hợp lệ
        if (shouldUpdateTags) {
          if (tagIds != null && Array.isArray(tagIds) && tagIds.length > 0) {
            promises.push(
              this.tagRepo.find({ where: { id: In(tagIds) } }).then(result => {
                tags = result;
                return result;
              })
            );
          } else {
            tags = [];
          }
        }
        
        // Chỉ query categories nếu cần update và có giá trị hợp lệ
        if (shouldUpdateCategories) {
          if (categoryIds != null && Array.isArray(categoryIds) && categoryIds.length > 0) {
            promises.push(
              this.categoryRepo.find({ where: { id: In(categoryIds) } }).then(result => {
                categories = result;
                return result;
              })
            );
          } else {
            categories = [];
          }
        }

        // Chỉ query những gì cần thiết (có thể 0, 1 hoặc 2 queries)
        if (promises.length > 0) {
          await Promise.all(promises);
        }

        // Chỉ update và save nếu có thay đổi
        if (shouldUpdateTags || shouldUpdateCategories) {
          if (shouldUpdateTags) post.tags = tags;
          if (shouldUpdateCategories) post.categories = categories;
          await this.repository.save(post);
          
          // Refresh result data
          const updated = await this.repository.findOne({ 
            where: { id } as any,
            relations: ['tags', 'categories', 'primary_category'],
          });
          if (updated) {
            result.data = updated;
          }
        }
      }
    }

    return result;
  }
}


