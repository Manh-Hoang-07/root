import { Injectable } from '@nestjs/common';
import { DataSource, Repository, DeepPartial } from 'typeorm';
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

  constructor(private readonly dataSource: DataSource) {
    super(dataSource.getRepository(User));
  }

  async profile(id: number): Promise<ApiResponse<any>> {
    const user = await this.repository.findOne({ where: { id } as any });
    if (!user) return ResponseUtil.notFound('Không tìm thấy người dùng');
    const profile = await this.profileRepo.findOne({ where: { userId: id } });
    return ResponseUtil.success({ user, profile }, 'Lấy thông tin profile thành công');
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

  async assignRoles(id: number, roleIds: number[]): Promise<ApiResponse<User | null>> {
    return ResponseUtil.error('Phân quyền chưa được cấu hình (thiếu Role entity)', 'ASSIGN_ROLES_UNSUPPORTED');
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
      const profile = this.profileRepo.create({ ...(profilePayload as any), userId: entity.id });
      await this.profileRepo.save(profile);
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
      let profile = await this.profileRepo.findOne({ where: { userId: entity.id } });
      if (profile) {
        Object.assign(profile, profilePayload as any);
        await this.profileRepo.save(profile);
      } else {
        const createdProfile = this.profileRepo.create({ ...(profilePayload as any), userId: entity.id });
        await this.profileRepo.save(createdProfile);
      }
    }
  }
}


