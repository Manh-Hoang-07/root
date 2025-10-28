import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';

// Import all entities
import { Post } from '../../shared/entities/post.entity';
import { PostCategory } from '../../shared/entities/post-category.entity';
import { PostTag } from '../../shared/entities/post-tag.entity';
import { User } from '../../shared/entities/user.entity';
import { Product } from '../../shared/entities/product.entity';
import { Order } from '../../shared/entities/order.entity';
import { Contact } from '../../shared/entities/contact.entity';
import { Cart } from '../../shared/entities/cart.entity';

@Injectable()
export class UserService {
  constructor(
    @InjectRepository(Post)
    private readonly postRepository: Repository<Post>,
    @InjectRepository(PostCategory)
    private readonly postCategoryRepository: Repository<PostCategory>,
    @InjectRepository(PostTag)
    private readonly postTagRepository: Repository<PostTag>,
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
    @InjectRepository(Product)
    private readonly productRepository: Repository<Product>,
    @InjectRepository(Order)
    private readonly orderRepository: Repository<Order>,
    @InjectRepository(Contact)
    private readonly contactRepository: Repository<Contact>,
    @InjectRepository(Cart)
    private readonly cartRepository: Repository<Cart>,
  ) {}

  // Generic list method
  private async list<T>(
    repository: Repository<T>,
    filters: any = {},
    perPage: number = 20,
    page: number = 1,
    relations: string[] = [],
  ) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const where: FindOptionsWhere<T> = {};

