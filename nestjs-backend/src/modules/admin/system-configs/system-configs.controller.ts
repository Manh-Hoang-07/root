import { Controller, UseGuards } from '@nestjs/common';
import { SystemConfigsService } from '../services/system-configs.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/system-configs')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class SystemConfigsController extends BaseController<any> {
  protected service = this.systemConfigsService;
  
  constructor(private readonly systemConfigsService: SystemConfigsService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'System config';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getSystemConfigs';
  }

  protected getGetMethodName(): string {
    return 'getSystemConfig';
  }

  protected getCreateMethodName(): string {
    return 'createSystemConfig';
  }

  protected getUpdateMethodName(): string {
    return 'updateSystemConfig';
  }

  protected getDeleteMethodName(): string {
    return 'deleteSystemConfig';
  }
}
