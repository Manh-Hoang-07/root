import { Controller, UseGuards } from '@nestjs/common';
import { PermissionsService } from './permissions.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/permissions')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class PermissionsController extends BaseController<any> {
  protected service = this.permissionsService;
  
  constructor(private readonly permissionsService: PermissionsService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Permission';
  }
}
