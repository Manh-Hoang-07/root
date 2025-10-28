export interface ApiSuccessResponse<T = any> {
  success: true;
  httpStatus: number;
  data: T;
  meta?: any;
  message?: string;
  timestamp: string;
}

export class ResponseUtil {
  /**
   * Success response
   */
  static success<T>(data: T, message?: string, httpStatus: number = 200): ApiSuccessResponse<T> {
    const resp: ApiSuccessResponse<T> = {
      success: true,
      httpStatus,
      data,
      timestamp: new Date().toISOString(),
    };
    if (message) resp.message = message;
    return resp;
  }

  /**
   * Error response
   */
  static error(
    message: string = 'Error',
    statusCode: number = 400,
    code?: string | number,
  ) {
    return {
      success: false,
      httpStatus: statusCode,
      message,
      code,
      data: null,
      timestamp: new Date().toISOString(),
    } as any;
  }

  /**
   * Paginated response
   */
  static paginated<T>(
    data: T[],
    total: number,
    page: number,
    limit: number,
    message?: string,
  ): ApiSuccessResponse<T[]> {
    const meta = {
      total,
      page,
      limit,
      totalPages: Math.ceil(total / limit),
    };
    const resp: ApiSuccessResponse<T[]> = {
      success: true,
      httpStatus: 200,
      data,
      meta,
      timestamp: new Date().toISOString(),
    };
    if (message) resp.message = message;
    return resp;
  }
}
