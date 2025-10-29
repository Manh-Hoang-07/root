import { HttpStatus } from '@nestjs/common';

export interface ApiResponse<T = any> {
  data: T;
  message: string;
  code: string;
  httpStatus?: HttpStatus;
}

export interface PaginatedMeta {
  page: number;
  limit: number;
  total: number;
  totalPages?: number;
}

export interface PaginatedResponse<T = any> extends ApiResponse<T[]> {
  meta: PaginatedMeta;
}

export class ResponseUtil {
  /**
   * Tạo response thành công với data
   */
  static success<T>(data: T, message: string = 'Thành công', code: string = 'SUCCESS'): ApiResponse<T> {
    return {
      data,
      message,
      code,
    };
  }

  /**
   * Tạo response thành công với pagination
   */
  static paginated<T>(
    data: T[],
    meta: PaginatedMeta,
    message: string = 'Lấy danh sách thành công',
    code: string = 'SUCCESS',
  ): PaginatedResponse<T> {
    return {
      data,
      meta,
      message,
      code,
    };
  }

  /**
   * Tạo response thành công với HTTP status cụ thể
   */
  static created<T>(data: T, message: string = 'Tạo mới thành công'): ApiResponse<T> {
    return {
      data,
      message,
      code: 'CREATED',
      httpStatus: HttpStatus.CREATED,
    };
  }

  /**
   * Tạo response cập nhật thành công
   */
  static updated<T>(data: T, message: string = 'Cập nhật thành công'): ApiResponse<T> {
    return {
      data,
      message,
      code: 'UPDATED',
    };
  }

  /**
   * Tạo response xóa thành công
   */
  static deleted(message: string = 'Xóa thành công'): ApiResponse<null> {
    return {
      data: null,
      message,
      code: 'DELETED',
    };
  }

  /**
   * Tạo response khôi phục thành công
   */
  static restored<T>(data: T, message: string = 'Khôi phục thành công'): ApiResponse<T> {
    return {
      data,
      message,
      code: 'RESTORED',
    };
  }

  /**
   * Tạo response lỗi validation
   */
  static invalidQuery(message: string = 'Tham số không hợp lệ'): PaginatedResponse {
    return {
      data: [],
      meta: { page: 1, limit: 0, total: 0 },
      message,
      code: 'INVALID_QUERY',
    };
  }

  /**
   * Tạo response không tìm thấy
   */
  static notFound(message: string = 'Không tìm thấy dữ liệu'): ApiResponse<null> {
    return {
      data: null,
      message,
      code: 'NOT_FOUND',
    };
  }

  /**
   * Tạo response lỗi server
   */
  static error(message: string = 'Có lỗi xảy ra', code: string = 'ERROR'): ApiResponse<null> {
    return {
      data: null,
      message,
      code,
    };
  }
}
