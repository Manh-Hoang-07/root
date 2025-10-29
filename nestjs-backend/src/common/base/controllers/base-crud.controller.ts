import { Filters, Options } from '../interfaces/list.interface';
import { BaseEntity } from '../entities/base.entity';

export interface ParseListQueryResult<T> {
  filters: Filters<T>;
  options: Options;
  valid: boolean;
  error?: string;
}

/**
 * Base Controller với các method chung cho CRUD operations
 * Extend từ class này để tránh duplicate code
 */
export abstract class BaseCrudController<T extends BaseEntity> {
  /**
   * Parse và validate query parameters cho list operations
   */
  protected parseListQuery(
    page?: string,
    limit?: string,
    search?: string,
    sortBy?: string,
    sortOrder?: 'ASC' | 'DESC',
    filtersJson?: string,
  ): ParseListQueryResult<T> {
    const pageNum = Number(page);
    const limitNum = Number(limit);
    const paginationValid = !!page && !!limit && Number.isFinite(pageNum) && Number.isFinite(limitNum) && pageNum > 0 && limitNum > 0;

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
        return { 
          filters: {} as Filters<T>, 
          options: { ...options }, 
          valid: false, 
          error: 'filters phải là JSON hợp lệ' 
        };
      }
    }

    if (!paginationValid) {
      return { 
        filters: filters as Filters<T>, 
        options, 
        valid: false, 
        error: 'page và limit là bắt buộc và phải > 0' 
      };
    }

    return { filters: filters as Filters<T>, options, valid: true };
  }

  /**
   * Override method này trong controller con để định nghĩa relations cần load
   * @returns Array of relation names
   */
  protected getRelations(): string[] {
    return [];
  }
}