    if (filters.status) {
      (where as any)['status'] = filters.status;
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

  // Profile
  async getProfile(userId: string) {
    return this.userRepository.findOne({
      where: { id: Number(userId) } as any,
    });
  }

  async updateProfile(userId: string, updateProfileDto: any) {
    const existingUser = await this.userRepository.findOne({
      where: { id: Number(userId) } as any,
    });

    if (!existingUser) {
      return null;
    }

    const updatedUser = this.userRepository.merge(existingUser, updateProfileDto as any);
    return this.userRepository.save(updatedUser);
  }

  // Posts (user's own posts)
  async getUserPosts(
    userId: string,
    filters: any = {},
    perPage: number = 20,
    page: number = 1,
    relations: string[] = [],
  ) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const queryBuilder = this.postRepository
      .createQueryBuilder('post')
      .where('post.created_user_id = :userId', { userId: Number(userId) })
      .orderBy('post.created_at', 'DESC')
      .skip(skip)
      .take(validPerPage);

    // Apply filters
    if (filters.status) {
      queryBuilder.andWhere('post.status = :status', { status: filters.status });
    }

    if (filters.search) {
      queryBuilder.andWhere('post.name LIKE :search', { search: `%${filters.search}%` });
    }

    // Add relations
    const validRelations = relations.filter((r) => ['categories', 'tags', 'createdUser', 'updatedUser'].includes(r));
    for (const relation of validRelations) {
      queryBuilder.leftJoinAndSelect(`post.${relation}`, relation);
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

  async getUserPost(userId: string, id: string, relations: string[] = []) {
    const queryBuilder = this.postRepository
      .createQueryBuilder('post')
      .where('post.id = :id', { id: Number(id) })
      .andWhere('post.created_user_id = :userId', { userId: Number(userId) });

    // Add relations
    const validRelations = relations.filter((r) => ['categories', 'tags', 'createdUser', 'updatedUser'].includes(r));
    for (const relation of validRelations) {
      queryBuilder.leftJoinAndSelect(`post.${relation}`, relation);
    }

    return queryBuilder.getOne();
  }

  async createPost(userId: string, createPostDto: any) {
    const { category_ids, tag_ids, ...postData } = createPostDto;
    
    // Set default status to draft for users
    if (!postData.status) {
      postData.status = 'draft';
    }

    // Set user ID
    postData.created_user_id = Number(userId);

    const post = this.postRepository.create(postData as any);
    const savedPost = await this.postRepository.save(post);

    // Handle categories and tags if provided
    if (category_ids && category_ids.length > 0) {
      await this.postRepository.manager
        .createQueryBuilder()
        .relation(Post, 'categories')
        .of((savedPost as any).id)
        .add(category_ids);
    }

    if (tag_ids && tag_ids.length > 0) {
      await this.postRepository.manager
        .createQueryBuilder()
        .relation(Post, 'tags')
        .of((savedPost as any).id)
        .add(tag_ids);
    }

    return savedPost;
  }

  async updatePost(userId: string, id: string, updatePostDto: any) {
    const { category_ids, tag_ids, ...postData } = updatePostDto;
    
    const existingPost = await this.postRepository.findOne({
      where: { 
        id: Number(id),
        created_user_id: Number(userId)
      } as any,
    });

    if (!existingPost) {
      return null;
    }

    const updatedPost = this.postRepository.merge(existingPost, postData as any);
    const savedPost = await this.postRepository.save(updatedPost);

    // Handle categories and tags if provided
    if (category_ids !== undefined) {
      await this.postRepository.manager
        .createQueryBuilder()
        .relation(Post, 'categories')
        .of((savedPost as any).id)
        .addAndRemove(category_ids, []);
    }

    if (tag_ids !== undefined) {
      await this.postRepository.manager
        .createQueryBuilder()
        .relation(Post, 'tags')
        .of((savedPost as any).id)
        .addAndRemove(tag_ids, []);
    }

    return savedPost;
  }

  async deletePost(userId: string, id: string) {
    const existingPost = await this.postRepository.findOne({
      where: { 
        id: Number(id),
        created_user_id: Number(userId)
      } as any,
    });

    if (!existingPost) {
      return null;
    }

    if (this.hasProperty(this.postRepository, 'deleted_at')) {
      (existingPost as any).deleted_at = new Date();
      return this.postRepository.save(existingPost);
    } else {
      return this.postRepository.remove(existingPost);
    }
  }

  // Products (view only)
  async getProducts(filters: any = {}, perPage: number = 20, page: number = 1) {
    // Only show published products
    const publicFilters = { ...filters, status: 'published' };
    return this.list(this.productRepository, publicFilters, perPage, page);
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

  // Orders (user's own orders)
  async getUserOrders(
    userId: string,
    filters: any = {},
    perPage: number = 20,
    page: number = 1,
  ) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const queryBuilder = this.orderRepository
      .createQueryBuilder('order')
      .where('order.user_id = :userId', { userId: Number(userId) })
      .orderBy('order.created_at', 'DESC')
      .skip(skip)
      .take(validPerPage);

    // Apply filters
    if (filters.status) {
      queryBuilder.andWhere('order.status = :status', { status: filters.status });
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

  async getUserOrder(userId: string, id: string) {
    return this.orderRepository.findOne({
      where: { 
        id: Number(id),
        user_id: Number(userId)
      } as any,
    });
  }

  async createOrder(userId: string, createOrderDto: any) {
    const orderData = {
      ...createOrderDto,
      user_id: Number(userId),
    };

    const order = this.orderRepository.create(orderData as any);
    return this.orderRepository.save(order);
  }

  // Cart
  async getCart(userId: string) {
    return this.cartRepository.find({
      where: { user_id: Number(userId) } as any,
      relations: ['product'],
    });
  }

  async addToCart(userId: string, addToCartDto: any) {
    const cartData = {
      ...addToCartDto,
      user_id: Number(userId),
    };

    const cart = this.cartRepository.create(cartData as any);
    return this.cartRepository.save(cart);
  }

  async updateCartItem(userId: string, id: string, updateCartDto: any) {
    const existingCart = await this.cartRepository.findOne({
      where: { 
        id: Number(id),
        user_id: Number(userId)
      } as any,
    });

    if (!existingCart) {
      return null;
    }

    const updatedCart = this.cartRepository.merge(existingCart, updateCartDto as any);
    return this.cartRepository.save(updatedCart);
  }

  async removeFromCart(userId: string, id: string) {
    const existingCart = await this.cartRepository.findOne({
      where: { 
        id: Number(id),
        user_id: Number(userId)
      } as any,
    });

    if (!existingCart) {
      return null;
    }

    return this.cartRepository.remove(existingCart);
  }

  async clearCart(userId: string) {
    await this.cartRepository.delete({ user_id: Number(userId) } as any);
  }

  // Contacts
  async getUserContacts(
    userId: string,
    filters: any = {},
    perPage: number = 20,
    page: number = 1,
  ) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const queryBuilder = this.contactRepository
      .createQueryBuilder('contact')
      .where('contact.user_id = :userId', { userId: Number(userId) })
      .orderBy('contact.created_at', 'DESC')
      .skip(skip)
      .take(validPerPage);

    // Apply filters
    if (filters.status) {
      queryBuilder.andWhere('contact.status = :status', { status: filters.status });
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

  async getUserContact(userId: string, id: string) {
    return this.contactRepository.findOne({
      where: { 
        id: Number(id),
        user_id: Number(userId)
      } as any,
    });
  }

  async createContact(userId: string, createContactDto: any) {
    const contactData = {
      ...createContactDto,
      user_id: Number(userId),
    };

    const contact = this.contactRepository.create(contactData as any);
    return this.contactRepository.save(contact);
  }
}
