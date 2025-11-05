import {
  Injectable,
  NestInterceptor,
  ExecutionContext,
  CallHandler,
} from '@nestjs/common';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';
import { ResponseUtil, ApiResponse } from '../../common/utils/response.util';

@Injectable()
export class TransformInterceptor<T> implements NestInterceptor<T, ApiResponse<T>> {
  intercept(context: ExecutionContext, next: CallHandler): Observable<ApiResponse<T>> {
    const request = context.switchToHttp().getRequest();
    const response = context.switchToHttp().getResponse();
    
    return next.handle().pipe(
      map((data) => {
        // If data is already in ApiResponse format, return as is
        if (data && typeof data === 'object' && 'success' in data && 'timestamp' in data) {
          return data;
        }

        // Transform data based on HTTP status code
        const statusCode = response.statusCode;
        
        if (statusCode >= 200 && statusCode < 300) {
          // Success responses
          if (statusCode === 201) {
            return ResponseUtil.created(data);
          }
          return ResponseUtil.success(data);
        } else if (statusCode >= 400) {
          // Error responses (though these should be handled by exception filters)
          return ResponseUtil.error('Request failed', 'REQUEST_FAILED', data);
        }

        // Default success response
        return ResponseUtil.success(data);
      }),
    );
  }
}
