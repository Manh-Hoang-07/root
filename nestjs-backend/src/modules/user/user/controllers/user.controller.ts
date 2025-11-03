import { Controller, Get, Patch, Body } from '@nestjs/common';
import { UserService } from '../services/user.service';
import { Auth } from '../../../../common/utils/auth.util';
import { ResponseUtil } from '../../../../common/utils/response.util';
import { UpdateProfileDto } from '../dto/update-profile.dto';
import { ChangePasswordDto } from '../dto/change-password.dto';

@Controller('users')
export class UserController {
  constructor(private readonly userService: UserService) {}

  @Get('me')
  async me() {
    const userId = Auth.id(undefined) as number | null;
    if (!userId) return ResponseUtil.unauthorized('Không thể lấy thông tin user');
    const user = await this.userService.getByIdSafe(userId);
    if (!user) return ResponseUtil.unauthorized('Không thể lấy thông tin user');
    return ResponseUtil.success(user, 'Lấy thông tin user thành công');
  }

  @Patch('me')
  async updateMe(@Body() dto: UpdateProfileDto) {
    const userId = Auth.id(undefined) as number | null;
    if (!userId) return ResponseUtil.unauthorized('Không thể cập nhật thông tin user');
    return this.userService.updateProfile(userId, dto);
  }

  @Patch('me/password')
  async changeMyPassword(@Body() dto: ChangePasswordDto) {
    const userId = Auth.id(undefined) as number | null;
    if (!userId) return ResponseUtil.unauthorized('Không thể đổi mật khẩu');
    return this.userService.changePassword(userId, dto);
  }
}
