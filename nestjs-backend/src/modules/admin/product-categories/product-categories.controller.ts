import { Controller, UseGuards } from '@nestjs/common';
import { ProductCategoriesService } from '../services/product-categories.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/product-categories')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class ProductCategoriesController extends BaseController<any> {
  protected service = this.productCategoriesService;
  
  constructor(private readonly productCategoriesService: ProductCategoriesService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Product category';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getProductCategories';
  }

  protected getGetMethodName(): string {
    return 'getProductCategory';
  }

  protected getCreateMethodName(): string {
    return 'createProductCategory';
  }

  protected getUpdateMethodName(): string {
    return 'updateProductCategory';
  }

  protected getDeleteMethodName(): string {
    return 'deleteProductCategory';
  }
}
