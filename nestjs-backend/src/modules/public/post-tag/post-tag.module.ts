import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { PostTagController } from './controllers/post-tag.controller';
import { PostTagService } from './services/post-tag.service';
import { PostTag } from '../../../shared/entities/post-tag.entity';

@Module({
  imports: [TypeOrmModule.forFeature([PostTag])],
  controllers: [PostTagController],
  providers: [PostTagService],
  exports: [PostTagService],
})
export class PostTagModule {}

