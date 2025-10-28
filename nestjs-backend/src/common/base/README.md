# Base Services Documentation

Base services cung cấp các lớp cơ sở để xây dựng các service cho các entity trong ứng dụng NestJS.

## Cấu trúc

### 1. BaseEntity
Lớp entity cơ sở với các trường chung:
- `id`: UUID primary key
- `createdAt`, `updatedAt`, `deletedAt`: Timestamps
- `createdBy`, `updatedBy`, `deletedBy`: Audit fields
- Methods: `softDelete()`, `restore()`, `toJSON()`

### 2. ListService
Service cơ sở cho việc **lấy danh sách** với đầy đủ tính năng:
- ✅ Phân trang (Pagination)
- ✅ Sắp xếp (Sorting)
- ✅ Lọc (Filtering) 
- ✅ Tìm kiếm (Search)
- ✅ Relationships (Quan hệ)
- ✅ Soft delete filtering

**Vị trí:** `src/common/base/services/list.service.ts`

### 3. CrudService
Service cơ sở cho **CRUD operations**, kế thừa từ `ListService`:
- ✅ Tất cả tính năng của ListService
- ✅ Create (Tạo mới)
- ✅ Update (Cập nhật)
- ✅ Delete (Xóa - Soft & Hard)
- ✅ Restore (Khôi phục)
- ✅ Bulk operations
- ✅ Hooks (beforeCreate, afterUpdate, etc.)

**Vị trí:** `src/common/base/services/crud.service.ts`


## Cách sử dụng

### Option 1: Sử dụng ListService (chỉ cần listing)

```typescript
import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { ListService } from '@/common/base';
import { YourEntity } from './entities/your-entity.entity';

@Injectable()
export class YourListService extends ListService<YourEntity> {
  constructor(
    @InjectRepository(YourEntity)
    repository: Repository<YourEntity>,
  ) {
    super(repository);
  }

  // Override để chỉ định các trường tìm kiếm mặc định
  protected getDefaultSearchFields(): (keyof YourEntity)[] {
    return ['name', 'email', 'description'];
  }

  // Override để chỉ định các relations mặc định
  protected getDefaultRelations(): string[] {
    return ['category', 'tags'];
  }
}
```

### Option 2: Sử dụng CrudService (đầy đủ CRUD)

```typescript
import { Injectable } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository } from 'typeorm';
import { CrudService } from '@/common/base';
import { YourEntity } from './entities/your-entity.entity';

@Injectable()
export class YourCrudService extends CrudService<YourEntity> {
  constructor(
    @InjectRepository(YourEntity)
    repository: Repository<YourEntity>,
  ) {
    super(repository);
  }

  // Override hooks nếu cần
  protected async beforeCreate(entity: YourEntity, createDto: any): Promise<void> {
    // Validation hoặc logic tùy chỉnh
    console.log('Before create:', entity);
  }

  protected async afterCreate(entity: YourEntity, createDto: any): Promise<void> {
    // Logic sau khi tạo (gửi email, notification, etc.)
    console.log('After create:', entity);
  }
}
```

## Ví dụ sử dụng trong Controller

### Listing với phân trang (TỐI GIẢN - Filters & Options)

