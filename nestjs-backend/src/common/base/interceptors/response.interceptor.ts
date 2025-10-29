import {
  Injectable,
  NestInterceptor,
  ExecutionContext,
  CallHandler,
} from '@nestjs/common';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { ResponseUtil } from '../../../core/utils/response.util';

interface ControllerResponse {
  data?: any;
  message?: string;
  meta?: any;
  code?: string;
  httpStatus?: number;
}

@Injectable()
export class ResponseInterceptor<T> implements NestInterceptor<T, any> {
  intercept(context: ExecutionContext, next: CallHandler): Observable<any> {
    return next.handle().pipe(
      map((data) => {
        // Nếu data đã có format ApiResponse rồi thì trả về nguyên
        if (data && typeof data === 'object' && 'success' in data) {
          return data;
        }

        // Nếu data có format ControllerResponse (có message, meta, etc.)
        if (data && typeof data === 'object' && 'data' in data) {
          const response = data as ControllerResponse;
          
          // Nếu có meta -> paginated response
          if (response.meta) {
            return ResponseUtil.paginated(
              response.data,
              response.meta.page || 1,
              response.meta.limit || 10,
              response.meta.totalItems || 0,
              response.message || 'Thành công',
              response.code,
            );
          }
          
          // Nếu không có meta -> action response
          return ResponseUtil.success(
            response.data,
            response.message || 'Thành công',
            response.code,
            response.httpStatus || 200,
          );
        }

        // Nếu data có PaginatedListResult format (từ service)
        if (data && typeof data === 'object' && 'data' in data && 'meta' in data) {
          return ResponseUtil.paginated(
            data.data,
            data.meta.page || 1,
            data.meta.limit || 10,
            data.meta.totalItems || 0,
            'Thành công',
          );
        }

        // Format thông thường (data trực tiếp)
        return ResponseUtil.success(data, 'Thành công');
      }),
    );
  }
}
