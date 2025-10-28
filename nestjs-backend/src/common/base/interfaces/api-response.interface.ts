/**
 * Standard API Response Interface
 */

export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  code?: string;
  httpStatus: number;
  meta?: {
    page?: number;
    limit?: number;
    totalItems?: number;
    totalPages?: number;
    hasNextPage?: boolean;
    hasPreviousPage?: boolean;
    nextPage?: number;
    previousPage?: number;
  };
  data?: T;
  timestamp: number;
}

export interface PaginatedApiResponse<T = any> extends ApiResponse<T[]> {
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

/**
 * Response Builder Class
 */
export class ResponseBuilder {
  static success<T>(
    data: T,
    message: string = 'Thành công',
    httpStatus: number = 200,
    code?: string,
    meta?: any,
  ): ApiResponse<T> {
    return {
      success: true,
      message,
      code,
      httpStatus,
      meta,
      data,
      timestamp: Date.now(),
    };
  }

  static error(
    message: string,
    httpStatus: number = 400,
    code?: string,
    meta?: any,
  ): ApiResponse<null> {
    return {
      success: false,
      message,
      code,
      httpStatus,
      meta,
      data: null,
      timestamp: Date.now(),
    };
  }

  static paginated<T>(
    data: T[],
    meta: {
      page: number;
      limit: number;
      totalItems: number;
      totalPages: number;
      hasNextPage: boolean;
      hasPreviousPage: boolean;
      nextPage?: number;
      previousPage?: number;
    },
    message: string = 'Thành công',
    httpStatus: number = 200,
    code?: string,
  ): PaginatedApiResponse<T> {
    return {
      success: true,
      message,
      code,
      httpStatus,
      meta,
      data,
      timestamp: Date.now(),
    };
  }
}
