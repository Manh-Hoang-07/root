export interface ApiResponse<T = any> {
  statusCode: number;
  message: string;
  data: T;
  timestamp: string;
}

export class ResponseUtil {
  /**
   * Success response
   */
  static success<T>(data: T, message: string = 'Success'): ApiResponse<T> {
    return {
      statusCode: 200,
      message,
      data,
      timestamp: new Date().toISOString(),
    };
  }

  /**
   * Error response
   */
  static error(
    message: string = 'Error',
    statusCode: number = 400,
  ): ApiResponse<null> {
    return {
      statusCode,
      message,
      data: null,
      timestamp: new Date().toISOString(),
    };
  }

  /**
   * Paginated response
   */
  static paginated<T>(
    data: T[],
    total: number,
    page: number,
    limit: number,
    message: string = 'Success',
  ): ApiResponse<{
    items: T[];
    meta: {
      total: number;
      page: number;
      limit: number;
      totalPages: number;
    };
  }> {
    return {
      statusCode: 200,
      message,
      data: {
        items: data,
        meta: {
          total,
          page,
          limit,
          totalPages: Math.ceil(total / limit),
        },
      },
      timestamp: new Date().toISOString(),
    };
  }
}
