import { Controller, Get, Param, Query } from '@nestjs/common';
import { ProductService } from './product.service';
import { GetProductDto } from './dtos/get-product.dto';
import { BaseController } from '../../../common/base/base.controller';

@Controller('public/products')
export class ProductController extends BaseController<any> {
  protected service = this.productService;
  
  constructor(private readonly productService: ProductService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Product';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getProducts';
  }

  protected getGetMethodName(): string {
    return 'getProduct';
  }

  // Disable các method khác
  protected getCreateMethodName(): string {
    return null; // Disable create
  }

  protected getUpdateMethodName(): string {
    return null; // Disable update
  }

  protected getDeleteMethodName(): string {
    return null; // Disable delete
  }

  // Custom list method với filters
  @Get()
  async list(@Query() query: GetProductDto) {
    const filters: any = {};
    if (query.status) filters.status = query.status;
    if (query.search) filters.search = query.search;
    if (query.category) filters.category = query.category;

    const result = await this.productService.getProducts(
      filters,
      parseInt(query.per_page) || 20,
      parseInt(query.page) || 1,
    );

    return this.handleListResponse(result);
  }

  // Custom get method
  @Get(':id')
  async get(@Param('id') id: string) {
    const result = await this.productService.getProduct(id);
    return this.handleResponse(result, 'Product not found');
  }

  // Custom slug endpoint
  @Get('slug/:slug')
  async getBySlug(@Param('slug') slug: string) {
    const result = await this.productService.getProductBySlug(slug);
    return this.handleResponse(result, 'Product not found');
  }
}
