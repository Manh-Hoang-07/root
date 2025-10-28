import { Module } from '@nestjs/common';
import { EnumController } from './enum.controller';
import { EnumHelperService } from '../../shared/services/enum-helper.service';

@Module({
  controllers: [EnumController],
  providers: [EnumHelperService],
  exports: [EnumHelperService],
})
export class EnumModule {}

