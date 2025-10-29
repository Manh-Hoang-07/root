import { HttpStatus } from '@nestjs/common';

export interface ApiResponse<T = any> {
  data: T;
  message: string;
  code: string;
  httpStatus?: HttpStatus;
  success?: boolean;
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
      httpStatus: HttpStatus.OK,
      success: true,
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
      httpStatus: HttpStatus.OK,
      success: true,
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
      success: true,
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
      httpStatus: HttpStatus.OK,
      success: true,
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
      httpStatus: HttpStatus.OK,
      success: true,
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
      httpStatus: HttpStatus.OK,
      success: true,
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
      httpStatus: HttpStatus.BAD_REQUEST,
      success: false,
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
      httpStatus: HttpStatus.NOT_FOUND,
      success: false,
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
      httpStatus: HttpStatus.INTERNAL_SERVER_ERROR,
      success: false,
    };
  }
}
