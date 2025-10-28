export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data?: T;
  meta?: any;
  errors?: any;
  timestamp: string;
}

export interface PaginationMeta {
  currentPage: number;
  itemCount: number;
  itemsPerPage: number;
  totalItems: number;
  totalPages: number;
  hasNextPage: boolean;
  hasPreviousPage: boolean;
}

export class ResponseUtil {
  /**
   * Create a success response
   */
  static success<T>(data?: T, message = 'Success', meta?: any): ApiResponse<T> {
    return {
      success: true,
      message,
      data,
      meta,
      timestamp: new Date().toISOString(),
    };
  }

  /**
   * Create an error response
   */
  static error(message = 'Error', errors?: any, data?: any): ApiResponse {
    return {
      success: false,
      message,
      data,
      errors,
      timestamp: new Date().toISOString(),
    };
  }

  /**
   * Create a paginated response
   */
  static paginated<T>(
    data: T[],
    currentPage: number,
    itemsPerPage: number,
    totalItems: number,
    message = 'Success',
  ): ApiResponse<T[]> {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const hasNextPage = currentPage < totalPages;
    const hasPreviousPage = currentPage > 1;

    const meta: PaginationMeta = {
      currentPage,
      itemCount: data.length,
      itemsPerPage,
      totalItems,
      totalPages,
      hasNextPage,
      hasPreviousPage,
    };

    return this.success(data, message, meta);
  }

  /**
   * Create a created response (201)
   */
  static created<T>(data?: T, message = 'Created'): ApiResponse<T> {
    return this.success(data, message);
  }

  /**
   * Create an updated response
   */
  static updated<T>(data?: T, message = 'Updated'): ApiResponse<T> {
    return this.success(data, message);
  }

  /**
   * Create a deleted response
   */
  static deleted(message = 'Deleted'): ApiResponse {
    return this.success(null, message);
  }

  /**
   * Create a not found response
   */
  static notFound(message = 'Not found'): ApiResponse {
    return this.error(message);
  }

  /**
   * Create a validation error response
   */
  static validationError(errors: any, message = 'Validation failed'): ApiResponse {
    return this.error(message, errors);
  }

  /**
   * Create a forbidden response
   */
  static forbidden(message = 'Forbidden'): ApiResponse {
    return this.error(message);
  }

  /**
   * Create an unauthorized response
   */
  static unauthorized(message = 'Unauthorized'): ApiResponse {
    return this.error(message);
  }

  /**
   * Create a bad request response
   */
  static badRequest(message = 'Bad request', errors?: any): ApiResponse {
    return this.error(message, errors);
  }

  /**
   * Create an internal server error response
   */
  static internalServerError(message = 'Internal server error'): ApiResponse {
    return this.error(message);
  }

  /**
   * Create a conflict response
   */
  static conflict(message = 'Conflict'): ApiResponse {
    return this.error(message);
  }

  /**
   * Create a too many requests response
   */
  static tooManyRequests(message = 'Too many requests'): ApiResponse {
    return this.error(message);
  }

  /**
   * Transform any data into a standardized response format
   */
  static transform<T>(
    data: T,
    success = true,
    message?: string,
    meta?: any,
    errors?: any,
  ): ApiResponse<T> {
    return {
      success,
      message: message || (success ? 'Success' : 'Error'),
      data: success ? data : undefined,
      meta,
      errors: success ? undefined : errors,
      timestamp: new Date().toISOString(),
    };
  }
}
