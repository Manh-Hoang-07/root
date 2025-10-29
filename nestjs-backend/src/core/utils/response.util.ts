export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  code?: string;
  httpStatus?: number;
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
  static success<T>(data?: T, message = 'Success', code = 'SUCCESS', httpStatus = 200, meta?: any): ApiResponse<T> {
    return {
      success: true,
      message,
      code,
      httpStatus,
      data,
      meta,
      timestamp: new Date().toISOString(),
    };
  }

  /**
   * Create an error response
   */
  static error(message = 'Error', code = 'ERROR', httpStatus = 400, errors?: any): ApiResponse {
    return {
      success: false,
      message,
      code,
      httpStatus,
      data: null,
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
    code = 'SUCCESS',
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

    return this.success(data, message, code, 200, meta);
  }

  /**
   * Create a created response (201)
   */
  static created<T>(data?: T, message = 'Created'): ApiResponse<T> {
    return this.success(data, message, 'CREATED', 201);
  }

  /**
   * Create an updated response
   */
  static updated<T>(data?: T, message = 'Updated'): ApiResponse<T> {
    return this.success(data, message, 'UPDATED');
  }

  /**
   * Create a deleted response
   */
  static deleted(message = 'Deleted'): ApiResponse {
    return this.success(null, message, 'DELETED');
  }

  /**
   * Create a not found response
   */
  static notFound(message = 'Not found'): ApiResponse {
    return this.error(message, 'NOT_FOUND', 404);
  }

  /**
   * Create a validation error response
   */
  static validationError(errors: any, message = 'Validation failed'): ApiResponse {
    return this.error(message, 'VALIDATION_ERROR', 400, errors);
  }

  /**
   * Create a forbidden response
   */
  static forbidden(message = 'Forbidden'): ApiResponse {
    return this.error(message, 'FORBIDDEN', 403);
  }

  /**
   * Create an unauthorized response
   */
  static unauthorized(message = 'Unauthorized'): ApiResponse {
    return this.error(message, 'UNAUTHORIZED', 401);
  }

  /**
   * Create a bad request response
   */
  static badRequest(message = 'Bad request', errors?: any): ApiResponse {
    return this.error(message, 'BAD_REQUEST', 400, errors);
  }

  /**
   * Create an internal server error response
   */
  static internalServerError(message = 'Internal server error'): ApiResponse {
    return this.error(message, 'INTERNAL_SERVER_ERROR', 500);
  }

  /**
   * Create a conflict response
   */
  static conflict(message = 'Conflict'): ApiResponse {
    return this.error(message, 'CONFLICT', 409);
  }

  /**
   * Create a too many requests response
   */
  static tooManyRequests(message = 'Too many requests'): ApiResponse {
    return this.error(message, 'TOO_MANY_REQUESTS', 429);
  }

  /**
   * Create an invalid query response for pagination errors
   */
  static invalidQuery(message = 'Invalid query parameters'): ApiResponse {
    return this.error(message, 'INVALID_QUERY', 400);
  }

  /**
   * Transform any data into a standardized response format
   */
  static transform<T>(
    data: T,
    success = true,
    message?: string,
    code?: string,
    httpStatus?: number,
    meta?: any,
    errors?: any,
  ): ApiResponse<T> {
    return {
      success,
      message: message || (success ? 'Success' : 'Error'),
      code: code || (success ? 'SUCCESS' : 'ERROR'),
      httpStatus: httpStatus || (success ? 200 : 400),
      data: success ? data : undefined,
      meta,
      errors: success ? undefined : errors,
      timestamp: new Date().toISOString(),
    };
  }
}
