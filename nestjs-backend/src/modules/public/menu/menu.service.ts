import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { Menu } from '../../../shared/entities/menu.entity';
import { BaseService } from '../../../common/base/base-public.service';

@Injectable()
export class MenuService extends BaseService<Menu> {
  constructor(
    @InjectRepository(Menu)
    menuRepository: Repository<Menu>,
  ) {
    super(menuRepository);
  }

  protected getAvailableRelations(): string[] {
    return [];
  }

  async getMenu() {
    return this.getSimpleList(
      { status: 'active' } as any,
      { sort_order: 'ASC' } as any,
    );
  }
}

