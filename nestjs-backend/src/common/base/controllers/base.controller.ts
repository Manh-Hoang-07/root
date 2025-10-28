import {
  Controller,
  Get,
  Post,
  Put,
  Delete,
  Body,
  Param,
  Query,
  UseInterceptors,
  HttpStatus,
} from '@nestjs/common';
import { CrudService } from '../services/crud.service';
import { ResponseInterceptor } from '../interceptors/response.interceptor';
import { Filters, Options, PaginatedListResult } from '../interfaces/list.interface';
import { BaseEntity } from '../entities/base.entity';

/**
 * Base Controller với đầy đủ CRUD và List operations
 * Các controller con chỉ cần kế thừa và khai báo route
 */
@UseInterceptors(ResponseInterceptor)
export abstract class BaseController<T extends BaseEntity> {
  constructor(protected readonly service: CrudService<T>) {}

  /**
   * Lấy danh sách với phân trang
   * GET /api/entities
   */
  @Get()
  async findAll(
    @Query('page') page?: number,
    @Query('limit') limit?: number,
    @Query('search') search?: string,
    @Query('sortBy') sortBy?: string,
    @Query('sortOrder') sortOrder?: 'ASC' | 'DESC',
    @Query('relations') relations?: string,
  ) {
    const options: Options = {
      page: page || 1,
      limit: limit || 10,
      search,
      sortBy,
      sortOrder,
      relations: relations ? relations.split(',') : undefined,
    };

    const result = await this.service.findAll(undefined, options);
    
    return {
      data: result.data,
      meta: result.meta,
      message: this.getListMessage(),
      code: 'SUCCESS',
    };
  }

  /**
   * Lấy danh sách với filters
   * POST /api/entities/search
   */
  @Post('search')
  async searchWithFilters(
    @Body() body: { filters?: Filters<T>; options?: Options },
  ) {
    const result = await this.service.findAll(body.filters, body.options);
    
    return {
      data: result.data,
      meta: result.meta,
      message: this.getSearchMessage(),
      code: 'SUCCESS',
    };
  }

  /**
   * Tìm kiếm đơn giản
   * GET /api/entities/search?q=keyword
   */
  @Get('search')
  async search(@Query('q') keyword: string, @Query('limit') limit?: number) {
    const data = await this.service.search(keyword, limit || 10);
    
    return {
      data,
      message: this.getSearchMessage(),
      code: 'SUCCESS',
    };
  }

  /**
   * Lấy một bản ghi theo ID
   * GET /api/entities/:id
   */
  @Get(':id')
  async findOne(@Param('id') id: string) {
    const data = await this.service.findByIdOrFail(id);
    
    return {
      data,
      message: this.getFindOneMessage(),
      code: 'SUCCESS',
    };
  }

  /**
   * Tạo mới
   * POST /api/entities
   */
  @Post()
  async create(@Body() createDto: any) {
    const data = await this.service.create(createDto);
    
    return {
      data,
      message: this.getCreateMessage(),
      code: 'CREATED',
      httpStatus: HttpStatus.CREATED,
    };
  }

  /**
   * Tạo nhiều bản ghi
   * POST /api/entities/bulk
   */
  @Post('bulk')
  async createMany(@Body() body: { data: any[] }) {
    const data = await this.service.createMany(body.data);
    
    return {
      data,
      message: this.getCreateManyMessage(),
      code: 'CREATED',
      httpStatus: HttpStatus.CREATED,
    };
  }

  /**
   * Cập nhật
   * PUT /api/entities/:id
   */
  @Put(':id')
  async update(@Param('id') id: string, @Body() updateDto: any) {
    const data = await this.service.update(id, updateDto);
    
    return {
      data,
      message: this.getUpdateMessage(),
      code: 'UPDATED',
    };
  }

