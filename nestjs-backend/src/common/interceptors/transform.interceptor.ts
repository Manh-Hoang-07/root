import {
  Injectable,
  NestInterceptor,
  ExecutionContext,
  CallHandler,
} from '@nestjs/common';
import { Observable } from 'rxjs';
import { map } from 'rxjs/operators';

export interface StandardSuccessResponse<T = any> {
  success: true;
  httpStatus: number;
  data: T;
  meta?: any;
  message?: string;
  timestamp: string;
}

@Injectable()
export class TransformInterceptor<T>
  implements NestInterceptor<T, StandardSuccessResponse<T>>
{
  intercept(
    context: ExecutionContext,
    next: CallHandler,
  ): Observable<StandardSuccessResponse<T>> {
    return next.handle().pipe(
      map((payload: any) => {
        const res = context.switchToHttp().getResponse();

        // Normalize controller/service return shapes
        const hasDataKey = payload && Object.prototype.hasOwnProperty.call(payload, 'data');
        const hasMetaKey = payload && Object.prototype.hasOwnProperty.call(payload, 'meta');
        const hasMessageKey = payload && Object.prototype.hasOwnProperty.call(payload, 'message');

        const data: any = hasDataKey ? payload.data : payload;
        const meta: any | undefined = hasMetaKey ? payload.meta : undefined;
        const message: string | undefined = hasMessageKey ? payload.message : undefined;

        const response: StandardSuccessResponse<T> = {
          success: true,
          httpStatus: res.statusCode,
          data,
          timestamp: new Date().toISOString(),
        };

        if (meta !== undefined) {
          (response as any).meta = meta;
        }

        // Only include message if explicitly provided by service/controller
        if (message !== undefined && message !== null && message !== '') {
          response.message = message;
        }

        return response;
      }),
    );
  }
}

