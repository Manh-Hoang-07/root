import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ProductsController } from './products.controller';
import { ProductsService } from './products.service';
import { Product } from '../../../shared/entities/product.entity';
import { ProductCategory } from '../../../shared/entities/product-category.entity';
import { ProductVariant } from '../../../shared/entities/product-variant.entity';

@Module({
  imports: [TypeOrmModule.forFeature([Product, ProductCategory, ProductVariant])],
  controllers: [ProductsController],
  providers: [ProductsService],
  exports: [ProductsService],
})
export class ProductsModule {}
