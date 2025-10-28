import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { PostTagsController } from './post-tags.controller';
import { PostTagsService } from './post-tags.service';
import { PostTag } from '../../../shared/entities/post-tag.entity';

@Module({
  imports: [TypeOrmModule.forFeature([PostTag])],
  controllers: [PostTagsController],
  providers: [PostTagsService],
  exports: [PostTagsService],
})
export class PostTagsModule {}
