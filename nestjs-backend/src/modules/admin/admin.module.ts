import { Module } from '@nestjs/common';
import { PostsModule } from './posts/posts.module';
import { ProductsModule } from './products/products.module';
import { UsersModule } from './users/users.module';
import { ContactsModule } from './contacts/contacts.module';
import { MenusModule } from './menus/menus.module';
import { OrdersModule } from './orders/orders.module';
import { PermissionsModule } from './permissions/permissions.module';
import { RolesModule } from './roles/roles.module';
import { SystemConfigsModule } from './system-configs/system-configs.module';
import { PostCategoriesModule } from './post-categories/post-categories.module';
import { PostTagsModule } from './post-tags/post-tags.module';
import { ProductCategoriesModule } from './product-categories/product-categories.module';

@Module({
  imports: [
    PostsModule,
    ProductsModule,
    UsersModule,
    ContactsModule,
    MenusModule,
    OrdersModule,
    PermissionsModule,
    RolesModule,
    SystemConfigsModule,
    PostCategoriesModule,
    PostTagsModule,
    ProductCategoriesModule,
  ],
})
export class AdminModule {}