import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';
import { User } from '../../../entities/user.entity';

@Injectable()
export class UsersService {
  constructor(
    @InjectRepository(User)
    private readonly userRepository: Repository<User>,
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

  // Users
  async getUsers(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.list(this.userRepository, filters, perPage, page);
  }

  async getUser(id: string) {
    return this.userRepository.findOne({
      where: { id: Number(id) } as any,
    });
  }

  async updateUser(id: string, updateUserDto: any) {
    const existingUser = await this.userRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingUser) {
      return null;
    }

    const updatedUser = this.userRepository.merge(existingUser, updateUserDto as any);
    return this.userRepository.save(updatedUser);
  }

  async deleteUser(id: string) {
    const existingUser = await this.userRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingUser) {
      return null;
    }

    if (this.hasProperty(this.userRepository, 'deleted_at')) {
      (existingUser as any).deleted_at = new Date();
      return this.userRepository.save(existingUser);
    } else {
      return this.userRepository.remove(existingUser);
    }
  }
}
