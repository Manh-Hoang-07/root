# Refactor CRUD Controllers với BaseController

## Trước khi refactor

Mỗi controller đều phải viết lại các method CRUD cơ bản:

```typescript
@Controller('admin/permissions')
export class PermissionsController extends BaseController<any> {
  // ... constructor và setup

  @Get()
  async list(
    @Query('page') page: string = '1',
    @Query('per_page') perPage: string = '20',
    @Query('status') status?: string,
    @Query('search') search?: string,
  ) {
    const filters: any = {};
    if (status) filters.status = status;
    if (search) filters.search = search;

    const result = await this.permissionsService.list(
      filters,
      parseInt(perPage),
      parseInt(page),
    );
    return this.handleListResponse(result);
  }

  @Get(':id')
  async get(@Param('id') id: string) {
    const result = await this.permissionsService.get(id);
    return this.handleResponse(result, 'Permission not found');
  }

  @Post()
  async create(@Body() createDto: any) {
    const result = await this.permissionsService.create(createDto);
    return this.handleResponse(result);
  }

  @Put(':id')
  async update(@Param('id') id: string, @Body() updateDto: any) {
    const result = await this.permissionsService.update(id, updateDto);
    return this.handleResponse(result, 'Permission not found');
  }

  @Delete(':id')
  async delete(@Param('id') id: string) {
    const result = await this.permissionsService.delete(id);
    return this.handleResponse(result, 'Permission not found', 'Permission deleted successfully');
  }
}
```

## Sau khi refactor

Controller chỉ cần override các method names và entity name:

```typescript
@Controller('admin/permissions')
export class PermissionsController extends BaseController<any> {
  protected service = this.permissionsService;
  
  constructor(private readonly permissionsService: PermissionsService) {
    super();
  }

  // Override để customize entity name
  protected getEntityName(): string {
    return 'Permission';
  }
}
```

## BaseController Features

### 1. CRUD Methods tự động
- `@Get()` - list với pagination và filters
- `@Get(':id')` - get by id
- `@Post()` - create
- `@Put(':id')` - update
- `@Delete(':id')` - delete

### 2. Customizable Method Names
Override các method này để customize service method names:

```typescript
protected getListMethodName(): string {
  return 'getMenus'; // thay vì 'list'
}

protected getGetMethodName(): string {
  return 'getMenu'; // thay vì 'get'
}

protected getCreateMethodName(): string {
  return 'createMenu'; // thay vì 'create'
}

protected getUpdateMethodName(): string {
  return 'updateMenu'; // thay vì 'update'
}

protected getDeleteMethodName(): string {
  return 'deleteMenu'; // thay vì 'delete'
}
```

### 3. Customizable Entity Names
Override để customize error messages:

```typescript
protected getEntityName(): string {
  return 'Menu'; // "Menu not found", "Menu deleted successfully"
}
```

## Lợi ích

1. **DRY Principle**: Không cần viết lại code CRUD cơ bản
2. **Consistency**: Tất cả controller có cùng behavior
3. **Maintainability**: Sửa logic chung chỉ cần sửa ở BaseController
4. **Flexibility**: Vẫn có thể override method riêng nếu cần custom logic
5. **Type Safety**: Vẫn giữ được type checking

## Cách sử dụng cho controller mới

```typescript
@Controller('admin/categories')
export class CategoriesController extends BaseController<any> {
  protected service = this.categoriesService;
  
  constructor(private readonly categoriesService: CategoriesService) {
    super();
  }

  protected getEntityName(): string {
    return 'Category';
  }

  // Nếu service methods có tên khác, override:
  protected getListMethodName(): string {
    return 'getCategories';
  }
  
  // Nếu cần custom logic, override method:
  @Get()
  async list(@Query() query: any) {
    // Custom logic here
    return super.list(query.page, query.per_page, query.status, query.search);
  }
}
```
