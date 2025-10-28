import { Repository, EntityTarget, FindOptionsWhere } from 'typeorm';
import { BaseEntity } from './base.entity';

export abstract class BaseRepository<T extends BaseEntity> extends Repository<T> {
  abstract getEntity(): EntityTarget<T>;

  async findById(id: number): Promise<T | null> {
    return this.findOne({
      where: { id } as FindOptionsWhere<T>,
    });
  }

  async softDelete(id: number): Promise<void> {
    await this.softDelete(id);
  }
}
