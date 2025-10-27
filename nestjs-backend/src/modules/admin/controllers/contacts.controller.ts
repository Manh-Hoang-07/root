import { Controller, Get, Put, Param, Body, Query, UseGuards } from '@nestjs/common';
import { ContactsService } from '../services/contacts.service';
import { JwtAuthGuard } from '../../auth/guards/jwt-auth.guard';
import { RolesGuard } from '../../auth/guards/roles.guard';
import { Roles } from '../../auth/roles.decorator';

@Controller('admin/contacts')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class ContactsController {
  constructor(private readonly contactsService: ContactsService) {}

  @Get()
  async getContacts(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    return this.contactsService.getContacts(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
  }

  @Get(':id')
  async getContact(@Param('id') id: string) {
    const result = await this.contactsService.getContact(id);

    if (!result) {
      return {
        statusCode: 404,
        message: 'Contact not found',
        data: null,
      };
    }

    return { data: result };
  }

  @Put(':id')
  async updateContact(@Param('id') id: string, @Body() updateContactDto: any) {
    const result = await this.contactsService.updateContact(id, updateContactDto);
    
    if (!result) {
      return {
        statusCode: 404,
        message: 'Contact not found',
        data: null,
      };
    }

    return { data: result };
  }
}
