import { Controller, Get, Post, Put, Delete, Param, Body, Query } from '@nestjs/common';

export abstract class BaseController<T> {
  protected abstract service: any;

  protected handleResponse(data: any, notFoundMessage?: string, successMessage?: string) {
    if (!data) {
      return {
        statusCode: 404,
        message: notFoundMessage || 'Not found',
        data: null,
      };
    }
    return successMessage ? { message: successMessage, data } : { data };
  }

  protected handleListResponse(result: any) {
    return {
      data: result.data || result.items,
      meta: result.meta || {
        total: result.total || 0,
        per_page: result.per_page || 20,
        current_page: result.current_page || 1,
        last_page: result.last_page || 1,
      },
    };
  }

  // CRUD Methods - có thể override trong controller con nếu cần custom logic
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

    const result = await this.service[this.getListMethodName()](
      filters,
      parseInt(perPage),
      parseInt(page),
    );
    return this.handleListResponse(result);
  }

  @Get(':id')
  async get(@Param('id') id: string) {
    const result = await this.service[this.getGetMethodName()](id);
    return this.handleResponse(result, `${this.getEntityName()} not found`);
  }

  @Post()
  async create(@Body() createDto: any) {
    const methodName = this.getCreateMethodName();
    if (!methodName) return { message: 'Create method not available' };
    
    const result = await this.service[methodName](createDto);
    return this.handleResponse(result);
  }

  @Put(':id')
  async update(@Param('id') id: string, @Body() updateDto: any) {
    const methodName = this.getUpdateMethodName();
    if (!methodName) return { message: 'Update method not available' };
    
    const result = await this.service[methodName](id, updateDto);
    return this.handleResponse(result, `${this.getEntityName()} not found`);
  }

  @Delete(':id')
  async delete(@Param('id') id: string) {
    const methodName = this.getDeleteMethodName();
    if (!methodName) return { message: 'Delete method not available' };
    
    const result = await this.service[methodName](id);
    return this.handleResponse(result, `${this.getEntityName()} not found`, `${this.getEntityName()} deleted successfully`);
  }

  // Override các method này trong controller con để customize method names
  protected getEntityName(): string {
    return 'Entity';
  }

  protected getListMethodName(): string {
    return 'list';
  }

  protected getGetMethodName(): string {
    return 'get';
  }

  protected getCreateMethodName(): string {
    return 'create';
  }

  protected getUpdateMethodName(): string {
    return 'update';
  }

  protected getDeleteMethodName(): string {
    return 'delete';
  }
}
