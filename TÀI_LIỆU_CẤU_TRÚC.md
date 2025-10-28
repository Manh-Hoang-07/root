# CẤU TRÚC DỰ ÁN NESTJS HOÀN CHỈNH

## ✅ ĐÃ HOÀN THÀNH

### 1. Core Infrastructure (`src/core/`)
- ✅ Config: app, database, jwt, mail
- ✅ Database module  
- ✅ Logger service
- ✅ Utils: date, string, response

### 2. Common Base Classes (`src/common/`)
- ✅ Base: entity, service, repository
- ✅ Decorators: roles, user, public
- ✅ Filters: http-exception
- ✅ Guards: jwt-auth, roles
- ✅ Interceptors: transform, logging, timeout

### 3. Shared Resources (`src/shared/`)
- ✅ Entities: All 20+ entities
- ✅ Enums: All enums
- ✅ DTOs: pagination, response

### 4. Typings (`src/typings/`)
- ✅ Global type definitions
- ✅ API response interfaces

### 5. Modules - Public Zone (`src/modules/public/`) ✅
Cấu trúc HOÀN CHỈNH cho mỗi feature:
```
public/
├── public.module.ts
├── post/
│   ├── post.controller.ts
│   ├── post.service.ts
│   ├── post.module.ts
│   └── dtos/
│       └── get-post.dto.ts
├── product/
├── menu/
├── contact/
├── cart/
├── product-category/
├── post-category/
├── post-tag/
└── system-config/
```

## ⚠️ CẦN HOÀN THIỆN

### 6. Modules - Admin Zone (`src/modules/admin/`)

**ĐÃ TẠO:**
- ✅ posts/ (controller, service, module, dtos)
- ✅ products/ (controller, service, module)
- ✅ users/ (controller, service, module)

**CẦN TẠO:**
- [ ] orders/
- [ ] contacts/
- [ ] menus/
- [ ] roles/
- [ ] permissions/
- [ ] system-configs/
- [ ] post-categories/
- [ ] post-tags/
- [ ] product-categories/

**HIỆN TẠI:**
```
admin/
├── admin.module.ts
├── controllers/  ← XÓA SAU KHI HOÀN THIỆN
├── services/     ← XÓA SAU KHI HOÀN THIỆN
├── posts/        ← ĐÃ TẠO
├── products/     ← ĐÃ TẠO
└── users/        ← ĐÃ TẠO
```

### 7. Modules - User Zone (`src/modules/user/`)

**CẦN TẠO:**
- [ ] profile/ (controller, service, module, dtos)
- [ ] cart/
- [ ] order/

**HIỆN TẠI:**
```
user/
├── user.module.ts
├── controllers/  ← CHUYỂN THÀNH SUB-MODULES
├── services/     ← CHUYỂN THÀNH SUB-MODULES
└── profile/      ← ĐANG TẠO
```

## HƯỚNG DẪN HOÀN THIỆN

### Bước 1: Tạo module con cho admin

**Ví dụ tạo module "orders":**

```bash
# Tạo thư mục
mkdir -p nestjs-backend/src/modules/admin/orders/dtos

# Copy files
cp src/modules/admin/controllers/orders.controller.ts src/modules/admin/orders/orders.controller.ts
cp src/modules/admin/services/orders.service.ts src/modules/admin/orders/orders.service.ts
```

**Tạo orders.module.ts:**
```typescript
import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { OrdersController } from './orders.controller';
import { OrdersService } from './orders.service';
import { Order } from '../../../shared/entities/order.entity';
import { OrderItem } from '../../../shared/entities/order-item.entity';

@Module({
  imports: [TypeOrmModule.for выбираюFeature([Order, OrderItem])],
  controllers: [OrdersController],
  providers: [OrdersService],
  exports: [OrdersService],
})
export class OrdersModule {}
```

**Tạo DTOs trong orders/dtos/**
- create-order.dto.ts
- update-order.dto.ts
- filter-order.dto.ts

**Update imports trong orders.service.ts:**
```typescript
// TỪ:
import { Order } from '../../../entities/order.entity';

// THÀNH:
import { Order } from '../../../shared/entities/order.entity';
```

### Bước 2: Update admin.module.ts

```typescript
import { Module } from '@nestjs/common';
import { PostsModule } from './posts/posts.module';
import { ProductsModule } from './products/products.module';
import { UsersModule } from './users/users.module';
import { OrdersModule } from './orders/orders.module';
// ... import tất cả modules con

@Module({
  imports: [
    PostsModule,
    ProductsModule,
    UsersModule,
    OrdersModule,
    // ... các modules khác
  ],
})
export class AdminModule {}
```

### Bước 3: Repeat cho tất cả features

Áp dụng tương tự cho:
- orders
- contacts
- menus
- roles
- permissions
- system-configs
- post-categories
- post-tags
- product-categories

### Bước 4: Xóa thư mục cũ

Sau khi đã tạo tất cả modules con:
```bash
rm -rf src/modules/admin/controllers
rm -rf src/modules/admin/services
```

### Bước 5: Làm tương tự cho User module

Tạo:
- profile/
- cart/
- order/

## TEMPLATE DTOs MẪU

### create-feature.dto.ts
```typescript
import { IsString, IsOptional, IsNumber } from 'class-validator';

export class CreateFeatureDto {
  @IsString()
  name: string;

  @IsString()
  @IsOptional()
  description?: string;

  @IsNumber()
  @IsOptional()
  price?: number;
}
```

### update-feature.dto.ts
```typescript
import { IsString, IsOptional, IsNumber } from 'class-validator';

export class UpdateFeatureDto {
  @IsString()
  @IsOptional()
  name?: string;

  @IsString()
  @IsOptional()
  description?: string;

  @IsNumber()
  @IsOptional()
  price?: number;
}
```

### filter-feature.dto.ts
```typescript
import { IsOptional, IsString, IsInt, Min } from 'class-validator';
import { Type } from particles/transform';

export class FilterFeatureDto {
  @IsOptional()
  @Type(() => Number)
  @IsInt()
  @Min(1)
  page?: number = 1;

  @IsOptional()
  @Type(() => Number)
  @IsInt()
  @Min(1)
  per_page?: number = 20;

  @IsOptional()
  @IsString()
  search?: string;
}
```

## CHECKLIST HOÀN THIỆN

### Admin Module
- [x] posts
- [x] products
- [x] users
- [ ] orders
- [ ] contacts
- [ ] menus
- [ ] roles
- [ ] permissions
- [ ] system-configs
- [ ] post-categories
- [ ] post-tags
- [ ] product-categories
- [ ] Update admin.module.ts
- [ ] Xóa controllers/ và services/

### User Module
- [ ] profile
- [ ] cart
- [ ] order
- [ ] Update user.module.ts
- [ ] Xóa controllers/ và services/

## Lợi ích của cấu trúc mới

1. **Tổ chức rõ ràng**: Mỗi feature độc lập
2. **Dễ bảo trì**: Tìm và sửa code nhanh
3. **Tái sử dụng**: Module có thể import ở nơi khác
4. **Mở rộng dễ**: Thêm feature mới dễ dàng
5. **Tuân thủ best practices**: Theo chuẩn NestJS

