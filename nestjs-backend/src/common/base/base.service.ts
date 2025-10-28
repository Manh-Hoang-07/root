import { Injectable, Logger } from '@nestjs/common';
import { InjectRepository } from '@nestjs/typeorm';
import { Repository, FindOptionsWhere, ILike, FindManyOptions } from 'typeorm';

@Injectable()
export abstract class BaseService<T> {
  protected readonly logger: Logger;

  constructor(
    protected readonly repository: Repository<T>,
    protected readonly entityName: string,
  ) {
    this.logger = new Logger(`${this.constructor.name}`);
  }

  // Metadata - override in child classes
  protected getRelations(): string[] {
    return [];
  }

  protected getAvailableRelations(): string[] {
    return [];
  }

  protected getIndexRelations(): string[] {
    return this.getRelations();
  }

  protected getShowRelations(): string[] {
    return this.getRelations();
  }

  // Main methods
  async findAll(): Promise<T[]> {
    return this.repository.find();
  }

  async findOne(id: number): Promise<T> {
    return this.repository.findOne({
      where: { id } as FindOptionsWhere<T>,
    });
  }

  async create(data: Partial<T>): Promise<T> {
    const entity = this.repository.create(data);
    return this.repository.save(entity);
  }

  async update(id: number, data: Partial<T>): Promise<T> {
    await this.repository.update(id, data);
    return this.findOne(id);
  }

  async remove(id: number): Promise<void> {
    await this.repository.softDelete(id);
  }

  // Advanced methods with pagination and search
  async getAll(filters: any = {}, perPage: number = 20, page: number = 1, relations?: string[]) {
    const validRelations = relations || this.getIndexRelations();
    return this.listItems(filters, perPage, page, validRelations);
  }

  async getOne(id: string, relations?: string[]) {
    const validRelations = relations || this.getShowRelations();
    return this.findItem(id, validRelations);
  }

  async getBySlug(slug: string, relations?: string[]) {
    const validRelations = relations || this.getShowRelations();
    return this.findItemBySlug(slug, validRelations);
  }

  // Core implementation
  protected async listItems(filters: any = {}, perPage: number = 20, page: number = 1, relations: string[] = []) {
    const validPerPage = Math.min(perPage, 100);
    const skip = (page - 1) * validPerPage;

    // Build where clause from base query
    const where = this.buildBaseQuery(filters);
    
    const findOptions: FindManyOptions<T> = {
      where,
      order: { created_at: 'DESC' } as any,
      relations: relations,
      skip,
      take: validPerPage,
    };
    
    const [data, total] = await this.repository.findAndCount(findOptions);

    return {
      data,
      meta: {
        total,
        per_page: validPerPage,
        current_page: page,
        last_page: Math.ceil(total / validPerPage),
        from: skip + 1,
        to: Math.min(skip + validPerPage, total),
      },
    };
  }

  protected async findItem(id: string, relations: string[] = []) {
    const where = this.buildBaseQuery();
    (where as any).id = Number(id);
    
    return this.repository.findOne({
      where,
      relations,
    });
  }

  protected async findItemBySlug(slug: string, relations: string[] = []) {
    const where = this.buildBaseQuery();
    (where as any).slug = slug;
    
    return this.repository.findOne({
      where,
      relations,
    });
  }

  // Helper: Build base where clause
  protected buildBaseQuery(filters: any = {}): FindOptionsWhere<T> {
    const where: any = {};

    // Add search if exists
    if (filters.search) {
      const searchFields = ['name', 'title', 'email'];
      for (const field of searchFields) {
        if (this.hasProperty(field)) {
          where[field] = ILike(`%${filters.search}%`);
          break;
        }
      }
    }

    return where as FindOptionsWhere<T>;
  }

  // Helper: Check if entity has property
  protected hasProperty(property: string): boolean {
    const metadata = this.repository.metadata;
    return metadata.columns.some(column => column.propertyName === property);
  }

  // Helper: Increment view count
  protected async incrementViewCount(entity: any) {
    if (entity && this.hasProperty('view_count')) {
      (entity as any).view_count += 1;
      await this.repository.save(entity);
    }
  }

  // Helper: Get simple list (no pagination) - for child classes that need it
  protected async getSimpleList(whereClause: any = {}, orderBy: any = {}) {
    return this.repository.find({
      where: whereClause,
      order: orderBy,
    });
  }
}
