import { FindOptionsWhere } from 'typeorm';
import { SortOptions, FilterOptions } from '../../../typings/api-response.interface';

/**
 * Filters - Điều kiện tìm kiếm (conditions)
 * Có thể dùng object key-value đơn giản hoặc FilterOptions[] nâng cao
 */
export type Filters<T> = 
  | Partial<Record<keyof T, any>>
  | FindOptionsWhere<T>
  | FindOptionsWhere<T>[]
  | FilterOptions[];

/**
 * Options - Các tùy chọn phân trang, sắp xếp, relations, etc.
 */
export interface Options {
  // Pagination
  page?: number;
  limit?: number;
  
  // Search
  search?: string;
  searchFields?: string[];
  
  // Sorting
  sortBy?: string;
  sortOrder?: 'ASC' | 'DESC';
  sort?: SortOptions[];
  
  // Relations
  relations?: string[];
  
  // Other
  select?: string[];
  includeDeleted?: boolean;
}

/**
 * Interface tối giản cho list query (Legacy - compatibility)
 */
export interface FindAllOptions<T = any> extends Options {
  filters?: Filters<T>;
}

/**
 * Interface for paginated list result
 */
export interface PaginatedListResult<T> {
  data: T[];
  meta: {
    page: number;
    limit: number;
    totalItems: number;
    totalPages: number;
    hasNextPage: boolean;
    hasPreviousPage: boolean;
    nextPage?: number;
    previousPage?: number;
  };
}

