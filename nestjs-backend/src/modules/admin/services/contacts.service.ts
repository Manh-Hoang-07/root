import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';
import { Contact } from '../../../entities/contact.entity';

@Injectable()
export class ContactsService {
  constructor(
    @InjectRepository(Contact)
    private readonly contactRepository: Repository<Contact>,
  ) {}

  // Generic list method
  private async list<T>(
    repository: Repository<T>,
    filters: any = {},
    perPage: number = 20,
    page: number = 1,
  ) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    const where: FindOptionsWhere<T> = {};

    if (filters.status) {
      (where as any)['status'] = filters.status;
    }

    if (filters.search) {
      const searchFields = ['name', 'title', 'email'];
      for (const field of searchFields) {
        if (this.hasProperty(repository, field)) {
          (where as any)[field] = ILike(`%${filters.search}%`);
          break;
        }
      }
    }

    const findOptions: FindManyOptions<T> = {
      where,
      order: { created_at: 'DESC' } as any,
      skip,
      take: validPerPage,
    };

    const [data, total] = await repository.findAndCount(findOptions);

    return {
      data,
      meta: {
        total,
        per_page: validPerPage,
        current_page: page,
        last_page: Math.ceil(total / validPerPage),
        from: skip + 1,
        to: Math.min(skip + validPerPage, total),
      },
    };
  }

  // Check if entity has a specific property
  private hasProperty<T>(repository: Repository<T>, property: string): boolean {
    const metadata = repository.metadata;
    return metadata.columns.some(column => column.propertyName === property);
  }

  // Contacts
  async getContacts(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.list(this.contactRepository, filters, perPage, page);
  }

  async getContact(id: string) {
    return this.contactRepository.findOne({
      where: { id: Number(id) } as any,
    });
  }

  async updateContact(id: string, updateContactDto: any) {
    const existingContact = await this.contactRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingContact) {
      return null;
    }

    const updatedContact = this.contactRepository.merge(existingContact, updateContactDto as any);
    return this.contactRepository.save(updatedContact);
  }
}
