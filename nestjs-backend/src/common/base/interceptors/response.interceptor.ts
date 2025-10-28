import {
  Injectable,
  NestInterceptor,
  ExecutionContext,
  CallHandler,
} from '@nestjs/common';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { ResponseBuilder, ApiResponse, PaginatedApiResponse } from '../interfaces/api-response.interface';

interface ControllerResponse {
  data?: any;
  message?: string;
  meta?: any;
  code?: string;
  httpStatus?: number;
}

@Injectable()
export class ResponseInterceptor<T> implements NestInterceptor<T, ApiResponse<T>> {
  intercept(context: ExecutionContext, next: CallHandler): Observable<ApiResponse<T>> {
    return next.handle().pipe(
      map((data) => {
        // Nếu data đã có format ApiResponse rồi thì trả về nguyên
        if (data && typeof data === 'object' && 'success' in data) {
          return data as ApiResponse<T>;
        }

        // Nếu data có format ControllerResponse (có message, meta, etc.)
        if (data && typeof data === 'object' && 'data' in data) {
          const response = data as ControllerResponse;
          
          // Nếu có meta -> paginated response
          if (response.meta) {
            return ResponseBuilder.paginated(
              response.data,
              response.meta,
              response.message || 'Thành công',
              response.httpStatus || 200,
              response.code,
            ) as PaginatedApiResponse<T>;
          }
          
          // Nếu không có meta -> action response
          return ResponseBuilder.success(
            response.data,
            response.message || 'Thành công',
            response.httpStatus || 200,
            response.code,
          );
        }

        // Nếu data có PaginatedListResult format (từ service)
        if (data && typeof data === 'object' && 'data' in data && 'meta' in data) {
          return ResponseBuilder.paginated(
            data.data,
            data.meta,
            'Thành công',
            200,
          ) as PaginatedApiResponse<T>;
        }

        // Format thông thường (data trực tiếp)
        return ResponseBuilder.success(data, 'Thành công', 200);
      }),
    );
  }
}
