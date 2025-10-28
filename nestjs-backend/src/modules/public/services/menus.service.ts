import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Menu } from '../../../entities/menu.entity';
import { BaseService } from './base.service';

@Injectable()
export class MenusService extends BaseService<Menu> {
  constructor(
    @InjectRepository(Menu)
    menuRepository: Repository<Menu>,
  ) {
    super(menuRepository);
  }

  protected getAvailableRelations(): string[] {
    return [];
  }

  async list() {
    return this.getSimpleList(
      { status: 'active' } as any,
      { sort_order: 'ASC' } as any,
    );
  }
}
