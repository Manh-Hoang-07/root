import { Controller } from '@nestjs/common';
import { BaseController } from '../controllers/base.controller';
import { CrudService } from '../services/crud.service';

// Example Entity
class User {
  id: string;
  name: string;
  email: string;
  createdAt: Date;
}

// Example Service
class UserService extends CrudService<User> {
  constructor() {
    super(null as any); // Inject repository here
  }

  protected getDefaultSearchFields(): (keyof User)[] {
    return ['name', 'email'];
  }

  protected getDefaultRelations(): string[] {
    return ['profile', 'roles'];
  }
}

/**
 * User Controller - Chỉ cần kế thừa và khai báo route
 */
@Controller('users')
export class UserController extends BaseController<User> {
  constructor(private readonly userService: UserService) {
    super(userService);
  }

  // Override message nếu cần
  protected getListMessage(): string {
    return 'Lấy danh sách user thành công';
  }

  protected getCreateMessage(): string {
    return 'Tạo user mới thành công';
  }

  protected getUpdateMessage(): string {
    return 'Cập nhật thông tin user thành công';
  }

  protected getDeleteMessage(): string {
    return 'Xóa user thành công';
  }
}

/**
 * Product Controller - Ví dụ khác
 */
@Controller('products')
export class ProductController extends BaseController<any> {
  constructor(private readonly productService: CrudService<any>) {
    super(productService);
  }

  protected getListMessage(): string {
    return 'Lấy danh sách sản phẩm thành công';
  }

  protected getCreateMessage(): string {
    return 'Thêm sản phẩm mới thành công';
  }
}
