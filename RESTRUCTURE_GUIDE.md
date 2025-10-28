# Hướng dẫn cấu trúc lại Admin và User Modules

## Tổng quan
Bạn đã có cấu trúc module hoàn chỉnh cho `public` module. Bây giờ cần áp dụng tương tự cho `admin` và `user` modules.

## Cấu trúc cần tạo

### Admin Module
Mỗi feature cần có cấu trúc như sau:

```
admin/
├── admin.module.ts
├── posts/
│   ├── posts.controller.ts
│   ├── posts.service.ts
│   ├── posts.module.ts
│   └── dtos/
│       ├── create-post.dto.ts
│       ├── update-post.dto.ts
│       └── filter-post.dto.ts
├── products/
│   ├── products.controller.ts
│   ├── products.service.ts
│   ├── products.module.ts
│   └── dtos/
│       ├── create-product.dto.ts
│       ├── update-product.dto.ts
│       └── filter-product.dto.ts
├── users/
│   ├── users.controller.ts
│   ├── users.service.ts
│   ├── users.module.ts
│   └── dtos/
│       ├── create-user.dto.ts
│       ├── update-user.dto.ts
│       └── filter-user.dto.ts
... (và các features khác)
```

### User Module
```
user/
├── user.module.ts
├── profile/
│   ├── profile.controller.ts
│   ├── profile.service.ts
│   ├── profile.module.ts
│   └── dtos/
│       └── update-profile.dto.ts
├── cart/
│   ├── cart.controller.ts
│   ├── cart.service.ts
│   ├── cart.module.ts
│   └── dtos/
│       └── add-cart.dto.ts
├── order/
│   ├── order.controller.ts
│   ├── order.service.ts
│   ├── order.module.ts
│   └── dtos/
│       └── create-order.dto.ts
```

## Các bước thực hiện

### 1. Tạo structure cho mỗi feature
- Copy controller từ `controllers/feature.controller.ts` vào `feature/feature.controller.ts`
- Copy service từ `services/feature.service.ts` vào `feature/feature.service.ts`
- Tạo `feature.module.ts`
- Tạo thư mục `dtos/` và các DTO files

### 2. Update imports
Tất cả imports entities/enums cần update từ:
```typescript
import { Entity } from '../../../entities/entity.entity';
```
Thành:
```typescript
import { Entity } from '../../../shared/entities/entity.entity';
```

### 3. Update admin.module.ts
Thay vì import controllers và providers riêng lẻ, import các modules:
```typescript
import { PostsModule } from './posts/posts.module';
import { ProductsModule } from './products/products.module';
// ... các modules khác

@Module({
  imports: [
    PostsModule,
    ProductsModule,
    // ...
  ],
})
export class AdminModule {}
```

### 4. Xóa files cũ
Sau khi đã tạo các modules con, xóa:
- `controllers/` folder
- `services/` folder

## Checklist

### Admin Module cần tạo:
- [x] posts (đã tạo)
- [x] products (đã tạo)
- [ ] users
- [ ] orders
- [ ] contacts
- [ ] menus
- [ ] roles
- [ ] permissions
- [ ] system-configs
- [ ] post-categories
- [ ] post-tags
- [ ] product-categories

### User Module cần tạo:
- [ ] profile
- [ ] cart
- [ ] order

## Template mẫu cho một module

### feature.controller.ts
```typescript
import { Controller, Get, Post, Put, Delete, Param, Body, Query, UseGuards } from '@nestjs/common';
import { FeatureService } from './feature.service';
import { JwtAuthGuard } from '../../../common/guards/jwt-auth.guard';
import { RolesGuard } from '../../../common/guards/roles.guard';
import { Roles } from '../../../common/decorators/roles.decorator';

@Controller('admin/features')
@UseGuards(JwtAuthGuard, RolesGuard)
@Roles('admin')
export class FeatureController {
  constructor(private readonly featureService: FeatureService) {}

  @Get()
  async findAll(@Query() query: any) {
    return this.featureService.findAll(query);
  }

  @Get(':id')
  async findOne(@Param('id') id: string) {
    return this.featureService.findOne(id);
  }

  @Post()
  async create(@Body() createDto: any) {
    return this.featureService.create(createDto);
  }

  @Put(':id')
  async update(@Param('id') id: string, @Body() updateDto: any) {
    return this.featureService.update(id, updateDto);
  }

  @Delete(':id')
  async remove(@Param('id') id: string) {
    return this.featureService.remove(id);
  }
}
```

### feature.module.ts
```typescript
import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { FeatureController } from './feature.controller';
import { FeatureService } from './feature.service';
import { FeatureEntity } from '../../../shared/entities/feature.entity';

@Module({
  imports: [TypeOrmModule.forFeature([FeatureEntity])],
  controllers: [FeatureController],
  providers: [FeatureService],
  exports: [FeatureService],
})
export class FeatureModule {}
```

## Lưu ý quan trọng
1. Luôn update imports entities/enums về `shared/`
2. Thêm guards cho admin routes
3. Tạo DTOs phù hợp cho mỗi operation
4. Export services nếu cần sử dụng ở nơi khác
5. Import đúng entities vào TypeOrmModule.forFeature()

