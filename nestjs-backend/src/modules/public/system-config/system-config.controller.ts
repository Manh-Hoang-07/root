import { Controller } from '@nestjs/common';
import { SystemConfigService } from './system-config.service';
import { BaseController } from '../../../common/base/base.controller';

@Controller('public/config')
export class SystemConfigController extends BaseController<any> {
  protected service = this.systemConfigService;
  
  constructor(private readonly systemConfigService: SystemConfigService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Config';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getConfig';
  }

  protected getGetMethodName(): string {
    return 'getConfigByKey';
  }

  // Disable các method khác
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
