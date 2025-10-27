import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards, Request } from '@nestjs/common';
import { UserService } from './user.service';
import { JwtAuthGuard } from '../auth/guards/jwt-auth.guard';

@Controller('user')
@UseGuards(JwtAuthGuard)
export class UserController {
  constructor(private readonly userService: UserService) {}

  // Profile
  @Get('profile')
  async getProfile(@Request() req) {
    const result = await this.userService.getProfile(req.user.sub);
    return { data: result };
  }

  @Put('profile')
  async updateProfile(@Request() req, @Body() updateProfileDto: any) {
    const result = await this.userService.updateProfile(req.user.sub, updateProfileDto);
    return { data: result };
  }

  // Posts (user's own posts)
  @Get('posts')
  async getPosts(
    @Request() req,
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

    return this.userService.getUserPosts(
      req.user.sub,
      filters,
      parseInt(perPage),
      parseInt(page),
      relationsArray,
    );
  }

  @Get('posts/:id')
  async getPost(
    @Request() req,
    @Param('id') id: string,
    @Query('relations') relations?: string,
  ) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.userService.getUserPost(req.user.sub, id, relationsArray);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Post not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post('posts')
  async createPost(@Request() req, @Body() createPostDto: any) {
    const result = await this.userService.createPost(req.user.sub, createPostDto);
    return { data: result };
  }

  @Put('posts/:id')
  async updatePost(@Request() req, @Param('id') id: string, @Body() updatePostDto: any) {
    const result = await this.userService.updatePost(req.user.sub, id, updatePostDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Post not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete('posts/:id')
  async deletePost(@Request() req, @Param('id') id: string) {
    const result = await this.userService.deletePost(req.user.sub, id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Post not found',
        data: null,
      };
    }

    return { message: 'Post deleted successfully' };
  }

  // Products (view only)
  @Get('products')
  async getProducts(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.userService.getProducts(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get('products/:id')
  async getProduct(@Param('id') id: string) {
    const result = await this.userService.getProduct(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Product not found',
        data: null,
      };
    }

    return { data: result };
  }

  // Orders (user's own orders)
  @Get('orders')
  async getOrders(
    @Request() req,
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;

    return this.userService.getUserOrders(
      req.user.sub,
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get('orders/:id')
  async getOrder(@Request() req, @Param('id') id: string) {
    const result = await this.userService.getUserOrder(req.user.sub, id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Order not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post('orders')
  async createOrder(@Request() req, @Body() createOrderDto: any) {
    const result = await this.userService.createOrder(req.user.sub, createOrderDto);
    return { data: result };
  }

  // Cart
  @Get('cart')
  async getCart(@Request() req) {
    const result = await this.userService.getCart(req.user.sub);
    return { data: result };
  }

  @Post('cart/add')
  async addToCart(@Request() req, @Body() addToCartDto: any) {
    const result = await this.userService.addToCart(req.user.sub, addToCartDto);
    return { data: result };
  }

  @Put('cart/update/:id')
  async updateCartItem(@Request() req, @Param('id') id: string, @Body() updateCartDto: any) {
    const result = await this.userService.updateCartItem(req.user.sub, id, updateCartDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Cart item not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Delete('cart/remove/:id')
  async removeFromCart(@Request() req, @Param('id') id: string) {
    const result = await this.userService.removeFromCart(req.user.sub, id);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Cart item not found',
        data: null,
      };
    }

    return { message: 'Item removed from cart' };
  }

  @Delete('cart/clear')
  async clearCart(@Request() req) {
    await this.userService.clearCart(req.user.sub);
    return { message: 'Cart cleared successfully' };
  }

  // Contacts
  @Get('contacts')
  async getContacts(
    @Request() req,
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;

    return this.userService.getUserContacts(
      req.user.sub,
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get('contacts/:id')
  async getContact(@Request() req, @Param('id') id: string) {
    const result = await this.userService.getUserContact(req.user.sub, id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Contact not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Post('contacts')
  async createContact(@Request() req, @Body() createContactDto: any) {
    const result = await this.userService.createContact(req.user.sub, createContactDto);
    return { data: result };
  }
}
