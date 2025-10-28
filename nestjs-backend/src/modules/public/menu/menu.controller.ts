import { Controller, Get } from '@nestjs/common';
import { MenuService } from './menu.service';

@Controller('public/menu')
export class MenuController {
  constructor(private readonly menuService: MenuService) {}

  @Get()
  async list() {
    const data = await this.menuService.getMenu();
    return { data };
  }
}
