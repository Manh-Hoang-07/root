import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ProductController } from './product.controller';
import { ProductService } from './product.service';
import { Product } from '../../../shared/entities/product.entity';
import { logToFile } from '../../../shared/utils/file-logger.util';

@Module({
  imports: [TypeOrmModule.forFeature([Product])],
  controllers: [ProductController],
  providers: [ProductService],
  exports: [ProductService],
})
export class ProductModule {
  constructor() {
    console.log('========================================');
    console.log('[ProductModule] MODULE CONSTRUCTOR CALLED!');
    console.log('[ProductModule] ProductController should be registered at: /api/public/products');
    console.log('========================================');
    logToFile('[ProductModule] Module initialized and ProductController should be registered');
    console.log('[ProductModule] Module initialized - checking if controller is loaded');
  }
}
