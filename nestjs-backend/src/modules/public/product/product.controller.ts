import { Controller, Get, Param, Query } from '@nestjs/common';
import { ProductService } from './product.service';
import { GetProductDto } from './dtos/get-product.dto';
import { logToFile } from '../../../shared/utils/file-logger.util';

@Controller('public/products')
export class ProductController {
  constructor(private readonly productService: ProductService) {
    console.log('========================================');
    console.log('[ProductController] CONTROLLER CONSTRUCTOR CALLED!');
    console.log('[ProductController] Controller path: public/products');
    console.log('[ProductController] Full route will be: /api/public/products');
    console.log('========================================');
    logToFile('[ProductController] Controller initialized');
    console.log('[ProductController] Controller initialized - routes should be registered');
  }
  
  // Simple test routes - no dependencies (must be before base route)
  @Get('ping')
  ping() {
    console.log('[ProductController] PING ROUTE CALLED!');
    return { message: 'ProductController is alive!', timestamp: new Date().toISOString() };
  }

  @Get('test')
  async test() {
    console.log('[ProductController] TEST ROUTE CALLED - Controller is working!');
    return { message: 'ProductController is working!', path: '/api/public/products/test' };
  }

  // Get by slug with optional relations (must be before :id route)
  @Get('slug/:slug')
  async getBySlug(@Param('slug') slug: string, @Query('relations') relations?: string) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.productService.getProductBySlug(slug, relationsArray);
    if (!result) {
      return { data: null, message: 'Product not found' };
    }
    return { data: result };
  }

  // Get by id with optional relations
  @Get(':id')
  async get(@Param('id') id: string, @Query('relations') relations?: string) {
    const relationsArray = relations ? relations.split(',') : [];
    const result = await this.productService.get(id, relationsArray);
    if (!result) {
      return { data: null, message: 'Product not found' };
    }
    return { data: result };
  }

  // List products with optional relations (base route - must be LAST)
  @Get()
  async list(@Query() query: GetProductDto) {
    console.log('========================================');
    console.log('[ProductController] LIST METHOD CALLED!');
    console.log('[ProductController] Route matched successfully!');
    console.log('Query:', JSON.stringify(query));
    console.log('========================================');
    logToFile('[ProductController] GET /api/public/products called');
    
    try {
      const filters: any = {};
      if (query?.status) filters.status = query.status;
      if (query?.search) filters.search = query.search;
      if (query?.category) filters.category = query.category;

      const relationsArray = query?.relations ? query.relations.split(',') : [];

      const result = await this.productService.list(
        filters,
        parseInt(query?.per_page) || 20,
        parseInt(query?.page) || 1,
        relationsArray,
      );
      return { data: result.data, meta: result.meta };
    } catch (error: any) {
      console.error('[ProductController] Error in list method:', error);
      throw error;
    }
  }
}
