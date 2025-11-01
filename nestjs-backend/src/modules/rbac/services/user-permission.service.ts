import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, In } from 'typeorm';
import { User } from '../../../shared/entities/user.entity';
import { Role } from '../../../shared/entities/role.entity';
import { Permission } from '../../../shared/entities/permission.entity';
import { ResponseUtil, ApiResponse } from '../../../common/utils/response.util';

@Injectable()
export class UserPermissionService {
  constructor(
    @InjectRepository(User) private readonly userRepo: Repository<User>,
    @InjectRepository(Role) private readonly roleRepo: Repository<Role>,
    @InjectRepository(Permission) private readonly permRepo: Repository<Permission>,
  ) {}

  /**
   * Sync roles cho user (thay thế toàn bộ)
   */
  async syncRoles(userId: number, roleIds: number[]): Promise<ApiResponse<User>> {
    try {
      const user = await this.userRepo.findOne({ 
        where: { id: userId }, 
        relations: { roles: true } 
      });
      
      if (!user) {
        return ResponseUtil.notFound('User not found');
      }

      // Validate role IDs
      if (roleIds.length > 0) {
        const roles = await this.roleRepo.findBy({ id: In(roleIds) });
        if (roles.length !== roleIds.length) {
          return ResponseUtil.error('Some role IDs are invalid', 'INVALID_ROLE_IDS');
        }
        user.roles = roles;
      } else {
        user.roles = [];
      }

      const saved = await this.userRepo.save(user);
      return ResponseUtil.success(saved, 'Roles synced successfully');
    } catch (error) {
      return ResponseUtil.error(`Failed to sync roles: ${error.message}`, 'SYNC_ROLES_FAILED');
    }
  }

  /**
   * Sync permissions cho user (thay thế toàn bộ direct permissions)
   */
  async syncPermissions(userId: number, permissionIds: number[]): Promise<ApiResponse<User>> {
    try {
      const user = await this.userRepo.findOne({ 
        where: { id: userId }, 
        relations: { direct_permissions: true } 
      });
      
      if (!user) {
        return ResponseUtil.notFound('User not found');
      }

      // Validate permission IDs
      if (permissionIds.length > 0) {
        const permissions = await this.permRepo.findBy({ id: In(permissionIds) });
        if (permissions.length !== permissionIds.length) {
          return ResponseUtil.error('Some permission IDs are invalid', 'INVALID_PERMISSION_IDS');
        }
        user.direct_permissions = permissions;
      } else {
        user.direct_permissions = [];
      }

      const saved = await this.userRepo.save(user);
      return ResponseUtil.success(saved, 'Permissions synced successfully');
    } catch (error) {
      return ResponseUtil.error(`Failed to sync permissions: ${error.message}`, 'SYNC_PERMISSIONS_FAILED');
    }
  }

  /**
   * Thêm roles cho user (append)
   */
  async addRoles(userId: number, roleIds: number[]): Promise<ApiResponse<User>> {
    try {
      const user = await this.userRepo.findOne({ 
        where: { id: userId }, 
        relations: { roles: true } 
      });
      
      if (!user) {
        return ResponseUtil.notFound('User not found');
      }

      const roles = await this.roleRepo.findBy({ id: In(roleIds) });
      const existingRoleIds = new Set((user.roles || []).map(r => r.id));
      const newRoles = roles.filter(r => !existingRoleIds.has(r.id));
      
      user.roles = [...(user.roles || []), ...newRoles];
      const saved = await this.userRepo.save(user);
      return ResponseUtil.success(saved, 'Roles added successfully');
    } catch (error) {
      return ResponseUtil.error(`Failed to add roles: ${error.message}`, 'ADD_ROLES_FAILED');
    }
  }

  /**
   * Thêm permissions cho user (append)
   */
  async addPermissions(userId: number, permissionIds: number[]): Promise<ApiResponse<User>> {
    try {
      const user = await this.userRepo.findOne({ 
        where: { id: userId }, 
        relations: { direct_permissions: true } 
      });
      
      if (!user) {
        return ResponseUtil.notFound('User not found');
      }

      const permissions = await this.permRepo.findBy({ id: In(permissionIds) });
      const existingPermIds = new Set((user.direct_permissions || []).map(p => p.id));
      const newPermissions = permissions.filter(p => !existingPermIds.has(p.id));
      
      user.direct_permissions = [...(user.direct_permissions || []), ...newPermissions];
      const saved = await this.userRepo.save(user);
      return ResponseUtil.success(saved, 'Permissions added successfully');
    } catch (error) {
      return ResponseUtil.error(`Failed to add permissions: ${error.message}`, 'ADD_PERMISSIONS_FAILED');
    }
  }

  /**
   * Xóa roles khỏi user
   */
  async removeRoles(userId: number, roleIds: number[]): Promise<ApiResponse<User>> {
    try {
      const user = await this.userRepo.findOne({ 
        where: { id: userId }, 
        relations: { roles: true } 
      });
      
      if (!user) {
        return ResponseUtil.notFound('User not found');
      }

      if (user.roles) {
        user.roles = user.roles.filter(r => !roleIds.includes(r.id));
      }

      const saved = await this.userRepo.save(user);
      return ResponseUtil.success(saved, 'Roles removed successfully');
    } catch (error) {
      return ResponseUtil.error(`Failed to remove roles: ${error.message}`, 'REMOVE_ROLES_FAILED');
    }
  }

  /**
   * Xóa permissions khỏi user
   */
  async removePermissions(userId: number, permissionIds: number[]): Promise<ApiResponse<User>> {
    try {
      const user = await this.userRepo.findOne({ 
        where: { id: userId }, 
        relations: { direct_permissions: true } 
      });
      
      if (!user) {
        return ResponseUtil.notFound('User not found');
      }

      if (user.direct_permissions) {
        user.direct_permissions = user.direct_permissions.filter(p => !permissionIds.includes(p.id));
      }

      const saved = await this.userRepo.save(user);
      return ResponseUtil.success(saved, 'Permissions removed successfully');
    } catch (error) {
      return ResponseUtil.error(`Failed to remove permissions: ${error.message}`, 'REMOVE_PERMISSIONS_FAILED');
    }
  }

  /**
   * Lấy thông tin phân quyền của user
   */
  async getUserPermissions(userId: number): Promise<ApiResponse<any>> {
    try {
      const user = await this.userRepo.findOne({ 
        where: { id: userId }, 
        relations: { 
          roles: { permissions: true, parent: true },
          direct_permissions: { parent: true }
        } 
      });
      
      if (!user) {
        return ResponseUtil.notFound('User not found');
      }

      return ResponseUtil.success({
        user_id: user.id,
        roles: user.roles || [],
        direct_permissions: user.direct_permissions || [],
      });
    } catch (error) {
      return ResponseUtil.error(`Failed to get user permissions: ${error.message}`, 'GET_PERMISSIONS_FAILED');
    }
  }
}



