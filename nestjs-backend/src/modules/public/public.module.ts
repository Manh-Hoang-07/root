import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { PublicController } from './public.controller';
import { PublicService } from './public.service';
import { CartsController } from './controllers/carts.controller';
import { CartsService } from './services/carts.service';

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
  providers: [PublicService, CartsService],
  exports: [PublicService, CartsService],
})
export class PublicModule {}
