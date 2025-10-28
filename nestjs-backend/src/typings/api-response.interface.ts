export interface ApiSuccessResponse<T = any> {
  success: true;
  httpStatus: number;
  data: T;
  meta?: any;
  message?: string;
  timestamp?: string;
}

export interface PaginatedResponse<T> {
  items: T[];
  meta: {
    total: number;
    page: number;
    limit: number;
    totalPages: number;
  };
}

export interface ApiErrorResponse {
  success: false;
  httpStatus: number;
  message: string | string[];
  code?: string | number;
  data: null;
  timestamp: string;
  path?: string;
}
