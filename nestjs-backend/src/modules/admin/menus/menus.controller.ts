import { Controller, UseGuards } from '@nestjs/common';
import { MenusService } from './menus.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/menus')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class MenusController extends BaseController<any> {
  protected service = this.menusService;
  
  constructor(private readonly menusService: MenusService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Menu';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getMenus';
  }

  protected getGetMethodName(): string {
    return 'getMenu';
  }

  protected getCreateMethodName(): string {
    return 'createMenu';
  }

  protected getUpdateMethodName(): string {
    return 'updateMenu';
  }

  protected getDeleteMethodName(): string {
    return 'deleteMenu';
  }
}
