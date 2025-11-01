import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, DeepPartial, In } from 'typeorm';
import { Role } from '../../../../shared/entities/role.entity';
import { Permission } from '../../../../shared/entities/permission.entity';
import { CrudService } from '../../../../common/base/services/crud.service';
import { ResponseRef, handleResponseRef } from '../../../../common/base/utils/response-ref.helper';
import { ApiResponse, ResponseUtil } from '../../../../common/utils/response.util';

@Injectable()
export class RoleService extends CrudService<Role> {
  private get permRepo(): Repository<Permission> {
    return this.repository.manager.getRepository(Permission);
  }

  constructor(
    @InjectRepository(Role) repository: Repository<Role>,
  ) {
    super(repository);
  }

  protected override prepareOptions(queryOptions: any = {}) {
    const base = super.prepareOptions(queryOptions);
    return {
      ...base,
      relations: ['parent', 'children'],
    } as any;
  }

  protected async beforeCreate(
    entity: Role,
    createDto: DeepPartial<Role>,
    response?: ResponseRef<Role | null>
  ): Promise<boolean> {
    // Validate code unique
    const code = (createDto as any).code;
    if (code) {
      const exists = await this.repository.findOne({ where: { code } as any });
      if (exists) {
        if (response) {
          response.message = 'Role code already exists';
          response.code = 'ROLE_CODE_EXISTS';
        }
        return false;
      }
    }

    // Handle parent_id
    const parentId = (createDto as any).parent_id;
    if (parentId) {
      const parent = await this.repository.findOne({ where: { id: parentId } as any });
      if (parent) {
        (createDto as any).parent = parent;
      }
      delete (createDto as any).parent_id;
    }

    return true;
  }

  protected async beforeUpdate(
    entity: Role,
    updateDto: DeepPartial<Role>,
    response?: ResponseRef<Role | null>
  ): Promise<boolean> {
    // Validate code unique (exclude current)
    const code = (updateDto as any).code;
    if (code && code !== entity.code) {
      const exists = await this.repository.findOne({ where: { code } as any });
      if (exists) {
        if (response) {
          response.message = 'Role code already exists';
          response.code = 'ROLE_CODE_EXISTS';
        }
        return false;
      }
    }

    // Handle parent_id
    const parentId = (updateDto as any).parent_id;
    if (parentId !== undefined) {
      if (parentId === null) {
        (updateDto as any).parent = null;
      } else {
        const parent = await this.repository.findOne({ where: { id: parentId } as any });
        if (parent) {
          (updateDto as any).parent = parent;
        }
      }
      delete (updateDto as any).parent_id;
    }

    return true;
  }

  protected async beforeDelete(
    entity: Role,
    response?: ResponseRef<null>
  ): Promise<boolean> {
    // Check if role has children
    const childrenCount = await this.repository.count({ where: { parent: { id: entity.id } } as any });
    if (childrenCount > 0) {
      if (response) {
        response.message = 'Cannot delete role with children';
        response.code = 'ROLE_HAS_CHILDREN';
      }
      return false;
    }

    // Check if role is assigned to users
    const userCount = await this.repository.manager
      .getRepository('User')
      .count({ where: { roles: { id: entity.id } } as any });
    
    if (userCount > 0) {
      if (response) {
        response.message = 'Cannot delete role assigned to users';
        response.code = 'ROLE_ASSIGNED_TO_USERS';
      }
      return false;
    }

    return true;
  }

  /**
   * Assign permissions to role (sync - replace all)
   */
  async assignPermissions(roleId: number, permissionIds: number[]): Promise<ApiResponse<Role>> {
    try {
      const role = await this.repository.findOne({ 
        where: { id: roleId } as any,
        relations: ['permissions'],
      });
      
      if (!role) {
        return ResponseUtil.notFound('Role not found');
      }

      if (permissionIds.length > 0) {
        const permissions = await this.permRepo.findBy({ id: In(permissionIds) });
        if (permissions.length !== permissionIds.length) {
          return ResponseUtil.error('Some permission IDs are invalid', 'INVALID_PERMISSION_IDS');
        }
        role.permissions = permissions;
      } else {
        role.permissions = [];
      }

      const saved = await this.repository.save(role);
      return ResponseUtil.success(saved, 'Permissions assigned successfully');
    } catch (error) {
      return ResponseUtil.error(`Failed to assign permissions: ${error.message}`, 'ASSIGN_PERMISSIONS_FAILED');
    }
  }
}