```typescript
import { Controller, Get, Query } from '@nestjs/common';
import { PaginationDto } from '@/shared/dto/pagination.dto';
import { YourCrudService } from './your-crud.service';

@Controller('items')
export class YourController {
  constructor(private readonly service: YourCrudService) {}

  // Cách 1: Đơn giản nhất - chỉ cần PaginationDto từ query params
  @Get()
  async findAll(@Query() paginationDto: PaginationDto) {
    return this.service.findAll(paginationDto);
  }

  // Cách 2: TÁCH RIÊNG - Filters (điều kiện) và Options (tùy chọn)
  // Filters: Điều kiện tìm kiếm
  // Options: Pagination, sorting, relations, search
  @Get('active')
  async findActive(@Query() paginationDto: PaginationDto) {
    return this.service.findAll(
      { status: 'active' }, // filters - điều kiện
      { ...paginationDto, relations: ['category'] } // options - tùy chọn
    );
  }

  // Cách 3: Filters đơn giản (object key-value)
  @Get('filtered')
  async findFiltered(@Query() paginationDto: PaginationDto) {
    return this.service.findAll(
      { status: 'active', categoryId: '123' }, // filters
      { ...paginationDto, sortBy: 'createdAt', sortOrder: 'DESC' } // options
    );
  }

  // Cách 4: Filters nâng cao (FilterOptions[])
  @Get('advanced-filters')
  async findAdvancedFilters(@Query() paginationDto: PaginationDto) {
    return this.service.findAll(
      [ // filters - nâng cao
        { field: 'status', operator: 'eq', value: 'active' },
        { field: 'price', operator: 'gte', value: 100 },
        { field: 'price', operator: 'lte', value: 1000 },
      ],
      { ...paginationDto, sortBy: 'price' } // options
    );
  }

  // Cách 5: Chỉ filters - không có options
  @Get('simple-list')
  async getSimpleList() {
    return this.service.findAll({ status: 'active' });
  }

  // Cách 6: Chỉ options - không có filters
  @Get('paginated')
  async getPaginated(@Query() paginationDto: PaginationDto) {
    return this.service.findAll(undefined, paginationDto);
  }

  // Cách 7: Gộp lại trong một object (vẫn hỗ trợ)
  @Get('combined')
  async getCombined() {
    return this.service.findAll({
      filters: { status: 'active' },
      page: 1,
      limit: 20,
      sortBy: 'createdAt',
      relations: ['category'],
    });
  }

  // Tìm kiếm với filters và options
  @Get('search')
  async search(
    @Query('q') query: string,
    @Query() paginationDto: PaginationDto,
  ) {
    return this.service.findAll(
      { status: 'active' }, // filters
      { ...paginationDto, search: query } // options có search
    );
  }
}
```

### CRUD Operations (Tối giản)

```typescript
import { Controller, Post, Put, Delete, Body, Param } from '@nestjs/common';
import { YourCrudService } from './your-crud.service';
import { CreateDto, UpdateDto } from './dto';

@Controller('items')
export class YourController {
  constructor(private readonly service: YourCrudService) {}

  @Post()
  async create(@Body() createDto: CreateDto, @User() user: any) {
    return this.service.create(createDto, user.id);
  }

  @Put(':id')
  async update(
    @Param('id') id: string,
    @Body() updateDto: UpdateDto,
    @User() user: any,
  ) {
    // Tự động detect có relations hay không
    return this.service.update(id, updateDto, user.id);
  }

  @Get(':id')
  async findOne(@Param('id') id: string) {
    // Tự động dùng default relations
    return this.service.findById(id);
  }

  @Delete(':id')
  async delete(@Param('id') id: string, @User() user: any) {
    await this.service.softDelete(id, user.id);
    return { message: 'Deleted successfully' };
  }

  @Post('restore/:id')
  async restore(@Param('id') id: string) {
    return this.service.restore(id);
  }

  // Tìm hoặc tạo - có thể dùng ID
  @Post('find-or-create/:id?')
  async findOrCreate(
    @Param('id') id: string,
    @Body() createDto?: CreateDto,
    @User() user?: any,
  ) {
    return this.service.findOrCreate(id || { email: createDto.email }, createDto, user?.id);
  }

  // Kiểm tra tồn tại - có thể dùng ID
  @Get('exists/:id')
  async exists(@Param('id') id: string) {
    return { exists: await this.service.exists(id) };
  }
}
```

## Các tính năng chi tiết

### Pagination
```typescript
// Kết quả trả về
{
  data: Entity[],
  meta: {
    page: number,
    limit: number,
    totalItems: number,
    totalPages: number,
    hasNextPage: boolean,
    hasPreviousPage: boolean,
    nextPage?: number,
    previousPage?: number,
  }
}
```

