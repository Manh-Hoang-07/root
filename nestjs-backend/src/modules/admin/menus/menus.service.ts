import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';
import { Menu } from '../../../entities/menu.entity';

@Injectable()
export class MenusService {
  constructor(
    @InjectRepository(Menu)
    private readonly menuRepository: Repository<Menu>,
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

  // Menus
  async getMenus(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.list(this.menuRepository, filters, perPage, page);
  }

  async getMenu(id: string) {
    return this.menuRepository.findOne({
      where: { id: Number(id) } as any,
    });
  }

  async createMenu(createMenuDto: any) {
    const menu = this.menuRepository.create(createMenuDto as any);
    return this.menuRepository.save(menu);
  }

  async updateMenu(id: string, updateMenuDto: any) {
    const existingMenu = await this.menuRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingMenu) {
      return null;
    }

    const updatedMenu = this.menuRepository.merge(existingMenu, updateMenuDto as any);
    return this.menuRepository.save(updatedMenu);
  }

  async deleteMenu(id: string) {
    const existingMenu = await this.menuRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingMenu) {
      return null;
    }

    if (this.hasProperty(this.menuRepository, 'deleted_at')) {
      (existingMenu as any).deleted_at = new Date();
      return this.menuRepository.save(existingMenu);
    } else {
      return this.menuRepository.remove(existingMenu);
    }
  }
}
