import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, In } from 'typeorm';
import { Role } from '../../../shared/entities/role.entity';
import { Permission } from '../../../shared/entities/permission.entity';
import { User } from '../../../shared/entities/user.entity';
import { ResponseUtil, ApiResponse } from '../../../common/utils/response.util';

/**
 * Service quản lý RBAC (Role-Based Access Control)
 * Bao gồm:
 * - Kiểm tra quyền và vai trò của user
 * - Quản lý roles cho user (sync, add, remove)
 */
@Injectable()
export class RbacService {
  constructor(
    @InjectRepository(Role) private readonly roleRepo: Repository<Role>,
    @InjectRepository(Permission) private readonly permRepo: Repository<Permission>,
    @InjectRepository(User) private readonly userRepo: Repository<User>,
  ) {}

  // ==================== PHẦN KIỂM TRA QUYỀN ====================

  /**
   * Lấy tất cả permissions hiệu quả của user (CHỈ TỪ ROLES)
   * - Chỉ trả về permissions có status = 'active'
   * - Bao gồm cả permissions từ parent hierarchy (nhưng chỉ nếu parent cũng active)
   * @returns Mảng permissions, nếu user không tồn tại thì trả về mảng rỗng
   */
  async getUserEffectivePermissions(userId: number): Promise<Permission[]> {
    const user = await this.userRepo.findOne({
      where: { id: userId },
      relations: { roles: { permissions: true } },
    });

    if (!user) {
      return [];
    }

    // Chỉ lấy permissions từ active roles
    const activeRoles = (user.roles ?? []).filter((r) => r.status === 'active');
    const rolePerms = activeRoles.flatMap((r) =>
      (r.permissions ?? []).filter((p) => p.status === 'active'),
    );

    // Expand hierarchy và loại bỏ duplicate
    const withHierarchy = await this.expandPermissionHierarchy(rolePerms);
    const unique = this.uniqueById(withHierarchy).filter((p) => p.status === 'active');

    return unique;
  }

  /**
   * Kiểm tra user có đủ permissions hay không
   * @param userId - ID của user
   * @param required - Mảng permission codes cần kiểm tra
   * @returns true nếu user có TẤT CẢ permissions, false nếu thiếu bất kỳ permission nào
   */
  async userHasPermissions(userId: number, required: string[]): Promise<boolean> {
    if (required.length === 0) return true;

    const perms = await this.getUserEffectivePermissions(userId);
    const userPermCodes = new Set(perms.map((p) => p.code));

    return required.every((code) => userPermCodes.has(code));
  }

  /**
   * Kiểm tra user có đủ roles hay không
   * @param userId - ID của user
   * @param required - Mảng role codes cần kiểm tra
   * @returns true nếu user có TẤT CẢ roles (kể cả từ parent hierarchy), false nếu thiếu
   */
  async userHasRoles(userId: number, required: string[]): Promise<boolean> {
    if (required.length === 0) return true;

    const user = await this.userRepo.findOne({
      where: { id: userId },
      relations: { roles: { parent: true } },
    });

    if (!user) return false;

    // Chỉ lấy active roles và expand hierarchy
    const activeRoles = (user.roles ?? []).filter((r) => r.status === 'active');
    const allRoles = await this.expandRoleHierarchy(activeRoles);
    const userRoleCodes = new Set(allRoles.map((r) => r.code));

    return required.every((code) => userRoleCodes.has(code));
  }

  // ==================== PHẦN QUẢN LÝ ROLES CHO USER ====================

