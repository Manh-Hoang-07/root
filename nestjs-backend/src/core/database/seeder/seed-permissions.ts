import { DataSource } from 'typeorm';
import { Injectable, Logger } from '@nestjs/common';
import { Permission } from '../../../shared/entities/permission.entity';

@Injectable()
export class SeedPermissions {
  private readonly logger = new Logger(SeedPermissions.name);

  constructor(private readonly dataSource: DataSource) {}

  async seed(): Promise<void> {
    this.logger.log('Seeding permissions...');

    const permRepo = this.dataSource.getRepository(Permission);

    // Check if permissions already exist
    const existingPermissions = await permRepo.count();
    if (existingPermissions > 0) {
      this.logger.log('Permissions already seeded, skipping...');
      return;
    }

    // Seed permissions with hierarchy - theo module/chức năng
    const permissions = [
      // ========== POST MODULE ==========
      { code: 'post.manage', name: 'Quản lý Bài viết', status: 'active', parent_code: null },
      { code: 'post.create', name: 'Tạo Bài viết', status: 'active', parent_code: 'post.manage' },
      { code: 'post.read', name: 'Xem Bài viết', status: 'active', parent_code: 'post.manage' },
      { code: 'post.update', name: 'Sửa Bài viết', status: 'active', parent_code: 'post.manage' },
      { code: 'post.delete', name: 'Xóa Bài viết', status: 'active', parent_code: 'post.manage' },
      { code: 'post.publish', name: 'Xuất bản Bài viết', status: 'active', parent_code: 'post.manage' },
      
      // ========== POST CATEGORY MODULE ==========
      { code: 'postcategory.manage', name: 'Quản lý Danh mục', status: 'active', parent_code: null },
      { code: 'postcategory.create', name: 'Tạo Danh mục', status: 'active', parent_code: 'postcategory.manage' },
      { code: 'postcategory.read', name: 'Xem Danh mục', status: 'active', parent_code: 'postcategory.manage' },
      { code: 'postcategory.update', name: 'Sửa Danh mục', status: 'active', parent_code: 'postcategory.manage' },
      { code: 'postcategory.delete', name: 'Xóa Danh mục', status: 'active', parent_code: 'postcategory.manage' },
      
      // ========== POST TAG MODULE ==========
      { code: 'posttag.manage', name: 'Quản lý Thẻ', status: 'active', parent_code: null },
      { code: 'posttag.create', name: 'Tạo Thẻ', status: 'active', parent_code: 'posttag.manage' },
      { code: 'posttag.read', name: 'Xem Thẻ', status: 'active', parent_code: 'posttag.manage' },
      { code: 'posttag.update', name: 'Sửa Thẻ', status: 'active', parent_code: 'posttag.manage' },
      { code: 'posttag.delete', name: 'Xóa Thẻ', status: 'active', parent_code: 'posttag.manage' },
      
      // ========== USER MODULE ==========
      { code: 'user.manage', name: 'Quản lý Người dùng', status: 'active', parent_code: null },
      { code: 'user.create', name: 'Tạo Người dùng', status: 'active', parent_code: 'user.manage' },
      { code: 'user.read', name: 'Xem Người dùng', status: 'active', parent_code: 'user.manage' },
      { code: 'user.update', name: 'Sửa Người dùng', status: 'active', parent_code: 'user.manage' },
      { code: 'user.delete', name: 'Xóa Người dùng', status: 'active', parent_code: 'user.manage' },
      { code: 'user.activate', name: 'Kích hoạt Người dùng', status: 'active', parent_code: 'user.manage' },
      { code: 'user.deactivate', name: 'Vô hiệu hóa Người dùng', status: 'active', parent_code: 'user.manage' },
      
      // ========== ROLE MODULE ==========
      { code: 'role.manage', name: 'Quản lý Vai trò', status: 'active', parent_code: null },
      { code: 'role.create', name: 'Tạo Vai trò', status: 'active', parent_code: 'role.manage' },
      { code: 'role.read', name: 'Xem Vai trò', status: 'active', parent_code: 'role.manage' },
      { code: 'role.update', name: 'Sửa Vai trò', status: 'active', parent_code: 'role.manage' },
      { code: 'role.delete', name: 'Xóa Vai trò', status: 'active', parent_code: 'role.manage' },
      { code: 'role.assign', name: 'Gán Vai trò', status: 'active', parent_code: 'role.manage' },
      
      // ========== PERMISSION MODULE ==========
      { code: 'permission.manage', name: 'Quản lý Quyền', status: 'active', parent_code: null },
      { code: 'permission.read', name: 'Xem Quyền', status: 'active', parent_code: 'permission.manage' },
      { code: 'permission.assign', name: 'Gán Quyền', status: 'active', parent_code: 'permission.manage' },
      
      // ========== SYSTEM MODULE ==========
      { code: 'system.manage', name: 'Quản lý Hệ thống', status: 'active', parent_code: null },
      { code: 'system.settings', name: 'Cài đặt Hệ thống', status: 'active', parent_code: 'system.manage' },
      { code: 'system.logs', name: 'Xem Nhật ký', status: 'active', parent_code: 'system.manage' },
      { code: 'system.backup', name: 'Sao lưu Hệ thống', status: 'active', parent_code: 'system.manage' },
    ];

    const createdPermissions: Map<string, Permission> = new Map();

    // Create permissions in order (parents first)
    const sortedPermissions = this.sortPermissionsByParent(permissions);
    
    for (const permData of sortedPermissions) {
      let parentPermission: Permission | null = null;
      if (permData.parent_code) {
        parentPermission = createdPermissions.get(permData.parent_code) || null;
        if (!parentPermission) {
          this.logger.warn(`Parent permission not found for ${permData.code}, skipping parent relation`);
        }
      }

      const permission = permRepo.create({
        code: permData.code,
        name: permData.name,
        status: permData.status,
        parent: parentPermission,
      });
      const saved = await permRepo.save(permission);
      createdPermissions.set(saved.code, saved);
      this.logger.log(`Created permission: ${saved.code}${parentPermission ? ` (parent: ${parentPermission.code})` : ''}`);
    }

    this.logger.log(`Permissions seeding completed - Total: ${createdPermissions.size}`);
  }

  private sortPermissionsByParent(permissions: Array<{code: string, name: string, status: string, parent_code: string | null}>): Array<{code: string, name: string, status: string, parent_code: string | null}> {
    const result: Array<{code: string, name: string, status: string, parent_code: string | null}> = [];
    const processed = new Set<string>();

    // First pass: add all permissions without parents
    for (const perm of permissions) {
      if (!perm.parent_code) {
        result.push(perm);
        processed.add(perm.code);
      }
    }

    // Second pass: add children
    let changed = true;
    while (changed) {
      changed = false;
      for (const perm of permissions) {
        if (!processed.has(perm.code)) {
          if (!perm.parent_code || processed.has(perm.parent_code)) {
            result.push(perm);
            processed.add(perm.code);
            changed = true;
          }
        }
      }
    }

    return result;
  }

  async clear(): Promise<void> {
    this.logger.log('Clearing permissions...');
    const permRepo = this.dataSource.getRepository(Permission);
    await permRepo.clear();
    this.logger.log('Permissions cleared');
  }
}
