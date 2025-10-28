import { Module } from '@nestjs/common';
import { PostModule } from './post/post.module';
import { ProductModule } from './product/product.module';
import { logToFile } from '../../shared/utils/file-logger.util';

@Module({
  imports: [
    PostModule,
    ProductModule,
  ],
})
export class PublicModule {
  constructor() {
    console.log('========================================');
    console.log('[PublicModule] MODULE CONSTRUCTOR CALLED!');
    console.log('========================================');
    logToFile('[PublicModule] Module initialized with PostModule and ProductModule');
    console.log('[PublicModule] Module initialized - ProductModule should be loaded');
  }
}