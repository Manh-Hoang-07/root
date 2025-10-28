import { Controller, UseGuards } from '@nestjs/common';
import { PostCategoriesService } from '../services/post-categories.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/post-categories')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class PostCategoriesController extends BaseController<any> {
  protected service = this.postCategoriesService;
  
  constructor(private readonly postCategoriesService: PostCategoriesService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Post category';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getPostCategories';
  }

  protected getGetMethodName(): string {
    return 'getPostCategory';
  }

  protected getCreateMethodName(): string {
    return 'createPostCategory';
  }

  protected getUpdateMethodName(): string {
    return 'updatePostCategory';
  }

  protected getDeleteMethodName(): string {
    return 'deletePostCategory';
  }
}
