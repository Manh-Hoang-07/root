# Sử dụng @Permission Decorator

Decorator `@Permission()` được sử dụng để kiểm tra quyền của user một cách đơn giản và trực quan.

## Import

```typescript
import { Permission } from '../../common/decorators/rbac.decorators';
```

## Cách sử dụng

### 1. Kiểm tra một permission

```typescript
import { Controller, Post, Body } from '@nestjs/common';
import { Permission } from '../../common/decorators/rbac.decorators';

@Controller('posts')
export class PostController {
  @Permission('post.create')
  @Post()
  async createPost(@Body() dto: CreatePostDto) {
    // Chỉ user có permission 'post.create' mới có thể truy cập
    return this.postService.create(dto);
  }
}
```

### 2. Kiểm tra nhiều permissions (OR logic)

Khi truyền nhiều permissions, user chỉ cần có **MỘT trong số** các permissions đó:

```typescript
@Permission('post.update', 'post.delete')
@Put(':id')
async updateOrDelete(@Param('id') id: number, @Body() dto: any) {
  // User có permission 'post.update' HOẶC 'post.delete' đều có thể truy cập
  return this.postService.update(id, dto);
}
```

### 3. Kết hợp với các decorator khác

```typescript
import { Controller, Get, Post, Put, Delete, Body, Param } from '@nestjs/common';
import { Permission } from '../../common/decorators/rbac.decorators';
import { Public } from '../../common/decorators/public.decorator';

@Controller('posts')
export class PostController {
  // Route public - không cần authentication và permission
  @Public()
  @Get('public')
  async getPublicPosts() {
    return this.postService.getPublicPosts();
  }

  // Route cần authentication nhưng không cần permission
  @Get()
  async getAll() {
    return this.postService.findAll();
  }

  // Route cần permission cụ thể
  @Permission('post.create')
  @Post()
  async create(@Body() dto: CreatePostDto) {
    return this.postService.create(dto);
  }

  @Permission('post.update')
  @Put(':id')
  async update(@Param('id') id: number, @Body() dto: UpdatePostDto) {
    return this.postService.update(id, dto);
  }

  @Permission('post.delete')
  @Delete(':id')
  async delete(@Param('id') id: number) {
    return this.postService.delete(id);
  }
}
```

### 4. Sử dụng ở Controller level

Bạn có thể đặt `@Permission()` ở controller level để áp dụng cho tất cả các routes:

```typescript
@Permission('admin') // Tất cả routes trong controller này đều cần permission 'admin'
@Controller('admin/users')
export class AdminUserController {
  @Get()
  async list() {
    // Cần permission 'admin'
  }

  @Get(':id')
  async getOne(@Param('id') id: number) {
    // Cần permission 'admin'
  }
}
```

### 5. Override ở route level

Route-level decorator sẽ override controller-level decorator:

```typescript
@Permission('admin')
@Controller('admin/users')
export class AdminUserController {
  @Get()
  async list() {
    // Cần permission 'admin'
  }

  @Permission('user.read') // Override - route này chỉ cần 'user.read'
  @Get('public')
  async getPublic() {
    // Chỉ cần permission 'user.read'
  }
}
```

## Cách hoạt động

1. **Guard tự động chạy**: `RolesPermissionsGuard` đã được register global, tự động check cho mọi route
2. **Kiểm tra authentication**: User phải đã đăng nhập (JWT token hợp lệ)
3. **Kiểm tra permission**: 
   - Lấy `userId` từ `req.user`
   - Gọi `RbacService.userHasPermissions(userId, requiredPermissions)`
   - Chỉ kiểm tra permissions có `status = 'active'` từ roles của user
   - Nếu không có quyền → throw `ForbiddenException`

## Error Responses

### Unauthorized (401)
```json
{
  "statusCode": 401,
  "message": "Authentication required"
}
```

### Forbidden (403)
```json
{
  "statusCode": 403,
  "message": "Access denied. Required permissions: post.create"
}
```

## Lưu ý quan trọng

1. **Permission phải tồn tại trong DB** với `status = 'active'`
2. **User phải có role** và role đó phải có permission
3. **Nếu role hoặc permission bị inactive** → user mất quyền ngay lập tức
4. **KHÔNG có direct permissions** - tất cả đều phải qua roles

## Ví dụ thực tế

```typescript
import { Controller, Get, Post, Put, Delete, Body, Param, ParseIntPipe } from '@nestjs/common';
import { Permission } from '../../common/decorators/rbac.decorators';

@Controller('admin/posts')
export class PostController {
  // List posts - cần quyền đọc
  @Permission('post.read')
  @Get()
  async getList() {
    return this.postService.findAll();
  }

  // Get one post - cần quyền đọc
  @Permission('post.read')
  @Get(':id')
  async getOne(@Param('id', ParseIntPipe) id: number) {
    return this.postService.findOne(id);
  }

  // Create post - cần quyền tạo
  @Permission('post.create')
  @Post()
  async create(@Body() dto: CreatePostDto) {
    return this.postService.create(dto);
  }

  // Update post - cần quyền cập nhật
  @Permission('post.update')
  @Put(':id')
  async update(
    @Param('id', ParseIntPipe) id: number,
    @Body() dto: UpdatePostDto
  ) {
    return this.postService.update(id, dto);
  }

  // Delete post - cần quyền xóa
  @Permission('post.delete')
  @Delete(':id')
  async delete(@Param('id', ParseIntPipe) id: number) {
    return this.postService.delete(id);
  }

  // Publish post - có thể cần quyền update hoặc publish
  @Permission('post.update', 'post.publish')
  @Post(':id/publish')
  async publish(@Param('id', ParseIntPipe) id: number) {
    return this.postService.publish(id);
  }
}
```

