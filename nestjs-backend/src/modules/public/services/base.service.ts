import { Repository, SelectQueryBuilder } from 'typeorm';

export abstract class BaseService<T> {
  constructor(protected readonly repository: Repository<T>) {}

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

    const queryBuilder = this.buildBaseQuery(filters);
    
    console.log('listItems - adding relations:', relations);
    this.addRelations(queryBuilder, relations);
    
    queryBuilder
      .orderBy('entity.created_at', 'DESC')
      .skip(skip)
      .take(validPerPage);

    console.log('Generated SQL:', queryBuilder.getSql());
    
    const [data, total] = await queryBuilder.getManyAndCount();
    
    console.log('Query executed, got', total, 'total items');

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
    const queryBuilder = this.buildBaseQuery()
      .where('entity.id = :id', { id: Number(id) });

    this.addRelations(queryBuilder, relations);

    return queryBuilder.getOne();
  }

  protected async findItemBySlug(slug: string, relations: string[] = []) {
    const queryBuilder = this.buildBaseQuery()
      .where('entity.slug = :slug', { slug });

    this.addRelations(queryBuilder, relations);

    return queryBuilder.getOne();
  }

  // Helper: Build base query
  protected buildBaseQuery(filters: any = {}): SelectQueryBuilder<T> {
    const queryBuilder = this.repository.createQueryBuilder('entity');

    // Add search if exists
    if (filters.search) {
      const searchFields = ['name', 'title', 'email'];
      for (const field of searchFields) {
        if (this.hasProperty(field)) {
          queryBuilder.andWhere(`entity.${field} LIKE :search`, { search: `%${filters.search}%` });
          break;
        }
      }
    }

    return queryBuilder;
  }

  // Helper: Add relations to query
  protected addRelations(queryBuilder: SelectQueryBuilder<T>, relations: string[]) {
    const availableRelations = this.getAvailableRelations();
    const validRelations = relations.filter(r => availableRelations.includes(r));
    
    for (const relation of validRelations) {
      queryBuilder.leftJoinAndSelect(`entity.${relation}`, relation);
    }
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
