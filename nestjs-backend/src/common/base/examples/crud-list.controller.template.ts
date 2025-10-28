// @ts-nocheck
import { Controller, Get, Post, Put, Delete, Body, Param, Query, UseInterceptors, HttpStatus, BadRequestException } from '@nestjs/common';
import { ResponseInterceptor } from '../interceptors/response.interceptor';
import { CrudService } from '../services/crud.service';
import { Filters, Options } from '../interfaces/list.interface';
import { BaseEntity } from '../entities/base.entity';

// HOW TO USE
// 1) Copy this class into your module (e.g., src/modules/users/users.controller.ts)
// 2) Replace T with your entity type in the subclass
// 3) Inject your concrete CrudService<T>
// 4) Change @Controller('replace-path') to your route prefix

@UseInterceptors(ResponseInterceptor)
@Controller('replace-path')
export class CrudListControllerTemplate<T extends BaseEntity> {
  constructor(protected readonly service: CrudService<T>) {}

  // Helper method để parse query params và validate pagination
  protected parseListQuery(
    page?: string,
    limit?: string,
    search?: string,
    sortBy?: string,
    sortOrder?: 'ASC' | 'DESC',
    filtersJson?: string,
  ): { filters: Filters<T>; options: Options } {
    const pageNum = Number(page);
    const limitNum = Number(limit);
    if (!page || !limit || !Number.isFinite(pageNum) || !Number.isFinite(limitNum) || pageNum < 1 || limitNum < 1) {
      throw new BadRequestException('page và limit là bắt buộc và phải > 0');
    }

    const options: Options = {
      page: pageNum,
      limit: limitNum,
      search,
      sortBy,
      sortOrder,
      relations: this.getRelations(),
    };

    // Parse filters from query (JSON) → always pass an object (not undefined)
    let filters: Filters<T> | Record<string, unknown> = {};
    if (filtersJson) {
      try {
        filters = JSON.parse(filtersJson);
      } catch {
        throw new BadRequestException('filters phải là JSON hợp lệ');
      }
    }

    return { filters: filters as Filters<T>, options };
  }

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
    const { filters, options } = this.parseListQuery(page, limit, search, sortBy, sortOrder, filtersJson);

    const result = await this.service.findAll(filters, options);
    return {
      data: result.data,
      meta: result.meta,
      message: 'Lấy danh sách thành công',
      code: 'SUCCESS',
    };
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
    const { filters, options } = this.parseListQuery(page, limit, keyword, sortBy, sortOrder, filtersJson);

    const result = await this.service.findAll(filters, options);
    return { data: result.data, meta: result.meta, message: 'Tìm kiếm thành công', code: 'SUCCESS' };
  }

  // GET /replace-path/:id
  @Get(':id')
  async find(@Param('id') id: string) {
    const data = await this.service.findByIdOrFail(id);
    return { data, message: 'Lấy thông tin thành công', code: 'SUCCESS' };
  }

  // POST /replace-path
  @Post()
  async create(@Body() createDto: any) {
    const data = await this.service.create(createDto);
    return { data, message: 'Tạo mới thành công', code: 'CREATED', httpStatus: HttpStatus.CREATED };
  }

  // (createMany removed as requested)

  // PUT /replace-path/:id
  @Put(':id')
  async update(@Param('id') id: string, @Body() updateDto: any) {
    const data = await this.service.update(id, updateDto);
    return { data, message: 'Cập nhật thành công', code: 'UPDATED' };
  }

  // PUT /replace-path/bulk
  @Put('bulk')
  async updateMany(@Body() body: { updates: Array<{ id: string; data: any }> }) {
    const data = await this.service.updateMany(body.updates);
    return { data, message: 'Cập nhật nhiều bản ghi thành công', code: 'UPDATED' };
  }

  // DELETE /replace-path/:id
  @Delete(':id')
  async softDelete(@Param('id') id: string) {
    await this.service.softDelete(id);
    return { data: null, message: 'Xóa thành công', code: 'DELETED' };
  }

  // DELETE /replace-path/bulk
  @Delete('bulk')
  async softDeleteMany(@Body() body: { ids: string[] }) {
    await this.service.softDeleteMany(body.ids);
    return { data: null, message: 'Xóa nhiều bản ghi thành công', code: 'DELETED' };
  }

  // DELETE /replace-path/:id/hard
  @Delete(':id/hard')
  async hardDelete(@Param('id') id: string) {
    await this.service.delete(id);
    return { data: null, message: 'Xóa vĩnh viễn thành công', code: 'HARD_DELETED' };
  }

  // POST /replace-path/:id/restore
  @Post(':id/restore')
  async restore(@Param('id') id: string) {
    const data = await this.service.restore(id);
    return { data, message: 'Khôi phục thành công', code: 'RESTORED' };
  }

  // POST /replace-path/restore
  @Post('restore')
  async restoreMany(@Body() body: { ids: string[] }) {
    const data = await this.service.restoreMany(body.ids);
    return { data, message: 'Khôi phục nhiều bản ghi thành công', code: 'RESTORED' };
  }

  // (exists, count removed as requested)
}
  
// Relations control – override in your controller to enforce which relations are loaded
// Example:
// protected getRelations(): string[] { return ['profile', 'roles']; }
declare module './crud-list.controller.template' {}
