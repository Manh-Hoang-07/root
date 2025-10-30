import { Module } from '@nestjs/common';
import { PostModule } from './post/post.module';
import { PostCategoryModule } from './post-category/post-category.module';
import { PostTagModule } from './post-tag/post-tag.module';

@Module({
  imports: [PostModule, PostCategoryModule, PostTagModule],
  exports: [PostModule, PostCategoryModule, PostTagModule],
})
export class PublicModule {}

