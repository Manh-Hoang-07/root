import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import * as bcrypt from 'bcryptjs';
import { User } from '../../../../shared/entities/user.entity';
import { safeUser } from '../../../auth/utils/user.util';
import { UpdateProfileDto } from '../dto/update-profile.dto';
import { ChangePasswordDto } from '../dto/change-password.dto';
import { ResponseUtil } from '../../../../common/utils/response.util';

@Injectable()
export class UserService {
  constructor(
    @InjectRepository(User) private readonly userRepository: Repository<User>,
  ) {}

  async getByIdSafe(userId: number) {
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user) return null;
    return safeUser(user);
  }

  async updateProfile(userId: number, dto: UpdateProfileDto) {
    const user = await this.userRepository.findOne({ where: { id: userId } });
    if (!user) return ResponseUtil.unauthorized('Không thể cập nhật thông tin user');

    // Unique check phone nếu cung cấp
    if (dto.phone) {
      const exists = await this.userRepository.findOne({ where: { phone: dto.phone } });
      if (exists && exists.id !== userId) {
        return ResponseUtil.badRequest('Số điện thoại đã được sử dụng.');
      }
    }

    const patch: Partial<User> = {};
    if (dto.phone !== undefined) patch.phone = dto.phone;
    if (dto.name !== undefined) {
      // nếu bạn lưu name trong profile thì ở đây cần cập nhật profile thay vì user
      // để đơn giản, gán username theo name nếu muốn
      patch.username = dto.name;
    }
    if (Object.keys(patch).length > 0) {
      await this.userRepository.update({ id: userId }, patch);
    }

    const updated = await this.userRepository.findOne({ where: { id: userId } });
    return ResponseUtil.success(updated ? safeUser(updated) : null, 'Cập nhật thông tin thành công');
  }

  async changePassword(userId: number, dto: ChangePasswordDto) {
    const user = await this.userRepository.findOne({ where: { id: userId }, select: { id: true, password: true } });
    if (!user || !user.password) return ResponseUtil.unauthorized('Không thể đổi mật khẩu');

    const ok = await bcrypt.compare(dto.oldPassword, user.password);
    if (!ok) return ResponseUtil.badRequest('Mật khẩu hiện tại không đúng');

    const hashed = await bcrypt.hash(dto.newPassword, 10);
    await this.userRepository.update({ id: userId }, { password: hashed });

    return ResponseUtil.success(null, 'Đổi mật khẩu thành công');
  }
}
