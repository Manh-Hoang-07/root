import { Controller, Get, Post, Param, Body, Query } from '@nestjs/common';
import { PublicService } from './public.service';

@Controller('public')
export class PublicController {
  constructor(private readonly publicService: PublicService) {}

  // Posts (published only)
  @Get('posts')
  async getPosts(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
    @Query('relations') relations?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    const relationsArray = relations ? relations.split(',') : [];

    return this.publicService.getPosts(
      filters,
      parseInt(perPage),
      parseInt(page),
      relationsArray,
    );
  }

  @Get('posts/:id')
  async getPost(
    @Param('id') id: string,
    @Query('relations') relations?: string,
  ) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.publicService.getPost(id, relationsArray);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Post not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Get('posts/slug/:slug')
  async getPostBySlug(
    @Param('slug') slug: string,
    @Query('relations') relations?: string,
  ) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.publicService.getPostBySlug(slug, relationsArray);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Post not found',
        data: null,
      };
    }

    return { data: result };
  }

  // Products (published only)
  @Get('products')
  async getProducts(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
    @Query('category') category?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;
    if (category) filters.category = category;

    return this.publicService.getProducts(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get('products/:id')
  async getProduct(@Param('id') id: string) {
    const result = await this.publicService.getProduct(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Product not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Get('products/slug/:slug')
  async getProductBySlug(@Param('slug') slug: string) {
    const result = await this.publicService.getProductBySlug(slug);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Product not found',
        data: null,
      };
    }

    return { data: result };
  }

  // Product Categories
  @Get('product-categories')
  async getProductCategories() {
    const result = await this.publicService.getProductCategories();
    return { data: result };
  }

  @Get('product-categories/:id')
  async getProductCategory(@Param('id') id: string) {
    const result = await this.publicService.getProductCategory(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Category not found',
        data: null,
      };
    }

    return { data: result };
  }

  // Post Categories
  @Get('post-categories')
  async getPostCategories() {
    const result = await this.publicService.getPostCategories();
    return { data: result };
  }

  @Get('post-categories/:id')
  async getPostCategory(@Param('id') id: string) {
    const result = await this.publicService.getPostCategory(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Category not found',
        data: null,
      };
    }

    return { data: result };
  }

  // Post Tags
  @Get('post-tags')
  async getPostTags() {
    const result = await this.publicService.getPostTags();
    return { data: result };
  }

  @Get('post-tags/:id')
  async getPostTag(@Param('id') id: string) {
    const result = await this.publicService.getPostTag(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Tag not found',
        data: null,
      };
    }

    return { data: result };
  }

  // Contact (submit contact form)
  @Post('contact')
  async submitContact(@Body() contactDto: any) {
    const result = await this.publicService.submitContact(contactDto);
    return { data: result };
  }

  // Menu
  @Get('menu')
  async getMenu() {
    const result = await this.publicService.getMenu();
    return { data: result };
  }

  // System Config
  @Get('config')
  async getConfig() {
    const result = await this.publicService.getConfig();
    return { data: result };
  }

  @Get('config/:key')
  async getConfigByKey(@Param('key') key: string) {
    const result = await this.publicService.getConfigByKey(key);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Config not found',
        data: null,
      };
    }

    return { data: result };
  }
}
