import { Injectable, NotFoundException, BadRequestException } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, In } from 'typeorm';
import { Role } from '../../shared/entities/role.entity';
import { Permission } from '../../shared/entities/permission.entity';
import { User } from '../../shared/entities/user.entity';

@Injectable()
export class RbacService {
  constructor(
    @InjectRepository(Role) private readonly roleRepo: Repository<Role>,
    @InjectRepository(Permission) private readonly permRepo: Repository<Permission>,
    @InjectRepository(User) private readonly userRepo: Repository<User>,
  ) {}

  async createRole(data: { code: string; name?: string | null; parent_id?: number | null; }): Promise<Role> {
    const exists = await this.roleRepo.findOne({ where: { code: data.code } });
    if (exists) throw new BadRequestException('Role name already exists for guard');
    let parent: Role | null = null;
    if (data.parent_id) {
      parent = await this.roleRepo.findOne({ where: { id: data.parent_id } }) || null;
    }
    const role = this.roleRepo.create({
      code: data.code,
      name: data.name ?? null,
      parent: parent ?? null,
      status: 'active',
    });
    return this.roleRepo.save(role);
  }

  async createPermission(data: { code: string; name?: string | null; parent_id?: number | null; }): Promise<Permission> {
    const exists = await this.permRepo.findOne({ where: { code: data.code } });
    if (exists) throw new BadRequestException('Permission name already exists for guard');
    let parent: Permission | null = null;
    if (data.parent_id) {
      parent = await this.permRepo.findOne({ where: { id: data.parent_id } }) || null;
    }
    const permission = this.permRepo.create({
      code: data.code,
      name: data.name ?? null,
      parent: parent ?? null,
      status: 'active',
    });
    return this.permRepo.save(permission);
  }

  async assignPermissionsToRole(roleId: number, permissionIds: number[]): Promise<Role> {
    const role = await this.roleRepo.findOne({ where: { id: roleId }, relations: { permissions: true } });
    if (!role) throw new NotFoundException('Role not found');
    const perms = await this.permRepo.findBy({ id: In(permissionIds) });
    role.permissions = [...(role.permissions ?? []), ...perms].filter((v, i, a) => a.findIndex(b => b.id === v.id) === i);
    return this.roleRepo.save(role);
  }

  async assignRolesToUser(userId: number, roleIds: number[]): Promise<User> {
    const user = await this.userRepo.findOne({ where: { id: userId }, relations: { roles: true } });
    if (!user) throw new NotFoundException('User not found');
    const roles = await this.roleRepo.findBy({ id: In(roleIds) });
    user.roles = [...(user.roles ?? []), ...roles].filter((v, i, a) => a.findIndex(b => String(b.id) === String(v.id)) === i);
    return this.userRepo.save(user);
  }

  async assignPermissionsToUser(userId: number, permissionIds: number[]): Promise<User> {
    const user = await this.userRepo.findOne({ where: { id: userId }, relations: { direct_permissions: true } });
    if (!user) throw new NotFoundException('User not found');
    const perms = await this.permRepo.findBy({ id: In(permissionIds) });
    user.direct_permissions = [...(user.direct_permissions ?? []), ...perms].filter((v, i, a) => a.findIndex(b => b.id === v.id) === i);
    return this.userRepo.save(user);
  }

  async getUserEffectivePermissions(userId: number): Promise<Permission[]> {
    const user = await this.userRepo.findOne({ where: { id: userId }, relations: { roles: { permissions: true }, direct_permissions: true } });
    if (!user) throw new NotFoundException('User not found');
    const rolePerms = (user.roles ?? []).flatMap((r) => r.permissions ?? []);
    const base = [...(user.direct_permissions ?? []), ...rolePerms];
    const withHierarchy = await this.expandPermissionHierarchy(base);
    // unique by id
    const unique = withHierarchy.filter((v, i, a) => a.findIndex(b => b.id === v.id) === i);
    return unique;
  }

  async userHasPermissions(userId: number, required: string[]): Promise<boolean> {
    const perms = await this.getUserEffectivePermissions(userId);
    const codes = new Set(perms.filter(p => p.status === 'active').map(p => p.code));
    return required.every((r) => codes.has(r));
  }

  async userHasRoles(userId: number, required: string[]): Promise<boolean> {
    const user = await this.userRepo.findOne({ where: { id: userId }, relations: { roles: { parent: true } } });
    if (!user) return false;
    const roles = await this.expandRoleHierarchy(user.roles ?? []);
    const codes = new Set(roles.filter(r => r.status === 'active').map(r => r.code));
    return required.every((r) => codes.has(r));
  }

  private async expandRoleHierarchy(roles: Role[]): Promise<Role[]> {
    const visited = new Map<number, Role>();
    const stack = [...roles];
    while (stack.length) {
      const r = stack.pop()!;
      if (visited.has(r.id)) continue;
      visited.set(r.id, r);
      if (!r.parent && r.parent !== null) {
        const loaded = await this.roleRepo.findOne({ where: { id: r.id }, relations: { parent: true } });
        if (loaded?.parent) stack.push(loaded.parent);
      } else if (r.parent) {
        stack.push(r.parent);
      }
    }
    return Array.from(visited.values());
  }

  private async expandPermissionHierarchy(perms: Permission[]): Promise<Permission[]> {
    const visited = new Map<number, Permission>();
    const stack = [...perms];
    while (stack.length) {
      const p = stack.pop()!;
      if (visited.has(p.id)) continue;
      visited.set(p.id, p);
      if (!p.parent && p.parent !== null) {
        const loaded = await this.permRepo.findOne({ where: { id: p.id }, relations: { parent: true } });
        if (loaded?.parent) stack.push(loaded.parent);
      } else if (p.parent) {
        stack.push(p.parent);
      }
    }
    return Array.from(visited.values());
  }
}


