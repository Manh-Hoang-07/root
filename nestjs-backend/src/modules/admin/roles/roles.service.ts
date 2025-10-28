import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';
import { Role } from '../../../entities/role.entity';

@Injectable()
export class RolesService {
  constructor(
    @InjectRepository(Role)
    private readonly roleRepository: Repository<Role>,
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

  // Roles
  async getRoles(filters: any = {}, perPage: number = 20, page: number = 1) {
    return this.list(this.roleRepository, filters, perPage, page);
  }

  async getRole(id: string) {
    return this.roleRepository.findOne({
      where: { id: Number(id) } as any,
    });
  }

  async createRole(createRoleDto: any) {
    const role = this.roleRepository.create(createRoleDto as any);
    return this.roleRepository.save(role);
  }

  async updateRole(id: string, updateRoleDto: any) {
    const existingRole = await this.roleRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingRole) {
      return null;
    }

    const updatedRole = this.roleRepository.merge(existingRole, updateRoleDto as any);
    return this.roleRepository.save(updatedRole);
  }

  async deleteRole(id: string) {
    const existingRole = await this.roleRepository.findOne({
      where: { id: Number(id) } as any,
    });

    if (!existingRole) {
      return null;
    }

    if (this.hasProperty(this.roleRepository, 'deleted_at')) {
      (existingRole as any).deleted_at = new Date();
      return this.roleRepository.save(existingRole);
    } else {
      return this.roleRepository.remove(existingRole);
    }
  }
}
