import { Body, Controller, Get, Param, Patch, Post } from '@nestjs/common';
import { UserService } from '../services/user.service';
import { CreateUserDto } from '../dtos/create-user.dto';
import { UpdateUserDto } from '../dtos/update-user.dto';
import { ChangePasswordDto } from '../dtos/change-password.dto';

@Controller('admin/users')
export class UserController {
  constructor(private readonly service: UserService) {}

  @Post()
  create(@Body() dto: CreateUserDto) {
    return this.service.create(dto);
  }

  @Patch(':id')
  update(@Param('id') id: string, @Body() dto: UpdateUserDto) {
    return this.service.update(Number(id), dto);
  }

  @Get(':id/profile')
  profile(@Param('id') id: string) {
    return this.service.profile(Number(id));
  }


  @Patch(':id/password')
  changePassword(@Param('id') id: string, @Body() dto: ChangePasswordDto) {
    return this.service.changePassword(Number(id), dto);
  }

  @Post(':id/roles')
  assignRoles(@Param('id') id: string, @Body('role_ids') roleIds: number[]) {
    return this.service.assignRoles(Number(id), roleIds);
  }
}