  /**
   * Sync roles cho user (thay thế toàn bộ)
   */
  async syncRoles(userId: number, roleIds: number[]): Promise<ApiResponse<User>> {
    try {
      const user = await this.userRepo.findOne({
        where: { id: userId },
        relations: { roles: true },
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
   * Thêm roles cho user (append)
   */
  async addRoles(userId: number, roleIds: number[]): Promise<ApiResponse<User>> {
    try {
      const user = await this.userRepo.findOne({
        where: { id: userId },
        relations: { roles: true },
      });

      if (!user) {
        return ResponseUtil.notFound('User not found');
      }

      const roles = await this.roleRepo.findBy({ id: In(roleIds) });
      const existingRoleIds = new Set((user.roles || []).map((r) => r.id));
      const newRoles = roles.filter((r) => !existingRoleIds.has(r.id));

      user.roles = [...(user.roles || []), ...newRoles];
      const saved = await this.userRepo.save(user);
      return ResponseUtil.success(saved, 'Roles added successfully');
    } catch (error) {
      return ResponseUtil.error(`Failed to add roles: ${error.message}`, 'ADD_ROLES_FAILED');
    }
  }

  /**
   * Xóa roles khỏi user
   */
  async removeRoles(userId: number, roleIds: number[]): Promise<ApiResponse<User>> {
    try {
      const user = await this.userRepo.findOne({
        where: { id: userId },
        relations: { roles: true },
      });

      if (!user) {
        return ResponseUtil.notFound('User not found');
      }

      if (user.roles) {
        user.roles = user.roles.filter((r) => !roleIds.includes(r.id));
      }

      const saved = await this.userRepo.save(user);
      return ResponseUtil.success(saved, 'Roles removed successfully');
    } catch (error) {
      return ResponseUtil.error(`Failed to remove roles: ${error.message}`, 'REMOVE_ROLES_FAILED');
    }
  }

  /**
   * Lấy thông tin phân quyền của user (CHỈ QUA ROLES)
   * Phân quyền chỉ được thực hiện qua roles, không có direct permissions
   */
  async getUserPermissions(userId: number): Promise<ApiResponse<any>> {
    try {
      const user = await this.userRepo.findOne({
        where: { id: userId },
        relations: {
          roles: { permissions: true, parent: true },
        },
      });

      if (!user) {
        return ResponseUtil.notFound('User not found');
      }

      return ResponseUtil.success({
        user_id: user.id,
        roles: user.roles || [],
        // Không trả về direct_permissions vì phân quyền chỉ qua roles
      });
    } catch (error) {
      return ResponseUtil.error(`Failed to get user permissions: ${error.message}`, 'GET_PERMISSIONS_FAILED');
    }
  }

  // ==================== PRIVATE HELPER METHODS ====================

  /**
   * Expand role hierarchy - lấy cả parent roles
   * Chỉ trả về roles có status = 'active'
   */
  private async expandRoleHierarchy(roles: Role[]): Promise<Role[]> {
    const visited = new Map<number, Role>();
    const stack = [...roles];

    while (stack.length > 0) {
      const role = stack.pop()!;

      if (visited.has(role.id)) continue;

      // Chỉ thêm active roles
      if (role.status === 'active') {
        visited.set(role.id, role);
      }

      // Load parent nếu chưa có
      if (!role.parent && role.parent !== null) {
        const loaded = await this.roleRepo.findOne({
          where: { id: role.id, status: 'active' },
          relations: { parent: true },
        });

        if (loaded?.parent && loaded.parent.status === 'active') {
          stack.push(loaded.parent);
        }
      } else if (role.parent && role.parent.status === 'active') {
        stack.push(role.parent);
      }
    }

    return Array.from(visited.values());
  }

  /**
   * Expand permission hierarchy - lấy cả parent permissions
   * Chỉ trả về permissions có status = 'active'
   */
  private async expandPermissionHierarchy(perms: Permission[]): Promise<Permission[]> {
    const visited = new Map<number, Permission>();
    const stack = [...perms];

    while (stack.length > 0) {
      const perm = stack.pop()!;

      if (visited.has(perm.id)) continue;

      // Chỉ thêm active permissions
      if (perm.status === 'active') {
        visited.set(perm.id, perm);
      }

      // Load parent nếu chưa có
      if (!perm.parent && perm.parent !== null) {
        const loaded = await this.permRepo.findOne({
          where: { id: perm.id, status: 'active' },
          relations: { parent: true },
        });

        if (loaded?.parent && loaded.parent.status === 'active') {
          stack.push(loaded.parent);
        }
      } else if (perm.parent && perm.parent.status === 'active') {
        stack.push(perm.parent);
      }
    }

    return Array.from(visited.values());
  }

  /**
   * Loại bỏ duplicate bằng id
   */
  private uniqueById<T extends { id: number }>(items: T[]): T[] {
    const seen = new Set<number>();
    return items.filter((item) => {
      if (seen.has(item.id)) return false;
      seen.add(item.id);
      return true;
    });
  }
}

