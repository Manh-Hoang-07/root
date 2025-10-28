import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ProductController } from './product.controller';
import { ProductService } from './product.service';
import { Product } from '../../../shared/entities/product.entity';
import { ProductCategory } from '../../../shared/entities/product-category.entity';
import { ProductVariant } from '../../../shared/entities/product-variant.entity';

@Module({
  imports: [TypeOrmModule.forFeature([Product, ProductCategory, ProductVariant])],
  controllers: [ProductController],
  providers: [ProductService],
  exports: [ProductService],
})
export class ProductModule {}
