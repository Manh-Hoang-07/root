import { Controller, Post, Body } from '@nestjs/common';
import { ContactService } from './contact.service';
import { ContactDto } from './dtos/contact.dto';
import { BaseController } from '../../../common/base/base.controller';

@Controller('public/contact')
export class ContactController extends BaseController<any> {
  protected service = this.contactService;
  
  constructor(private readonly contactService: ContactService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Contact';
  }

  // Override để customize method names
  protected getCreateMethodName(): string {
    return 'submit';
  }

  // Disable các method khác
  protected getListMethodName(): string {
    return null; // Disable list
  }

  protected getGetMethodName(): string {
    return null; // Disable get
  }

  protected getUpdateMethodName(): string {
    return null; // Disable update
  }

  protected getDeleteMethodName(): string {
    return null; // Disable delete
  }

  // Custom create method với success message
  @Post()
  async create(@Body() contactDto: ContactDto) {
    const result = await this.contactService.submit(contactDto);
    return this.handleResponse(result, undefined, 'Contact submitted successfully');
  }
}
