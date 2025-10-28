import { Controller, Get, Post, Body, UseInterceptors } from '@nestjs/common';
import { CrudService } from '../services/crud.service';
import { ResponseBuilder, ApiResponse, PaginatedApiResponse } from '../interfaces/api-response.interface';
import { ResponseInterceptor } from '../interceptors/response.interceptor';

// Example Entity
class User {
  id: string;
  name: string;
  email: string;
}

@Controller('users')
@UseInterceptors(ResponseInterceptor) // Tự động format response
export class UserController {
  constructor(private readonly userService: CrudService<User>) {}

  // Cách 1: Dùng interceptor với message tùy chỉnh
  @Get()
  async findAll() {
    const result = await this.userService.findAll();
    return {
      data: result.data,
      meta: result.meta,
      message: 'Lấy danh sách user thành công',
      code: 'SUCCESS'
    };
  }

  // Cách 2: API hành động (không có meta)
  @Post()
  async create(@Body() createUserDto: any) {
    const user = await this.userService.create(createUserDto);
    return {
      data: user,
      message: 'Tạo user thành công',
      code: 'CREATED',
      httpStatus: 201
    };
  }

  // Cách 3: API cập nhật
  @Put(':id')
  async update(@Param('id') id: string, @Body() updateUserDto: any) {
    const user = await this.userService.update(id, updateUserDto);
    return {
      data: user,
      message: 'Cập nhật user thành công',
      code: 'UPDATED'
    };
  }

  // Cách 4: API xóa
  @Delete(':id')
  async delete(@Param('id') id: string) {
    await this.userService.softDelete(id);
    return {
      data: null,
      message: 'Xóa user thành công',
      code: 'DELETED'
    };
  }

  // Cách 5: Format thủ công (không dùng interceptor)
  @Get('manual')
  async findAllManual(): Promise<PaginatedApiResponse<User>> {
    const result = await this.userService.findAll();
    
    return ResponseBuilder.paginated(
      result.data,
      result.meta,
      'Lấy danh sách user thành công',
      200,
      'SUCCESS'
    );
  }

  // Cách 6: Error response
  @Get('error')
  async getError(): Promise<ApiResponse<null>> {
    return ResponseBuilder.error(
      'Không đủ quyền truy cập',
      403,
      'FORBIDDEN'
    );
  }
}
