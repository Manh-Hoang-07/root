// @ts-nocheck
import { Controller, Get, Post, Put, Delete, Body, Param, Query, UseInterceptors } from '@nestjs/common';
import { ResponseInterceptor } from '../interceptors/response.interceptor';
import { CrudService } from '../services/crud.service';
import { BaseEntity } from '../entities/base.entity';
import { ResponseUtil } from '../../utils/response.util';
import { BaseCrudController } from '../controllers/base-crud.controller';

// HOW TO USE
// 1) Copy this class into your module (e.g., src/modules/users/users.controller.ts)
// 2) Replace T with your entity type in the subclass
// 3) Inject your concrete CrudService<T>
// 4) Change @Controller('replace-path') to your route prefix

@UseInterceptors(ResponseInterceptor)
@Controller('replace-path')
export class CrudListControllerTemplate<T extends BaseEntity> extends BaseCrudController<T> {
  constructor(protected readonly service: CrudService<T>) {
    super();
  }

  // Relations control – override in your controller to enforce which relations are loaded
  // Example:
  // protected getRelations(): string[] { return ['profile', 'roles']; }

  // GET /replace-path (REQUIRES pagination)
  @Get()
  async findAll(
    @Query('page') page?: string,
    @Query('limit') limit?: string,
    @Query('search') search?: string,
    @Query('sortBy') sortBy?: string,
    @Query('sortOrder') sortOrder?: 'ASC' | 'DESC',
    @Query('filters') filtersJson?: string,
  ) {
    const parsed = this.parseListQuery(page, limit, search, sortBy, sortOrder, filtersJson);

    if (!parsed.valid) {
      return ResponseUtil.invalidQuery(parsed.error);
    }

    const result = await this.service.findAll(parsed.filters, parsed.options);
    return ResponseUtil.paginated(result.data, result.meta);
  }

  // SEARCH unified: GET /replace-path/search (REQUIRES pagination)
  // Supports both keyword (q) and filters (JSON) simultaneously
  @Get('search')
  async search(
    @Query('page') page?: string,
    @Query('limit') limit?: string,
    @Query('q') keyword?: string,
    @Query('filters') filtersJson?: string,
    @Query('sortBy') sortBy?: string,
    @Query('sortOrder') sortOrder?: 'ASC' | 'DESC',
  ) {
    const parsed = this.parseListQuery(page, limit, keyword, sortBy, sortOrder, filtersJson);

    if (!parsed.valid) {
      return ResponseUtil.invalidQuery(parsed.error);
    }

    const result = await this.service.findAll(parsed.filters, parsed.options);
    return ResponseUtil.paginated(result.data, result.meta, 'Tìm kiếm thành công');
  }

  // GET /replace-path/:id
  @Get(':id')
  async find(@Param('id') id: string) {
    const data = await this.service.findByIdOrFail(id);
    return ResponseUtil.success(data, 'Lấy thông tin thành công');
  }

  // POST /replace-path
  @Post()
  async create(@Body() createDto: any) {
    const data = await this.service.create(createDto);
    return ResponseUtil.created(data);
  }

  // (createMany removed as requested)

  // PUT /replace-path/:id
  @Put(':id')
  async update(@Param('id') id: string, @Body() updateDto: any) {
    const data = await this.service.update(id, updateDto);
    return ResponseUtil.updated(data);
  }

  // PUT /replace-path/bulk
  @Put('bulk')
  async updateMany(@Body() body: { updates: Array<{ id: string; data: any }> }) {
    const data = await this.service.updateMany(body.updates);
    return ResponseUtil.updated(data, 'Cập nhật nhiều bản ghi thành công');
  }

  // DELETE /replace-path/:id
  @Delete(':id')
  async softDelete(@Param('id') id: string) {
    await this.service.softDelete(id);
    return ResponseUtil.deleted();
  }

  // DELETE /replace-path/bulk
  @Delete('bulk')
  async softDeleteMany(@Body() body: { ids: string[] }) {
    await this.service.softDeleteMany(body.ids);
    return ResponseUtil.deleted('Xóa nhiều bản ghi thành công');
  }

  // DELETE /replace-path/:id/hard
  @Delete(':id/hard')
  async hardDelete(@Param('id') id: string) {
    await this.service.delete(id);
    return ResponseUtil.deleted('Xóa vĩnh viễn thành công');
  }

  // POST /replace-path/:id/restore
  @Post(':id/restore')
  async restore(@Param('id') id: string) {
    const data = await this.service.restore(id);
    return ResponseUtil.restored(data);
  }

  // POST /replace-path/restore
  @Post('restore')
  async restoreMany(@Body() body: { ids: string[] }) {
    const data = await this.service.restoreMany(body.ids);
    return ResponseUtil.restored(data, 'Khôi phục nhiều bản ghi thành công');
  }

  // (exists, count removed as requested)
}
  
// Relations control – override in your controller to enforce which relations are loaded
// Example:
// protected getRelations(): string[] { return ['profile', 'roles']; }
