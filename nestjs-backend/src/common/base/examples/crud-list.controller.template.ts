import { Controller, Get, Post, Put, Delete, Body, Param, Query, UseInterceptors, HttpStatus } from '@nestjs/common';
import { ResponseInterceptor } from '../interceptors/response.interceptor';
import { CrudService } from '../services/crud.service';
import { Filters, Options } from '../interfaces/list.interface';

// HOW TO USE
// 1) Copy this file into your module (e.g., src/modules/users/users.controller.ts)
// 2) Replace all occurrences of ReplaceEntity with your entity type
// 3) Inject your concrete CrudService<ReplaceEntity>
// 4) Change @Controller('replace-path') to your route prefix

// Example:
// @Controller('users')
// export class UsersController extends CrudListControllerTemplate<User> {
//   constructor(protected readonly service: UsersService) { super(service); }
//   protected getListMessage() { return 'Lấy danh sách user thành công'; }
// }

@UseInterceptors(ResponseInterceptor)
@Controller('replace-path')
export class CrudListControllerTemplate<ReplaceEntity> {
  constructor(protected readonly service: CrudService<ReplaceEntity>) {}

  // GET /replace-path
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

  // POST /replace-path/search
  @Post('search')
  async searchWithFilters(@Body() body: { filters?: Filters<ReplaceEntity>; options?: Options }) {
    const result = await this.service.findAll(body.filters, body.options);
    return {
      data: result.data,
      meta: result.meta,
      message: this.getSearchMessage(),
      code: 'SUCCESS',
    };
  }

  // GET /replace-path/search?q=keyword
  @Get('search')
  async search(@Query('q') keyword: string, @Query('limit') limit?: number) {
    const data = await this.service.search(keyword, limit || 10);
    return {
      data,
      message: this.getSearchMessage(),
      code: 'SUCCESS',
    };
  }

  // GET /replace-path/:id
  @Get(':id')
  async findOne(@Param('id') id: string) {
    const data = await this.service.findByIdOrFail(id);
    return {
      data,
      message: this.getFindOneMessage(),
      code: 'SUCCESS',
    };
  }

  // POST /replace-path
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

  // POST /replace-path/bulk
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

  // PUT /replace-path/:id
  @Put(':id')
  async update(@Param('id') id: string, @Body() updateDto: any) {
    const data = await this.service.update(id, updateDto);
    return {
      data,
      message: this.getUpdateMessage(),
      code: 'UPDATED',
    };
  }

  // PUT /replace-path/bulk
  @Put('bulk')
  async updateMany(@Body() body: { updates: Array<{ id: string; data: any }> }) {
    const data = await this.service.updateMany(body.updates);
    return {
      data,
      message: this.getUpdateManyMessage(),
      code: 'UPDATED',
    };
  }

  // DELETE /replace-path/:id
  @Delete(':id')
  async softDelete(@Param('id') id: string) {
    await this.service.softDelete(id);
    return {
      data: null,
      message: this.getDeleteMessage(),
      code: 'DELETED',
    };
  }

  // DELETE /replace-path/bulk
  @Delete('bulk')
  async softDeleteMany(@Body() body: { ids: string[] }) {
    await this.service.softDeleteMany(body.ids);
    return {
      data: null,
      message: this.getDeleteManyMessage(),
      code: 'DELETED',
    };
  }

  // DELETE /replace-path/:id/hard
  @Delete(':id/hard')
  async hardDelete(@Param('id') id: string) {
    await this.service.delete(id);
    return {
      data: null,
      message: this.getHardDeleteMessage(),
      code: 'HARD_DELETED',
    };
  }

  // POST /replace-path/:id/restore
  @Post(':id/restore')
  async restore(@Param('id') id: string) {
    const data = await this.service.restore(id);
    return {
      data,
      message: this.getRestoreMessage(),
      code: 'RESTORED',
    };
  }

  // POST /replace-path/restore
  @Post('restore')
  async restoreMany(@Body() body: { ids: string[] }) {
    const data = await this.service.restoreMany(body.ids);
    return {
      data,
      message: this.getRestoreManyMessage(),
      code: 'RESTORED',
    };
  }

  // GET /replace-path/:id/exists
  @Get(':id/exists')
  async exists(@Param('id') id: string) {
    const exists = await this.service.exists(id);
    return {
      data: { exists },
      message: this.getExistsMessage(),
      code: 'SUCCESS',
    };
  }

  // GET /replace-path/count
  @Get('count')
  async count(@Query() filters: any) {
    const count = await this.service.count(filters);
    return {
      data: { count },
      message: this.getCountMessage(),
      code: 'SUCCESS',
    };
  }

  // ===== Messages (override tùy module) =====
  protected getListMessage() { return 'Lấy danh sách thành công'; }
  protected getSearchMessage() { return 'Tìm kiếm thành công'; }
  protected getFindOneMessage() { return 'Lấy thông tin thành công'; }
  protected getCreateMessage() { return 'Tạo mới thành công'; }
  protected getCreateManyMessage() { return 'Tạo nhiều bản ghi thành công'; }
  protected getUpdateMessage() { return 'Cập nhật thành công'; }
  protected getUpdateManyMessage() { return 'Cập nhật nhiều bản ghi thành công'; }
  protected getDeleteMessage() { return 'Xóa thành công'; }
  protected getDeleteManyMessage() { return 'Xóa nhiều bản ghi thành công'; }
  protected getHardDeleteMessage() { return 'Xóa vĩnh viễn thành công'; }
  protected getRestoreMessage() { return 'Khôi phục thành công'; }
  protected getRestoreManyMessage() { return 'Khôi phục nhiều bản ghi thành công'; }
  protected getExistsMessage() { return 'Kiểm tra tồn tại thành công'; }
  protected getCountMessage() { return 'Đếm số lượng thành công'; }
}
