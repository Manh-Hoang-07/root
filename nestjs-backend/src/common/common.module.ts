import { Global, Module } from '@nestjs/common';
import { AuthService } from './services/auth.service';

/**
 * Common Module - Cung cấp các services dùng chung
 * Module này được đặt là Global để có thể inject vào bất kỳ module nào
 */
@Global()
@Module({
  providers: [AuthService],
  exports: [AuthService],
})
export class CommonModule {}

