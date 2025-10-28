import { Controller, Get, Param } from '@nestjs/common';
import { SystemConfigService } from './system-config.service';

@Controller('public/config')
export class SystemConfigController {
  constructor(private readonly systemConfigService: SystemConfigService) {}

  @Get()
  async list() {
    const data = await this.systemConfigService.getConfig();
    return { data };
  }

  @Get(':key')
  async get(@Param('key') key: string) {
    const data = await this.systemConfigService.getConfigByKey(key);
    return { data };
  }
}
