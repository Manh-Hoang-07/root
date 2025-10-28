import { Controller } from '@nestjs/common';
import { ProductCategoryService } from './product-category.service';
import { BaseController } from '../../../common/base/base.controller';

@Controller('public/product-categories')
export class ProductCategoryController extends BaseController<any> {
  protected service = this.productCategoryService;
  
  constructor(private readonly productCategoryService: ProductCategoryService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Category';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getProductCategories';
  }

  protected getGetMethodName(): string {
    return 'getProductCategory';
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
}
