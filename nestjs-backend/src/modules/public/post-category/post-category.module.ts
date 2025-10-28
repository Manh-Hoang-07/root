import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { PostCategoryController } from './post-category.controller';
import { PostCategoryService } from './post-category.service';
import { PostCategory } from '../../../shared/entities/post-category.entity';

@Module({
  imports: [TypeOrmModule.forFeature([PostCategory])],
  controllers: [PostCategoryController],
  providers: [PostCategoryService],
  exports: [PostCategoryService],
})
export class PostCategoryModule {}
