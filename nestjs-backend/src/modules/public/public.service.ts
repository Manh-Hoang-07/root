import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';

// Import all entities
import { Post } from '../../entities/post.entity';
import { PostCategory } from '../../entities/post-category.entity';
import { PostTag } from '../../entities/post-tag.entity';
import { Product } from '../../entities/product.entity';
import { ProductCategory } from '../../entities/product-category.entity';
import { Contact } from '../../entities/contact.entity';
import { Menu } from '../../entities/menu.entity';
import { SystemConfig } from '../../entities/system-config.entity';
import { PostStatus } from '../../enums/post-status.enum';

@Injectable()
export class PublicService {
  constructor(
    @InjectRepository(Post)
    private readonly postRepository: Repository<Post>,
    @InjectRepository(PostCategory)
    private readonly postCategoryRepository: Repository<PostCategory>,
    @InjectRepository(PostTag)
    private readonly postTagRepository: Repository<PostTag>,
    @InjectRepository(Product)
    private readonly productRepository: Repository<Product>,
    @InjectRepository(ProductCategory)
    private readonly productCategoryRepository: Repository<ProductCategory>,
    @InjectRepository(Contact)
    private readonly contactRepository: Repository<Contact>,
    @InjectRepository(Menu)
    private readonly menuRepository: Repository<Menu>,
    @InjectRepository(SystemConfig)
    private readonly systemConfigRepository: Repository<SystemConfig>,
  ) {}

  // Generic list method for published content
  private async listPublished<T>(
    repository: Repository<T>,
    filters: any = {},
    perPage: number = 20,
    page: number = 1,
    relations: string[] = [],
  ) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const where: FindOptionsWhere<T> = {};

    // Only apply status filter for Post entity
    if (this.hasProperty(repository, 'status') && repository.metadata.name === 'Post') {
      (where as any).status = PostStatus.PUBLISHED;
    }

    if (filters.search) {
      const searchFields = ['name', 'title', 'email'];
      for (const field of searchFields) {
        if (this.hasProperty(repository, field)) {
          (where as any)[field] = ILike(`%${filters.search}%`);
          break;
        }
      }
    }

    const findOptions: FindManyOptions<T> = {
      where,
      order: { created_at: 'DESC' } as any,
      skip,
      take: validPerPage,
    };

    if (relations.length > 0) {
      (findOptions as any).relations = relations;
    }

    const [data, total] = await repository.findAndCount(findOptions);

