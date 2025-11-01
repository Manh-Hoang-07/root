import { Module } from '@nestjs/common';
import { PostModule } from './post/post.module';
import { PostCategoryModule } from './post-category/post-category.module';
import { PostTagModule } from './post-tag/post-tag.module';
import { RoleModule } from './role/role.module';
import { PermissionModule } from './permission/permission.module';

@Module({
  imports: [
    PostModule,
    PostCategoryModule,
    PostTagModule,
    RoleModule,
    PermissionModule,
  ],
  exports: [
    PostModule,
    PostCategoryModule,
    PostTagModule,
    RoleModule,
    PermissionModule,
  ],
})
export class AdminModule {}