  /**
   * Cập nhật nhiều bản ghi
   * PUT /api/entities/bulk
   */
  @Put('bulk')
  async updateMany(@Body() body: { updates: Array<{ id: string; data: any }> }) {
    const data = await this.service.updateMany(body.updates);
    
    return {
      data,
      message: this.getUpdateManyMessage(),
      code: 'UPDATED',
    };
  }

  /**
   * Xóa mềm
   * DELETE /api/entities/:id
   */
  @Delete(':id')
  async softDelete(@Param('id') id: string) {
    await this.service.softDelete(id);
    
    return {
      data: null,
      message: this.getDeleteMessage(),
      code: 'DELETED',
    };
  }

  /**
   * Xóa mềm nhiều bản ghi
   * DELETE /api/entities/bulk
   */
  @Delete('bulk')
  async softDeleteMany(@Body() body: { ids: string[] }) {
    await this.service.softDeleteMany(body.ids);
    
    return {
      data: null,
      message: this.getDeleteManyMessage(),
      code: 'DELETED',
    };
  }

  /**
   * Xóa cứng
   * DELETE /api/entities/:id/hard
   */
  @Delete(':id/hard')
  async hardDelete(@Param('id') id: string) {
    await this.service.delete(id);
    
    return {
      data: null,
      message: this.getHardDeleteMessage(),
      code: 'HARD_DELETED',
    };
  }

  /**
   * Khôi phục
   * POST /api/entities/:id/restore
   */
  @Post(':id/restore')
  async restore(@Param('id') id: string) {
    const data = await this.service.restore(id);
    
    return {
      data,
      message: this.getRestoreMessage(),
      code: 'RESTORED',
    };
  }

  /**
   * Khôi phục nhiều bản ghi
   * POST /api/entities/restore
   */
  @Post('restore')
  async restoreMany(@Body() body: { ids: string[] }) {
    const data = await this.service.restoreMany(body.ids);
    
    return {
      data,
      message: this.getRestoreManyMessage(),
      code: 'RESTORED',
    };
  }

  /**
   * Kiểm tra tồn tại
   * GET /api/entities/:id/exists
   */
  @Get(':id/exists')
  async exists(@Param('id') id: string) {
    const exists = await this.service.exists(id);
    
    return {
      data: { exists },
      message: this.getExistsMessage(),
      code: 'SUCCESS',
    };
  }

  /**
   * Đếm số lượng
   * GET /api/entities/count
   */
  @Get('count')
  async count(@Query() filters: any) {
    const count = await this.service.count(filters);
    
    return {
      data: { count },
      message: this.getCountMessage(),
      code: 'SUCCESS',
    };
  }

  // ==================== Override Methods ====================
  // Override các method này trong controller con để tùy chỉnh message

  protected getListMessage(): string {
    return 'Lấy danh sách thành công';
  }

  protected getSearchMessage(): string {
    return 'Tìm kiếm thành công';
  }

  protected getFindOneMessage(): string {
    return 'Lấy thông tin thành công';
  }

  protected getCreateMessage(): string {
    return 'Tạo mới thành công';
  }

  protected getCreateManyMessage(): string {
    return 'Tạo nhiều bản ghi thành công';
  }

  protected getUpdateMessage(): string {
    return 'Cập nhật thành công';
  }

  protected getUpdateManyMessage(): string {
    return 'Cập nhật nhiều bản ghi thành công';
  }

  protected getDeleteMessage(): string {
    return 'Xóa thành công';
  }

  protected getDeleteManyMessage(): string {
    return 'Xóa nhiều bản ghi thành công';
  }

  protected getHardDeleteMessage(): string {
    return 'Xóa vĩnh viễn thành công';
  }

  protected getRestoreMessage(): string {
    return 'Khôi phục thành công';
  }

  protected getRestoreManyMessage(): string {
    return 'Khôi phục nhiều bản ghi thành công';
  }

  protected getExistsMessage(): string {
    return 'Kiểm tra tồn tại thành công';
  }

  protected getCountMessage(): string {
    return 'Đếm số lượng thành công';
  }
}
