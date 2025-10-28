import { Controller, UseGuards } from '@nestjs/common';
import { PostTagsService } from '../services/post-tags.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/post-tags')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class PostTagsController extends BaseController<any> {
  protected service = this.postTagsService;
  
  constructor(private readonly postTagsService: PostTagsService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Post tag';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getPostTags';
  }

  protected getGetMethodName(): string {
    return 'getPostTag';
  }

  protected getCreateMethodName(): string {
    return 'createPostTag';
  }

  protected getUpdateMethodName(): string {
    return 'updatePostTag';
  }

  protected getDeleteMethodName(): string {
    return 'deletePostTag';
  }
}
