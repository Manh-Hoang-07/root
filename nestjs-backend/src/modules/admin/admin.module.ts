import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';

// Controllers
import { PostsController } from './controllers/posts.controller';
import { UsersController } from './controllers/users.controller';
import { ProductsController } from './controllers/products.controller';
import { RolesController } from './controllers/roles.controller';
import { PermissionsController } from './controllers/permissions.controller';
import { OrdersController } from './controllers/orders.controller';
import { ContactsController } from './controllers/contacts.controller';
import { MenusController } from './controllers/menus.controller';
import { PostCategoriesController } from './controllers/post-categories.controller';
import { PostTagsController } from './controllers/post-tags.controller';
// import { ProductCategoriesController } from './controllers/product-categories.controller';
import { SystemConfigsController } from './controllers/system-configs.controller';

// Services
import { PostsService } from './services/posts.service';
import { UsersService } from './services/users.service';
import { ProductsService } from './services/products.service';
import { RolesService } from './services/roles.service';
import { PermissionsService } from './services/permissions.service';
import { OrdersService } from './services/orders.service';
import { ContactsService } from './services/contacts.service';
import { MenusService } from './services/menus.service';
import { PostCategoriesService } from './services/post-categories.service';
import { PostTagsService } from './services/post-tags.service';
// import { ProductCategoriesService } from './services/product-categories.service';
import { SystemConfigsService } from './services/system-configs.service';

// Import all entities
import { Post } from '../../entities/post.entity';
import { PostCategory } from '../../entities/post-category.entity';
import { PostTag } from '../../entities/post-tag.entity';
import { User } from '../../entities/user.entity';
import { Product } from '../../entities/product.entity';
import { ProductCategory } from '../../entities/product-category.entity';
import { Order } from '../../entities/order.entity';
import { Contact } from '../../entities/contact.entity';
import { Menu } from '../../entities/menu.entity';
import { SystemConfig } from '../../entities/system-config.entity';
import { Role } from '../../entities/role.entity';
import { Permission } from '../../entities/permission.entity';
import { NotificationTemplate } from '../../entities/notification-template.entity';
import { ProductAttribute } from '../../entities/product-attribute.entity';
import { ProductAttributeValue } from '../../entities/product-attribute-value.entity';
import { ProductVariant } from '../../entities/product-variant.entity';
import { ProductVariantAttribute } from '../../entities/product-variant-attribute.entity';
import { OrderItem } from '../../entities/order-item.entity';
import { Profile } from '../../entities/profile.entity';

@Module({
  imports: [
    TypeOrmModule.forFeature([
      Post,
      PostCategory,
      PostTag,
      User,
      Product,
      ProductCategory,
      Order,
      OrderItem,
      Contact,
      Menu,
      SystemConfig,
      Role,
      Permission,
      NotificationTemplate,
      ProductAttribute,
      ProductAttributeValue,
      ProductVariant,
      ProductVariantAttribute,
      Profile,
    ]),
  ],
  controllers: [
    PostsController,
    UsersController,
    ProductsController,
    RolesController,
    PermissionsController,
    OrdersController,
    ContactsController,
    MenusController,
    PostCategoriesController,
    PostTagsController,
    // ProductCategoriesController,
    SystemConfigsController,
  ],
  providers: [
    PostsService,
    UsersService,
    ProductsService,
    RolesService,
    PermissionsService,
    OrdersService,
    ContactsService,
    MenusService,
    PostCategoriesService,
    PostTagsService,
    // ProductCategoriesService,
    SystemConfigsService,
  ],
  exports: [
    PostsService,
    UsersService,
    ProductsService,
    RolesService,
    PermissionsService,
    OrdersService,
    ContactsService,
    MenusService,
    PostCategoriesService,
    PostTagsService,
    // ProductCategoriesService,
    SystemConfigsService,
  ],
})
export class AdminModule {}
