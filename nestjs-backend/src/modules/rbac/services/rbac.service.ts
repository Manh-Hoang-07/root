import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, In } from 'typeorm';
import { Role } from '../../../shared/entities/role.entity';
import { Permission } from '../../../shared/entities/permission.entity';
import { User } from '../../../shared/entities/user.entity';
import { ResponseUtil, ApiResponse } from '../../../common/utils/response.util';

/**
 * Service quản lý RBAC (Role-Based Access Control)
 * Bao gồm: kiểm tra quyền/vai trò của user và quản lý roles cho user
 */
@Injectable()
export class RbacService {
  constructor(
    @InjectRepository(Role) private readonly roleRepo: Repository<Role>,
    @InjectRepository(Permission) private readonly permRepo: Repository<Permission>,
    @InjectRepository(User) private readonly userRepo: Repository<User>,
  ) {}

  /**
   * Kiểm tra user có ÍT NHẤT 1 trong các permissions cần thiết (OR logic)
   * Điều kiện:
   * - Permission cần check phải có status = 'active'
   * - Role chứa permission phải có status = 'active'
   * - Cả role và permission đều phải thuộc user
   */
  async userHasPermissions(userId: number, required: string[]): Promise<boolean> {
    if (required.length === 0) return true;

    const ACTIVE = 'active';

    // 1️⃣ Lấy các permissions active (bao gồm parent)
    const perms = await this.permRepo.find({
      where: { code: In(required), status: ACTIVE },
      relations: { parent: true },
    });

    if (perms.length === 0) return false;

    // 2️⃣ Gom danh sách code cần check (bao gồm parent nếu active)
    const codesToCheck = new Set<string>(required);
    for (const p of perms) {
      if (p.parent?.status === ACTIVE) codesToCheck.add(p.parent.code);
    }

    // 3️⃣ Kiểm tra user có ít nhất 1 permission (OR logic)
    // innerJoin sẽ filter: chỉ trả về user nếu có role active và permission match
    // Nếu query trả về user → user có quyền (không cần load permissions vào result)
    const user = await this.userRepo
      .createQueryBuilder('user')
      .where('user.id = :userId', { userId })
      .innerJoin('user.roles', 'role', 'role.status = :status', { status: ACTIVE })
      .innerJoin('role.permissions', 'perm', 'perm.status = :status AND perm.code IN (:...codes)', {
        status: ACTIVE,
        codes: Array.from(codesToCheck),
      })
      .getOne();

    // 4️⃣ Nếu query trả về user → user có quyền (vì innerJoin đã filter đúng)
    return Boolean(user);
  }

  /**
   * Sync roles cho user (thay thế toàn bộ roles hiện tại)
   * @param userId - ID của user
   * @param roleIds - Mảng role IDs cần gán cho user (nếu rỗng thì xóa hết roles)
   */
  async syncRoles(userId: number, roleIds: number[]): Promise<ApiResponse<User>> {
    try {
      const user = await this.userRepo.findOne({ where: { id: userId } });
      if (!user) return ResponseUtil.notFound('User not found');

      if (roleIds.length > 0) {
        const roles = await this.roleRepo.findBy({ id: In(roleIds) });
        if (roles.length !== roleIds.length) {
          return ResponseUtil.error('Some role IDs are invalid', 'INVALID_ROLE_IDS');
        }
        user.roles = roles;
      } else {
        user.roles = [];
      }

      return ResponseUtil.success(await this.userRepo.save(user), 'Roles synced successfully');
    } catch (error) {
      return ResponseUtil.error(`Failed to sync roles: ${error.message}`, 'SYNC_ROLES_FAILED');
    }
  }
}

