import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { UserController } from './user.controller';
import { UserService } from './user.service';
import { ProfilesController } from './controllers/profiles.controller';
import { ProfilesService } from './services/profiles.service';

// Import all entities
import { Post } from '../../entities/post.entity';
import { PostCategory } from '../../entities/post-category.entity';
import { PostTag } from '../../entities/post-tag.entity';
import { User } from '../../entities/user.entity';
import { Product } from '../../entities/product.entity';
import { Order } from '../../entities/order.entity';
import { Contact } from '../../entities/contact.entity';
import { Cart } from '../../entities/cart.entity';
import { Profile } from '../../entities/profile.entity';

@Module({
  imports: [
    TypeOrmModule.forFeature([
      Post,
      PostCategory,
      PostTag,
      User,
      Product,
      Order,
      Contact,
      Cart,
      Profile,
    ]),
  ],
  controllers: [UserController, ProfilesController],
  providers: [UserService, ProfilesService],
  exports: [UserService, ProfilesService],
})
export class UserModule {}