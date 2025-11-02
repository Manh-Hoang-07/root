import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, DeepPartial } from 'typeorm';
import * as bcrypt from 'bcryptjs';
import { User } from '../../../../shared/entities/user.entity';
import { Profile } from '../../../../shared/entities/profile.entity';
import { ChangePasswordDto } from '../dtos/change-password.dto';
import { ResponseUtil, ApiResponse } from '../../../../common/utils/response.util';
import { CrudService } from '../../../../common/base/services/crud.service';

@Injectable()
export class UserService extends CrudService<User> {
  private get profileRepo(): Repository<Profile> {
    return this.repository.manager.getRepository(Profile);
  }

  constructor(
    @InjectRepository(User) repository: Repository<User>,
  ) {
    super(repository);
  }

  /**
   * Override getOne để đảm bảo load relations trong admin
   */
  async getOne(
    where: any,
    options?: any,
  ) {
    // Đảm bảo load relations trong admin
    const adminOptions = {
      ...options,
      relations: ['roles', 'direct_permissions'],
    };
    const user = await super.getOne(where, adminOptions);
    
    // Load profile riêng vì không có relation OneToOne trong entity
    if (user && where.id) {
      const profile = await this.profileRepo.findOne({ where: { userId: where.id } });
      (user as any).profile = profile || null;
    }
    
    return user;
  }

  async changePassword(id: number, dto: ChangePasswordDto): Promise<ApiResponse<null>> {
    try {
      const user = await this.repository.findOne({ where: { id } as any });
      if (!user) return ResponseUtil.notFound('Không tìm thấy người dùng');
      user.password = await bcrypt.hash(dto.newPassword, 10);
      await this.repository.save(user as any);
      return ResponseUtil.success(null, 'Đổi mật khẩu thành công');
    } catch (error) {
      return ResponseUtil.error('Không thể đổi mật khẩu', 'CHANGE_PASSWORD_FAILED');
    }
  }

  protected override async beforeCreate(_entity: User, createDto: DeepPartial<User>): Promise<boolean> {
    if ((createDto as any).password) {
      (createDto as any).password = await bcrypt.hash((createDto as any).password, 10);
    }
    if ('role_ids' in (createDto as any)) delete (createDto as any).role_ids;
    if ('profile' in (createDto as any)) delete (createDto as any).profile;
    return true;
  }

  protected override async afterCreate(entity: User, createDto: DeepPartial<User>): Promise<void> {
    const profilePayload = (createDto as any).profile ?? null;
    if (profilePayload) {
      // Kiểm tra xem profile đã tồn tại chưa (1 user chỉ có 1 profile)
      const existingProfile = await this.profileRepo.findOne({ where: { userId: entity.id } });
      if (!existingProfile) {
        const profile = this.profileRepo.create({ ...(profilePayload as any), userId: entity.id });
        await this.profileRepo.save(profile);
      }
    }
  }

  protected override async beforeUpdate(_entity: User, updateDto: DeepPartial<User>): Promise<boolean> {
    if ((updateDto as any).password) {
      (updateDto as any).password = await bcrypt.hash((updateDto as any).password, 10);
    } else if ('password' in (updateDto as any)) {
      delete (updateDto as any).password;
    }
    if ('role_ids' in (updateDto as any)) delete (updateDto as any).role_ids;
    if ('profile' in (updateDto as any)) delete (updateDto as any).profile;
    return true;
  }

  protected override async afterUpdate(entity: User, updateDto: DeepPartial<User>): Promise<void> {
    const profilePayload = (updateDto as any).profile ?? null;
    if (profilePayload && Object.keys(profilePayload).length) {
      // 1 user chỉ có 1 profile, tìm profile hiện tại hoặc tạo mới
      let profile = await this.profileRepo.findOne({ where: { userId: entity.id } });
      if (profile) {
        // Cập nhật profile hiện tại
        Object.assign(profile, profilePayload as any);
        await this.profileRepo.save(profile);
      } else {
        // Tạo profile mới nếu chưa có
        const createdProfile = this.profileRepo.create({ ...(profilePayload as any), userId: entity.id });
        await this.profileRepo.save(createdProfile);
      }
    }
  }

  protected override async afterDelete(entity: User): Promise<void> {
    // Xóa profile sau khi xóa user
    try {
      const profile = await this.profileRepo.findOne({ where: { userId: entity.id } });
      if (profile) {
        await this.profileRepo.remove(profile);
      }
    } catch (error) {
      // Log error nhưng không throw vì user đã được xóa
      console.error(`Failed to delete profile for user ${entity.id}:`, error);
    }
  }
}


