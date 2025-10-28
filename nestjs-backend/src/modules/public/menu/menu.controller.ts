import { Controller } from '@nestjs/common';
import { MenuService } from './menu.service';
import { BaseController } from '../../../common/base/base.controller';

@Controller('public/menu')
export class MenuController extends BaseController<any> {
  protected service = this.menuService;
  
  constructor(private readonly menuService: MenuService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Menu';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getMenu';
  }

  // Disable các method khác
  protected getGetMethodName(): string {
    return null; // Disable get
  }

  protected getCreateMethodName(): string {
    return null; // Disable create
  }

  protected getUpdateMethodName(): string {
    return null; // Disable update
  }

  protected getDeleteMethodName(): string {
    return null; // Disable delete
  }
}
