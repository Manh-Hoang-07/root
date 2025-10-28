import { Module } from '@nestjs/common';
import { PostModule } from './post/post.module';
import { ProductModule } from './product/product.module';
import { MenuModule } from './menu/menu.module';
import { ContactModule } from './contact/contact.module';
import { ProductCategoryModule } from './product-category/product-category.module';
import { PostCategoryModule } from './post-category/post-category.module';
import { PostTagModule } from './post-tag/post-tag.module';
import { SystemConfigModule } from './system-config/system-config.module';
import { CartModule } from './cart/cart.module';

@Module({
  imports: [
    PostModule,
    ProductModule,
    MenuModule,
    ContactModule,
    ProductCategoryModule,
    PostCategoryModule,
    PostTagModule,
    SystemConfigModule,
    CartModule,
  ],
})
export class PublicModule {}