import { Controller, UseGuards } from '@nestjs/common';
import { RolesService } from '../services/roles.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/roles')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class RolesController extends BaseController<any> {
  protected service = this.rolesService;
  
  constructor(private readonly rolesService: RolesService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Role';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getRoles';
  }

  protected getGetMethodName(): string {
    return 'getRole';
  }

  protected getCreateMethodName(): string {
    return 'createRole';
  }

  protected getUpdateMethodName(): string {
    return 'updateRole';
  }

  protected getDeleteMethodName(): string {
    return 'deleteRole';
  }
}
