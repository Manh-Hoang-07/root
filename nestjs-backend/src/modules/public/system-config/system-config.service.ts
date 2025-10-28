import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { SystemConfig } from '../../../shared/entities/system-config.entity';

@Injectable()
export class SystemConfigService {
  constructor(
    @InjectRepository(SystemConfig)
    private readonly configRepository: Repository<SystemConfig>,
  ) {}

  async getConfig() {
    return this.configRepository.find({
      where: { status: true } as any,
    });
  }

  async getConfigByKey(key: string) {
    return this.configRepository.findOne({
      where: { key, status: true } as any,
    });
  }
}

