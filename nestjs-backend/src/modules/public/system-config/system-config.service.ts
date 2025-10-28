import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { SystemConfig } from '../../../shared/entities/system-config.entity';
import { BaseService } from '../../../common/base/base-public.service';

@Injectable()
export class SystemConfigService extends BaseService<SystemConfig> {
  constructor(
    @InjectRepository(SystemConfig)
    configRepository: Repository<SystemConfig>,
  ) {
    super(configRepository);
  }

  protected getAvailableRelations(): string[] {
    return [];
  }

  async getConfig() {
    return this.getSimpleList(
      { status: 'active' } as any,
      {},
    );
  }

  async getConfigByKey(key: string) {
    return this.repository.findOne({
      where: { 
        key,
        status: 'active'
      } as any,
    });
  }
}

