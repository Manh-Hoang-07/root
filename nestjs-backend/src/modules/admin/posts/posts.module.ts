import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { PostsController } from './posts.controller';
import { PostsService } from './posts.service';
import { Post } from '../../../shared/entities/post.entity';
import { PostCategory } from '../../../shared/entities/post-category.entity';
import { PostTag } from '../../../shared/entities/post-tag.entity';

@Module({
  imports: [TypeOrmModule.forFeature([Post, PostCategory, PostTag])],
  controllers: [PostsController],
  providers: [PostsService],
  exports: [PostsService],
})
export class PostsModule {}
