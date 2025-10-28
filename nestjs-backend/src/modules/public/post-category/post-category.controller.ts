import { Controller } from '@nestjs/common';
import { PostCategoryService } from './post-category.service';
import { BaseController } from '../../../common/base/base.controller';

@Controller('public/post-categories')
export class PostCategoryController extends BaseController<any> {
  protected service = this.postCategoryService;
  
  constructor(private readonly postCategoryService: PostCategoryService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Category';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getPostCategories';
  }

  protected getGetMethodName(): string {
    return 'getPostCategory';
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
