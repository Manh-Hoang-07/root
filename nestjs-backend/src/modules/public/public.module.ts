import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { PublicController } from './public.controller';
import { PublicService } from './public.service';
import { CartsController } from './controllers/carts.controller';

// Import all services from new structure
import { PostService } from './post/post.service';
import { ProductService } from './product/product.service';
import { PostCategoryService } from './post-category/post-category.service';
import { ProductCategoryService } from './product-category/product-category.service';
import { PostTagService } from './post-tag/post-tag.service';
import { ContactService } from './contact/contact.service';
import { MenuService } from './menu/menu.service';
import { SystemConfigService } from './system-config/system-config.service';
import { CartService } from './cart/cart.service';

// Import all entities
import { Post } from '../../entities/post.entity';
import { PostCategory } from '../../entities/post-category.entity';
import { PostTag } from '../../entities/post-tag.entity';
import { Product } from '../../entities/product.entity';
import { ProductCategory } from '../../entities/product-category.entity';
import { Contact } from '../../entities/contact.entity';
import { Menu } from '../../entities/menu.entity';
import { SystemConfig } from '../../entities/system-config.entity';
import { Cart } from '../../entities/cart.entity';
import { ProductVariant } from '../../entities/product-variant.entity';

@Module({
  imports: [
    TypeOrmModule.forFeature([
      Post,
      PostCategory,
      PostTag,
      Product,
      ProductCategory,
      ProductVariant,
      Contact,
      Menu,
      SystemConfig,
      Cart,
    ]),
  ],
  controllers: [PublicController, CartsController],
  providers: [
    PublicService,
    CartService,
    PostService,
    ProductService,
    PostCategoryService,
    ProductCategoryService,
    PostTagService,
    ContactService,
    MenuService,
    SystemConfigService,
  ],
  exports: [PublicService, CartService],
})
export class PublicModule {}
