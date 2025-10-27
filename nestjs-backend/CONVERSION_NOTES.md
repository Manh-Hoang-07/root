# Laravel to NestJS Conversion Notes

## Overview

This is a NestJS backend converted from a Laravel application. The conversion maintains the same database structure and API endpoints.

## What Has Been Completed

### ✅ Project Setup
- Created NestJS project structure
- Configured TypeScript, ESLint, Prettier
- Set up TypeORM with SQLite
- Configured CORS, validation, exception filters
- Created `.gitignore` and documentation

### ✅ Enums Conversion
All PHP enums have been converted to TypeScript:
- UserStatus
- ProductStatus
- OrderStatus
- PaymentStatus
- ShippingStatus
- PostStatus
- ContactStatus
- RoleStatus
- BasicStatus
- Gender
- AttributeType
- ConfigAction
- ConfigGroup
- ConfigType

### ✅ Core Entities Created
Base entities with TypeORM decorators:
- User & Profile
- Product & ProductCategory & ProductVariant
- Order & OrderItem
- Cart & CartHeader
- Menu
- Contact
- SystemConfig
- Role
- Permission

### ✅ Common Files
- Exception filter (HTTP exception handling)
- Transform interceptor (response formatting)
- Main application configuration
- App module structure

## What Needs to Be Done

### 🔲 Complete Entity Conversions
Still need to create entities for:
- Post
- PostCategory
- PostTag
- ProductAttribute
- ProductAttributeValue
- ProductVariantAttribute
- NotificationTemplate
- SocialAccount
- ConfigAuditLog
- ConfigPermission

### 🔲 Module Implementations
Each module needs:
1. **Module file** - Define module dependencies
2. **Service** - Business logic (convert from Laravel services)
3. **Controller** - HTTP handlers (convert from Laravel controllers)
4. **DTOs** - Data transfer objects (validation)
5. **Repository** - Data access layer (optional, if needed)

### 🔲 Authentication & Authorization
- JWT strategy implementation
- Guards (AuthGuard, RolesGuard, PermissionsGuard)
- Passport setup
- Login/Register endpoints
- Token refresh

### 🔲 Key Modules to Complete

#### Auth Module
```typescript
- src/modules/auth/auth.module.ts
- src/modules/auth/auth.service.ts
- src/modules/auth/auth.controller.ts
- src/modules/auth/dto/login.dto.ts
- src/modules/auth/dto/register.dto.ts
- src/modules/auth/strategies/jwt.strategy.ts
```

#### Product Module
```typescript
- src/modules/product/product.module.ts
- src/modules/product/product.service.ts
- src/modules/product/product.controller.ts
- src/modules/product/dto/*.dto.ts
```

#### Order Module
```typescript
- src/modules/order/order.module.ts
- src/modules/order/order.service.ts
- src/modules/order/order.controller.ts
- src/modules/order/dto/*.dto.ts
```

#### Cart Module
```typescript
- src/modules/cart/cart.module.ts
- src/modules/cart/cart.service.ts
- src/modules/cart/cart.controller.ts
```

#### And all other modules...

### 🔲 Middleware & Guards
- Rate limiting
- Authentication guard
- Authorization guards (roles, permissions)
- File upload handling

### 🔲 File Upload Module
- Multer configuration
- File validation
- Storage handling

## Migration Guide

### Database
The database schema is the same. TypeORM will use the same SQLite database or you can create migrations based on the existing Laravel migrations.

### API Endpoints
The API structure follows the same routes:
- Public API: `/api/*` (products, categories, etc.)
- User API: `/api/*` with auth (user orders, profile)
- Admin API: `/api/admin/*` with auth + admin role

### Key Differences from Laravel

1. **Dependency Injection**: NestJS uses decorator-based DI
2. **Validation**: Uses class-validator instead of Form Requests
3. **Database**: TypeORM instead of Eloquent
4. **Authentication**: Passport-JWT instead of Laravel Sanctum
5. **Response Format**: Interceptor-based instead of Resource classes

### Example: Converting a Controller

**Laravel:**
```php
class ProductController extends Controller
{
    public function index(Request $request)
    {
        return $this->service->getAll($request);
    }
}
```

**NestJS:**
```typescript
@Controller('products')
export class ProductController {
    constructor(private readonly productService: ProductService) {}
    
    @Get()
    async findAll(@Query() query: QueryProductDto) {
        return this.productService.findAll(query);
    }
}
```

## Next Steps

1. Complete remaining entity definitions
2. Implement services for each module
3. Create controllers for all endpoints
4. Add DTOs with validation
5. Implement authentication flow
6. Add file upload functionality
7. Write unit and integration tests
8. Set up CI/CD pipeline

## Resources

- [NestJS Documentation](https://docs.nestjs.com/)
- [TypeORM Documentation](https://typeorm.io/)
- [class-validator](https://github.com/typestack/class-validator)
- [Passport](http://www.passportjs.org/)

