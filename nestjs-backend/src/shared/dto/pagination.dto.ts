import { Type } from 'class-transformer';
import { IsOptional, IsInt, Min, Max, IsString } from 'class-validator';

export class PaginationDto {
  @IsOptional()
  @Type(() => Number)
  @IsInt()
  @Min(1)
  page?: number = 1;

  @IsOptional()
  @Type(() => Number)
  @IsInt()
  @Min(1)
  @Max(100)
  limit?: number = 10;

  @IsOptional()
  @IsString()
  sortBy?: string;

  @IsOptional()
  @IsString()
  sortOrder?: 'ASC' | 'DESC' = 'DESC';

  @IsOptional()
  @IsString()
  search?: string;

  // Calculate skip value
  get skip(): number {
    return (this.page - 1) * this.limit;
  }
}

export class PaginationMetaDto {
  page: number;
  limit: number;
  totalItems: number;
  totalPages: number;
  hasNextPage: boolean;
  hasPreviousPage: boolean;
  nextPage?: number;
  previousPage?: number;

  constructor(pagination: PaginationDto, totalItems: number) {
    this.page = pagination.page;
    this.limit = pagination.limit;
    this.totalItems = totalItems;
    this.totalPages = Math.ceil(totalItems / pagination.limit);
    this.hasNextPage = pagination.page < this.totalPages;
    this.hasPreviousPage = pagination.page > 1;
    this.nextPage = this.hasNextPage ? pagination.page + 1 : undefined;
    this.previousPage = this.hasPreviousPage ? pagination.page - 1 : undefined;
  }
}

export class PaginatedResultDto<T> {
  data: T[];
  meta: PaginationMetaDto;

  constructor(data: T[], pagination: PaginationDto, totalItems: number) {
    this.data = data;
    this.meta = new PaginationMetaDto(pagination, totalItems);
  }
}

/**
 * Search and filter DTO
 */
export class SearchDto extends PaginationDto {
  @IsOptional()
  @IsString()
  searchFields?: string; // Comma-separated list of fields to search in

  @IsOptional()
  @IsString()
  filters?: string; // JSON string of filters

  // Parse filters from JSON string
  get parsedFilters(): Record<string, any> {
    if (!this.filters) return {};
    
    try {
      return JSON.parse(this.filters);
    } catch {
      return {};
    }
  }

  // Get search fields as array
  get searchFieldsArray(): string[] {
    if (!this.searchFields) return [];
    return this.searchFields.split(',').map(field => field.trim());
  }
}

/**
 * Date range filter DTO
 */
export class DateRangeDto {
  @IsOptional()
  @Type(() => Date)
  startDate?: Date;

  @IsOptional()
  @Type(() => Date)
  endDate?: Date;
}

/**
 * Combined pagination with date range
 */
export class PaginationWithDateRangeDto extends PaginationDto {
  @IsOptional()
  @Type(() => Date)
  startDate?: Date;

  @IsOptional()
  @Type(() => Date)
  endDate?: Date;
}

/**
 * Order by DTO for sorting
 */
export class OrderByDto {
  @IsOptional()
  @IsString()
  field?: string;

  @IsOptional()
  @IsString()
  direction?: 'ASC' | 'DESC' = 'DESC';
}

/**
 * Advanced search DTO
 */
export class AdvancedSearchDto extends PaginationDto {
  @IsOptional()
  @IsString()
  keyword?: string;

  @IsOptional()
  @IsString()
  category?: string;

  @IsOptional()
  @IsString()
  status?: string;

  @IsOptional()
  @Type(() => Date)
  createdFrom?: Date;

  @IsOptional()
  @Type(() => Date)
  createdTo?: Date;

  @IsOptional()
  @Type(() => Date)
  updatedFrom?: Date;

  @IsOptional()
  @Type(() => Date)
  updatedTo?: Date;

  @IsOptional()
  @IsString()
  createdBy?: string;

  @IsOptional()
  @IsString()
  updatedBy?: string;
}

/**
 * Utility functions for pagination
 */
export class PaginationUtils {
  /**
   * Create pagination metadata
   */
  static createMeta(
    page: number,
    limit: number,
    totalItems: number
  ): PaginationMetaDto {
    return new PaginationMetaDto({ page, limit } as PaginationDto, totalItems);
  }

  /**
   * Create paginated result
   */
  static createResult<T>(
    data: T[],
    page: number,
    limit: number,
    totalItems: number
  ): PaginatedResultDto<T> {
    return new PaginatedResultDto(data, { page, limit } as PaginationDto, totalItems);
  }

  /**
   * Validate pagination parameters
   */
  static validatePagination(page: number, limit: number): void {
    if (page < 1) {
      throw new Error('Page must be greater than 0');
    }
    if (limit < 1 || limit > 100) {
      throw new Error('Limit must be between 1 and 100');
    }
  }

  /**
   * Calculate offset for database queries
   */
  static calculateOffset(page: number, limit: number): number {
    return (page - 1) * limit;
  }

  /**
   * Calculate total pages
   */
  static calculateTotalPages(totalItems: number, limit: number): number {
    return Math.ceil(totalItems / limit);
  }
}
