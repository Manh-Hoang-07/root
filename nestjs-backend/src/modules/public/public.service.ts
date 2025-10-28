import { Injectable } from '@nestjs/common';
import { PostsService } from './services/posts.service';
import { ProductsService } from './services/products.service';
import { PostCategoriesService, ProductCategoriesService } from './services/categories.service';
import { PostTagsService } from './services/tags.service';
import { ContactsService } from './services/contacts.service';
import { MenusService } from './services/menus.service';
import { SystemConfigsService } from './services/configs.service';

@Injectable()
export class PublicService {
  constructor(
    private readonly postsService: PostsService,
    private readonly productsService: ProductsService,
    private readonly postCategoriesService: PostCategoriesService,
    private readonly productCategoriesService: ProductCategoriesService,
    private readonly postTagsService: PostTagsService,
    private readonly contactsService: ContactsService,
    private readonly menusService: MenusService,
    private readonly systemConfigsService: SystemConfigsService,
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
