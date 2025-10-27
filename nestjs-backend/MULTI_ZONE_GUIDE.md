# Hướng Dẫn Multi-Zone Architecture

## Cấu Trúc Zones

Ứng dụng được chia thành 3 zones:

### 1. **Public Zone** (`/public/...`)
- **Không cần authentication**
- Ai cũng có thể truy cập
- **Examples:**
  - `POST /public/auth/login` - Login
  - `POST /public/auth/register` - Register
  - `GET /public/posts` - Xem posts
  - `GET /public/products` - Xem sản phẩm
  - `GET /public/enums` - Lấy enum values

### 2. **User Zone** (`/user/...`)
- **Cần authentication** (JWT token)
- Chỉ user đã đăng nhập mới truy cập được
- **Examples:**
  - `GET /user/profile` - Xem profile
  - `GET /user/orders` - Xem đơn hàng của mình
  - `PUT /user/profile` - Cập nhật profile
  - `POST /user/logout` - Logout

### 3. **Admin Zone** (`/admin/...`)
- **Cần authentication** + Admin role
- Chỉ admin mới truy cập được
- **Examples:**
  - `GET /admin/users` - Quản lý users
  - `POST /admin/products` - Tạo product
  - `PUT /admin/products/:id` - Cập nhật product
  - `DELETE /admin/products/:id` - Xóa product

## Cách Implement

### 1. Public Controllers

```typescript
@Controller('public/posts')
export class PostController {
  // Không cần guard
}
```

### 2. User Controllers

```typescript
@Controller('user/posts')
@UseGuards(JwtAuthGuard)  // Require authentication
export class UserPostController {
  // Cần token
}
```

### 3. Admin Controllers

```typescript
@Controller('admin/posts')
@UseGuards(JwtAuthGuard, RolesGuard)  // Require auth + role
@Roles('admin')  // Require admin role
export class AdminPostController {
  // Cần token + admin role
}
```

## Guards

### JwtAuthGuard
```typescript
@Injectable()
export class JwtAuthGuard extends AuthGuard('jwt') {}
```

### RolesGuard
```typescript
@Injectable()
export class RolesGuard implements CanActivate {
  canActivate(context: ExecutionContext): boolean {
    // Check user role
  }
}
```

## Ví Dụ API Calls

```bash
# Public API (không cần token)
POST /public/auth/login
GET /public/posts?page=1

# User API (cần token)
GET /user/profile
Authorization: Bearer <token>

# Admin API (cần token + admin role)
GET /admin/users
Authorization: Bearer <token>
```

## Migration từ Laravel

Cấu trúc routes trong Laravel:

```
- routes/api/public.php  → /public/*
- routes/api/user.php    → /user/*
- routes/api/admin.php   → /admin/*
```

Tương ứng trong NestJS:

```
- src/modules/post/controllers/public-post.controller.ts  → /public/posts
- src/modules/post/controllers/user-post.controller.ts    → /user/posts
- src/modules/post/controllers/admin-post.controller.ts   → /admin/posts
```

## Next Steps

1. Tạo separate controllers cho từng zone
2. Implement guards (JwtAuthGuard, RolesGuard)
3. Tạo decorators cho roles (@Roles, @Public)
4. Setup middleware cho rate limiting, logging

