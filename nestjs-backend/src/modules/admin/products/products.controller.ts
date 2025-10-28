import { Controller, UseGuards } from '@nestjs/common';
import { ProductsService } from '../services/products.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/products')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class ProductsController extends BaseController<any> {
  protected service = this.productsService;
  
  constructor(private readonly productsService: ProductsService) {
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

  protected getCreateMethodName(): string {
    return 'createProduct';
  }

  protected getUpdateMethodName(): string {
    return 'updateProduct';
  }

  protected getDeleteMethodName(): string {
    return 'deleteProduct';
  }
}
