# NestJS Backend Structure

## Overview
This document describes the new clean architecture structure for the NestJS backend application.

## Directory Structure

```
src/
├── app.module.ts                  # Root module
├── main.ts                        # Entry point (bootstrap Nest app)
│
├── core/                          # Cấu hình và khởi tạo hạ tầng hệ thống
│   ├── config/                    # Configuration files
│   │   ├── app.config.ts
│   │   ├── database.config.ts
│   │   ├── jwt.config.ts
│   │   └── mail.config.ts
│   │
│   ├── database/                  # Database module
│   │   └── database.module.ts
│   │
│   ├── logger/                    # Logger service
│   │   └── logger.service.ts
│   │
│   └── utils/                     # Common utilities
│       ├── date.util.ts
│       ├── string.util.ts
│       └── response.util.ts
│
├── common/                        # Base classes and reusable logic
│   ├── base/                      # Base classes
│   │   ├── base.entity.ts
│   │   ├── base.service.ts
│   │   └── base.repository.ts
│   │
│   ├── decorators/                # Custom decorators
│   │   ├── roles.decorator.ts
│   │   ├── user.decorator.ts
│   │   └── public.decorator.ts
│   │
│   ├── filters/                   # Exception filters
│   │   └── http-exception.filter.ts
│   │
│   ├── interceptors/              # Interceptors
│   │   ├── transform.interceptor.ts
│   │   ├── timeout.interceptor.ts
│   │   └── logging.interceptor.ts
│   │
│   └── guards/                    # Auth guards
│       ├── jwt-auth.guard.ts
│       └── roles.guard.ts
│
├── shared/                        # Shared resources
│   ├── entities/                  # TypeORM entities
│   │   ├── user.entity.ts
│   │   ├── role.entity.ts
│   │   ├── post.entity.ts
│   │   ├── product.entity.ts
│   │   └── ...
│   │
│   ├── enums/                     # Enum definitions
│   │   ├── user-status.enum.ts
│   │   ├── order-status.enum.ts
│   │   └── ...
│   │
│   └── dto/                       # Shared DTOs
│       ├── pagination.dto.ts
│       └── response.dto.ts
│
├── modules/                       # Business logic modules
│   ├── admin/                     # Admin zone
│   │   ├── admin.module.ts
│   │   ├── controllers/
│   │   └── services/
│   │
│   ├── user/                      # User zone (authenticated)
│   │   ├── user.module.ts
│   │   ├── controllers/
│   │   └── services/
│   │
│   ├── public/                    # Public zone (no auth)
│   │   ├── public.module.ts
│   │   ├── controllers/
│   │   └── services/
│   │
│   ├── auth/                      # Authentication module
│   │   ├── auth.module.ts
│   │   ├── auth.controller.ts
│   │   ├── auth.service.ts
│   │   ├── dto/
│   │   └── strategies/
│   │
│   ├── enum/                      # Enum module
│   │   ├── enum.module.ts
│   │   └── enum.controller.ts
│   │
│   └── file/                      # File upload module
│       └── file.module.ts
│
└── typings/                       # Global type definitions
    ├── global.d.ts
    └── api-response.interface.ts
```

## Key Changes Made

### 1. Core Folder
- **Config**: Centralized configuration files for app, database, JWT, and mail
- **Database**: Database module for TypeORM setup
- **Logger**: Centralized logging service
- **Utils**: Reusable utility functions (date, string, response)

### 2. Common Folder
- **Base**: Base entity, service, and repository classes
- **Decorators**: Custom decorators (Roles, CurrentUser, Public)
- **Filters**: Exception handling filters
- **Interceptors**: Response transformation, logging, and timeout interceptors
- **Guards**: Authentication and authorization guards

### 3. Shared Folder
- **Entities**: All TypeORM entities moved from `src/entities/` to `shared/entities/`
- **Enums**: All enums moved from `src/enums/` to `shared/enums/`
- **DTOs**: Shared DTOs for pagination and responses

### 4. Typings Folder
- **global.d.ts**: Global type definitions for environment variables
- **api-response.interface.ts**: API response interface definitions

## Import Updates

All imports have been updated throughout the application:
- Entity imports: `from '../../shared/entities/...'`
- Enum imports: `from '../../shared/enums/...'`
- Guard imports: `from '../../../common/guards/...'`
- Decorator imports: `from '../../../common/decorators/...'`

## Benefits

1. **Better Organization**: Clear separation of concerns
2. **Reusability**: Shared resources accessible across modules
3. **Maintainability**: Easier to find and maintain code
4. **Scalability**: Easy to add new modules and features
5. **Consistency**: Standardized structure across the application

## Next Steps

1. Update any remaining imports that might have been missed
2. Add more utilities to `core/utils/` as needed
3. Implement file upload module in `modules/file/`
4. Add more base classes to `common/base/` if needed
5. Create DTOs for each module following the pattern
