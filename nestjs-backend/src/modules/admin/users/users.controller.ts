import { Controller, UseGuards } from '@nestjs/common';
import { UsersService } from '../services/users.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/users')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class UsersController extends BaseController<any> {
  protected service = this.usersService;
  
  constructor(private readonly usersService: UsersService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'User';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'getUsers';
  }

  protected getGetMethodName(): string {
    return 'getUser';
  }

  // Users không có create, chỉ có update và delete
  protected getCreateMethodName(): string {
    return null; // Disable create
  }

  protected getUpdateMethodName(): string {
    return 'updateUser';
  }

  protected getDeleteMethodName(): string {
    return 'deleteUser';
  }
}
