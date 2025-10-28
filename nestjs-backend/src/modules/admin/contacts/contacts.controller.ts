import { Controller, UseGuards } from '@nestjs/common';
import { ContactsService } from './contacts.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';
import { BaseController } from '../../../common/base/base.controller';

@Controller('admin/contacts')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class ContactsController extends BaseController<any> {
  protected service = this.contactsService;
  
  constructor(private readonly contactsService: ContactsService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Contact';
  }

  // Override để customize method names
  protected getListMethodName(): string {
    return 'list';
  }

  protected getGetMethodName(): string {
    return 'get';
  }

  // Contacts chỉ có GET và PUT, không có create và delete
  protected getCreateMethodName(): string {
    return null; // Disable create
  }

  protected getUpdateMethodName(): string {
    return 'update';
  }

  protected getDeleteMethodName(): string {
    return null; // Disable delete
  }
}