### Sorting
```typescript
sort: [
  { field: 'createdAt', direction: 'DESC' },
  { field: 'name', direction: 'ASC' },
]
```

### Filtering
Các operators hỗ trợ:
- `eq`: Equal
- `ne`: Not equal
- `gt`: Greater than
- `gte`: Greater than or equal
- `lt`: Less than
- `lte`: Less than or equal
- `in`: In array
- `nin`: Not in array
- `like`: Like pattern
- `between`: Between two values

### Search
Tìm kiếm trên nhiều trường cùng lúc với pattern LIKE.

### Relationships
Tự động load relationships khi chỉ định trong `relations` hoặc `withRelations`.

### Soft Delete
Mặc định tự động lọc các bản ghi đã bị xóa mềm. Dùng `includeDeleted: true` để bao gồm.

## Tính năng - Tối giản Filters & Options

### ✅ Tách riêng Filters và Options
- **Filters**: Điều kiện tìm kiếm (conditions) - `{ status: 'active' }` hoặc `FilterOptions[]`
- **Options**: Tùy chọn (pagination, sorting, relations, search) - `{ page: 1, limit: 20, sortBy: 'name' }`

### ✅ API cực kỳ đơn giản
```typescript
// Tách riêng - Rõ ràng nhất!
service.findAll(filters, options)

// Ví dụ:
service.findAll(
  { status: 'active' },                    // filters - điều kiện
  { page: 1, limit: 20, sortBy: 'name' }  // options - tùy chọn
)

// Hoặc gộp lại (vẫn hỗ trợ)
service.findAll({
  filters: { status: 'active' },
  page: 1,
  limit: 20,
})
```

### ✅ Filters - Hỗ trợ nhiều format
```typescript
// Object đơn giản (tự động =)
{ status: 'active', categoryId: '123' }

// FilterOptions[] (nâng cao)
[
  { field: 'price', operator: 'gte', value: 100 },
  { field: 'price', operator: 'lte', value: 1000 }
]

// FindOptionsWhere
{ status: 'active', deletedAt: null }
```

### ✅ Options - Tất cả tùy chọn
```typescript
{
  page?: number;           // Phân trang
  limit?: number;          // Số lượng/trang
  search?: string;         // Tìm kiếm
  searchFields?: string[]; // Trường tìm kiếm
  sortBy?: string;         // Sắp xếp theo trường
  sortOrder?: 'ASC'|'DESC';// Thứ tự
  sort?: SortOptions[];    // Sắp xếp nâng cao
  relations?: string[];    // Quan hệ
  select?: string[];       // Chọn trường
  includeDeleted?: boolean;// Bao gồm đã xóa
}
```

## Cấu trúc thư mục

```
src/common/base/
├── entities/
│   ├── base.entity.ts         ← BaseEntity
│   └── index.ts               ← Export entities
├── repositories/
│   ├── base.repository.ts      ← BaseRepository
│   └── index.ts                ← Export repositories
├── interfaces/
│   ├── list.interface.ts       ← Filters, Options, PaginatedListResult
│   └── index.ts                ← Export interfaces
├── services/
│   ├── list.service.ts         ← ListService
│   ├── crud.service.ts         ← CrudService
│   └── index.ts                ← Export services
├── index.ts                    ← Export tất cả
└── README.md
```

## Best Practices

1. **Sử dụng ListService** khi chỉ cần listing (Read-only)
2. **Sử dụng CrudService** khi cần đầy đủ CRUD
3. **Override hooks** để thêm validation, logging, hoặc business logic
4. **Override getDefaultSearchFields()** để chỉ định các trường tìm kiếm mặc định (quan trọng!)
5. **Override getDefaultRelations()** để tự động load relationships cần thiết (quan trọng!)
6. **Sử dụng PaginationDto** từ query params - đơn giản và dễ dùng
7. **Sử dụng Soft Delete** thay vì Hard Delete khi có thể

