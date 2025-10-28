import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { PostCategoriesController } from './post-categories.controller';
import { PostCategoriesService } from './post-categories.service';
import { PostCategory } from '../../../shared/entities/post-category.entity';

@Module({
  imports: [TypeOrmModule.forFeature([PostCategory])],
  controllers: [PostCategoriesController],
  providers: [PostCategoriesService],
  exports: [PostCategoriesService],
})
export class PostCategoriesModule {}
