import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { PostCategory } from '../../../shared/entities/post-category.entity';
import { PostCategoryController } from './controllers/post-category.controller';
import { PostCategoryService } from './services/post-category.service';

@Module({
  imports: [TypeOrmModule.forFeature([PostCategory])],
  controllers: [PostCategoryController],
  providers: [PostCategoryService],
  exports: [PostCategoryService],
})
export class PostCategoryModule {}

