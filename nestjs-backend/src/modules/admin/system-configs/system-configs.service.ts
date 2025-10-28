import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';
import { SystemConfig } from '../../../entities/system-config.entity';

@Injectable()
export class SystemConfigsService {
  constructor(
    @InjectRepository(SystemConfig)
    private readonly systemConfigRepository: Repository<SystemConfig>,
  ) {}

  // Generic list method
  private async list<T>(
    repository: Repository<T>,
    filters: any = {},
    perPage: number = 20,
    page: number = 1,
  ) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const where: FindOptionsWhere<T> = {};

    if (filters.status) {
      (where as any)['status'] = filters.status;
    }

    if (filters.search) {
      const searchFields = ['name', 'title', 'email'];
      for (const field of searchFields) {
        if (this.hasProperty(repository, field)) {
          (where as any)[field] = ILike(`%${filters.search}%`);
          break;
        }
      }
    }

    const findOptions: FindManyOptions<T> = {
      where,
      order: { created_at: 'DESC' } as any,
      skip,
      take: validPerPage,
    };

    const [data, total] = await repository.findAndCount(findOptions);

    return {
      data,
      meta: {
        total,
        per_page: validPerPage,
        current_page: page,
        last_page: Math.ceil(total / validPerPage),
        from: skip + 1,
        to: Math.min(skip + validPerPage, total),
      },
    };
  }

  // Check if entity has a specific property
  private hasProperty<T>(repository: Repository<T>, property: string): boolean {
    const metadata = repository.metadata;
    return metadata.columns.some(column => column.propertyName === property);
  }

  // System Configs
  async getSystemConfigs(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.list(this.systemConfigRepository, filters, perPage, page);
  }

  async getSystemConfig(id: string) {
    return this.systemConfigRepository.findOne({
      where: { id: Number(id) } as any,
    });
  }

  async createSystemConfig(createSystemConfigDto: any) {
    const systemConfig = this.systemConfigRepository.create(createSystemConfigDto as any);
    return this.systemConfigRepository.save(systemConfig);
  }

  async updateSystemConfig(id: string, updateSystemConfigDto: any) {
    const existingSystemConfig = await this.systemConfigRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingSystemConfig) {
      return null;
    }

    const updatedSystemConfig = this.systemConfigRepository.merge(existingSystemConfig, updateSystemConfigDto as any);
    return this.systemConfigRepository.save(updatedSystemConfig);
  }

  async deleteSystemConfig(id: string) {
    const existingSystemConfig = await this.systemConfigRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingSystemConfig) {
      return null;
    }

    if (this.hasProperty(this.systemConfigRepository, 'deleted_at')) {
      (existingSystemConfig as any).deleted_at = new Date();
      return this.systemConfigRepository.save(existingSystemConfig);
    } else {
      return this.systemConfigRepository.remove(existingSystemConfig);
    }
  }
}
