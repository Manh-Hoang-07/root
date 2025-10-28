import { Controller } from '@nestjs/common';
import { PostTagService } from './post-tag.service';
import { BaseController } from '../../../common/base/base.controller';

@Controller('public/post-tags')
export class PostTagController extends BaseController<any> {
  protected service = this.postTagService;
  
  constructor(private readonly postTagService: PostTagService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Tag';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getPostTags';
  }

  protected getGetMethodName(): string {
    return 'getPostTag';
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
