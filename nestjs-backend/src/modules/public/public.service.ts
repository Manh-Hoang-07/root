import { Injectable } from '@nestjs/common';
import { PostService } from './post/post.service';
import { ProductService } from './product/product.service';
import { PostCategoryService } from './post-category/post-category.service';
import { ProductCategoryService } from './product-category/product-category.service';
import { PostTagService } from './post-tag/post-tag.service';
import { ContactService } from './contact/contact.service';
import { MenuService } from './menu/menu.service';
import { SystemConfigService } from './system-config/system-config.service';

@Injectable()
export class PublicService {
  constructor(
    private readonly postsService: PostService,
    private readonly productsService: ProductService,
    private readonly postCategoriesService: PostCategoryService,
    private readonly productCategoriesService: ProductCategoryService,
    private readonly postTagsService: PostTagService,
    private readonly contactsService: ContactService,
    private readonly menusService: MenuService,
    private readonly systemConfigsService: SystemConfigService,
  ) {}

  // Posts
  async getPosts(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    return this.postsService.getPosts(filters, perPage, page, relations);
  }

  async getPost(id: string, relations: string[] = []) {
    return this.postsService.getPost(id, relations);
  }

  async getPostBySlug(slug: string, relations: string[] = []) {
    return this.postsService.getPostBySlug(slug, relations);
  }

  // Products
  async getProducts(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.productsService.getProducts(filters, perPage, page);
  }

  async getProduct(id: string) {
    return this.productsService.getProduct(id);
  }

  async getProductBySlug(slug: string) {
    return this.productsService.getProductBySlug(slug);
  }

  // Post Categories
  async getPostCategories() {
    return this.postCategoriesService.list();
  }

  async getPostCategory(id: string) {
    return this.postCategoriesService.getOne(id);
  }

  // Product Categories
  async getProductCategories() {
    return this.productCategoriesService.list();
  }

  async getProductCategory(id: string) {
    return this.productCategoriesService.getOne(id);
  }

  // Post Tags
  async getPostTags() {
    return this.postTagsService.list();
  }

  async getPostTag(id: string) {
    return this.postTagsService.getOne(id);
  }

  // Contact
  async submitContact(contactDto: any) {
    return this.contactsService.submit(contactDto);
  }

  // Menu
  async getMenu() {
    return this.menusService.list();
  }

  // System Config
  async getConfig() {
    return this.systemConfigsService.list();
  }

  async getConfigByKey(key: string) {
    return this.systemConfigsService.getByKey(key);
  }
}
