import {
  Controller,
  Get,
  Query,
  Param,
  ValidationPipe,
} from '@nestjs/common';
import { Public } from '../../../../common/decorators/public.decorator';
import { PostTagService } from '../services/post-tag.service';
import { GetTagsDto } from '../../post/dtos/get-tags.dto';

@Controller('public/post-tags')
@Public()
export class PostTagController {
  constructor(private readonly postTagService: PostTagService) {}

  @Get()
  async findAll(@Query(ValidationPipe) query: GetTagsDto) {
    return this.postTagService.findAll(query);
  }

  @Get(':slug')
  async findOne(@Param('slug') slug: string) {
    return this.postTagService.findOne(slug);
  }
}

