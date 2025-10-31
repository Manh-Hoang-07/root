import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { Post } from '../../../shared/entities/post.entity';
import { PostCategory } from '../../../shared/entities/post-category.entity';
import { PostTag } from '../../../shared/entities/post-tag.entity';
import { PostController } from './controllers/post.controller';
import { PostService } from './services/post.service';

@Module({
  imports: [TypeOrmModule.forFeature([Post, PostCategory, PostTag])],
  controllers: [PostController],
  providers: [PostService],
  exports: [PostService],
})
export class PostModule {}