    return {
      data,
      meta: {
        total,
        per_page: validPerPage,
        current_page: page,
        last_page: Math.ceil(total / validPerPage),
        from: skip + 1,
        to: Math.min(skip + validPerPage, total),
      },
    };
  }

  // Check if entity has a specific property
  private hasProperty<T>(repository: Repository<T>, property: string): boolean {
    const metadata = repository.metadata;
    return metadata.columns.some(column => column.propertyName === property);
  }

  // Posts (published only)
  async getPosts(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    try {
      const validPerPage = Math.min(perPage, 100);
      const skip = (page - 1) * validPerPage;

      const where: any = {
        status: PostStatus.PUBLISHED,
      };

      if (filters.search) {
        where.name = ILike(`%${filters.search}%`);
      }

      const findOptions: any = {
        where,
        order: { created_at: 'DESC' },
        skip,
        take: validPerPage,
      };

      // Add relations if provided
      if (relations.length > 0) {
        const validRelations = relations.filter((r) => ['categories', 'tags', 'createdUser', 'updatedUser'].includes(r));
        if (validRelations.length > 0) {
          try {
            findOptions.relations = validRelations;
            const [data, total] = await this.postRepository.findAndCount(findOptions);
            return {
              data,
              meta: {
                total,
                per_page: validPerPage,
                current_page: page,
                last_page: Math.ceil(total / validPerPage),
                from: skip + 1,
                to: Math.min(skip + validPerPage, total),
              },
            };
          } catch (relationError) {
            console.error('Error loading relations:', relationError.message);
            // Continue without relations
          }
        }
      }

      const [data, total] = await this.postRepository.findAndCount(findOptions);

      return {
        data,
        meta: {
          total,
          per_page: validPerPage,
          current_page: page,
          last_page: Math.ceil(total / validPerPage),
          from: skip + 1,
          to: Math.min(skip + validPerPage, total),
        },
      };
    } catch (error) {
      console.error('Error in getPosts:', error);
      throw error;
    }
  }

  async getPost(id: string, relations: string[] = []) {
    const findOptions: any = {
      where: { 
        id: Number(id),
        status: PostStatus.PUBLISHED
      },
    };

    if (relations.length > 0) {
      findOptions.relations = relations;
    }

    const post = await this.postRepository.findOne(findOptions);

    if (!post) {
      return null;
    }

    // Increment view count
    (post as any).view_count += 1;
    await this.postRepository.save(post);

    return post;
  }

  async getPostBySlug(slug: string, relations: string[] = []) {
    const findOptions: any = {
      where: { 
        slug,
        status: 'published'
      },
    };

    if (relations.length > 0) {
      findOptions.relations = relations;
    }

    const post = await this.postRepository.findOne(findOptions);

    if (!post) {
      return null;
    }

    // Increment view count
    (post as any).view_count += 1;
    await this.postRepository.save(post);

    return post;
  }

  // Products (published only)
  async getProducts(filters: any = {}, perPage: number = 20, page: number = 1) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const queryBuilder = this.productRepository
      .createQueryBuilder('product')
      .where('product.status = :status', { status: 'published' })
      .orderBy('product.created_at', 'DESC')
      .skip(skip)
      .take(validPerPage);

    // Apply filters
    if (filters.search) {
      queryBuilder.andWhere('product.name LIKE :search', { search: `%${filters.search}%` });
    }

    if (filters.category) {
      queryBuilder.andWhere('product.category_id = :category', { category: filters.category });
    }

    const [data, total] = await queryBuilder.getManyAndCount();

    return {
      data,
      meta: {
        total,
        per_page: validPerPage,
        current_page: page,
        last_page: Math.ceil(total / validPerPage),
        from: skip + 1,
        to: Math.min(skip + validPerPage, total),
      },
    };
  }

  async getProduct(id: string) {
    const product = await this.productRepository.findOne({
      where: { 
        id: Number(id),
        status: 'published'
      } as any,
    });

    if (!product) {
      return null;
    }

    // Increment view count
    (product as any).view_count += 1;
    await this.productRepository.save(product);

    return product;
  }

  async getProductBySlug(slug: string) {
    const product = await this.productRepository.findOne({
      where: { 
        slug,
        status: 'published'
      } as any,
    });

    if (!product) {
      return null;
    }

    // Increment view count
    (product as any).view_count += 1;
    await this.productRepository.save(product);

    return product;
  }

  // Product Categories
  async getProductCategories() {
    return this.productCategoryRepository.find({
      where: { status: 'active' } as any,
      order: { name: 'ASC' } as any,
    });
  }

  async getProductCategory(id: string) {
    return this.productCategoryRepository.findOne({
      where: { 
        id: Number(id),
        status: 'active'
      } as any,
    });
  }

  // Post Categories
  async getPostCategories() {
    return this.postCategoryRepository.find({
      where: { status: 'active' } as any,
      order: { name: 'ASC' } as any,
    });
  }

  async getPostCategory(id: string) {
    return this.postCategoryRepository.findOne({
      where: { 
        id: Number(id),
        status: 'active'
      } as any,
    });
  }

  // Post Tags
  async getPostTags() {
    return this.postTagRepository.find({
      where: { status: 'active' } as any,
      order: { name: 'ASC' } as any,
    });
  }

  async getPostTag(id: string) {
    return this.postTagRepository.findOne({
      where: { 
        id: Number(id),
        status: 'active'
      } as any,
    });
  }

  // Contact
  async submitContact(contactDto: any) {
    const contact = this.contactRepository.create(contactDto as any);
    return this.contactRepository.save(contact);
  }

  // Menu
  async getMenu() {
    return this.menuRepository.find({
      where: { status: 'active' } as any,
      order: { sort_order: 'ASC' } as any,
    });
  }

  // System Config
  async getConfig() {
    return this.systemConfigRepository.find({
      where: { status: 'active' } as any,
    });
  }

  async getConfigByKey(key: string) {
    return this.systemConfigRepository.findOne({
      where: { 
        key,
        status: 'active'
      } as any,
    });
  }
}
