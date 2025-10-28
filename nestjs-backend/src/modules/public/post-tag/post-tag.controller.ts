import { Controller, Get, Param } from '@nestjs/common';
import { PostTagService } from './post-tag.service';

@Controller('public/post-tags')
export class PostTagController {
  constructor(private readonly postTagService: PostTagService) {}

  @Get()
  async list() {
    const data = await this.postTagService.getPostTags();
    return { data };
  }

  @Get(':id')
  async get(@Param('id') id: string) {
    const data = await this.postTagService.getPostTag(id);
    return { data };
  }
}
