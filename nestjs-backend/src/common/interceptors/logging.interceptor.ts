import {
  Injectable,
  NestInterceptor,
  ExecutionContext,
  CallHandler,
} from '@nestjs/common';
import { Observable } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { Request, Response } from 'express';
import { CustomLoggerService } from '../../core/logger/logger.service';
import { Auth } from '../../common/utils/auth.util';

@Injectable()
export class LoggingInterceptor implements NestInterceptor {
  constructor(private readonly logger: CustomLoggerService) {}

  intercept(context: ExecutionContext, next: CallHandler): Observable<any> {
    if (context.getType() !== 'http') {
      return next.handle();
    }

    const request = context.switchToHttp().getRequest<Request>();
    const response = context.switchToHttp().getResponse<Response>();
    const { method, url, body, params, query, headers } = request;
    const userAgent = request.get('User-Agent') || '';
    const ip = request.ip;
    const startTime = Date.now();

    // Generate unique request ID if not present
    const ridHeader = headers['x-request-id'] as unknown;
    const requestId = Array.isArray(ridHeader)
      ? (ridHeader[0] as string)
      : (typeof ridHeader === 'string' && ridHeader.length > 0
          ? ridHeader
          : this.generateRequestId());
    
    // Add request ID to response headers
    response.setHeader('X-Request-ID', requestId);

    const filePathHeader = (headers['x-log-file'] as string) || undefined;
    const user = Auth.user(context);
    const contextBase = {
      context: 'HTTP',
      requestId,
      method,
      url,
      userAgent,
      ip,
      // Account info if framework/auth puts it on request
      userId: Auth.id(context),
      username: user?.username || user?.email || null,
      extra: {
        params: Object.keys(params || {}).length ? params : undefined,
        query: Object.keys(query || {}).length ? query : undefined,
        bodySize: body ? JSON.stringify(body).length : 0,
      },
    } as const;

    return next.handle().pipe(
      tap(() => {
        const duration = Date.now() - startTime;
        const { statusCode } = response;

        this.logger.log(
          `Outgoing Response`,
          {
            ...contextBase,
            extra: {
              ...contextBase.extra,
              statusCode,
              durationMs: duration,
              logDetails: { end: duration },
            },
          },
          filePathHeader ? { filePath: filePathHeader } : undefined,
        );
      }),
      catchError((error) => {
        const duration = Date.now() - startTime;

        this.logger.error(
          `Error Response`,
          error?.stack,
          {
            ...contextBase,
            extra: {
              ...contextBase.extra,
              statusCode: (error as any)?.status || 500,
              durationMs: duration,
              errorMessage: error?.message,
              logDetails: { end: duration },
            },
          },
          filePathHeader ? { filePath: filePathHeader } : undefined,
        );

        throw error;
      }),
    );
  }

  private generateRequestId(): string {
    return `req_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
  }

  // private sanitizeBody(body: any): any {
  //   if (!body || typeof body !== 'object') {
  //     return body;
  //   }

  //   const sensitiveFields = ['password', 'token', 'secret', 'key', 'authorization'];
  //   const sanitized = { ...body };

  //   for (const field of sensitiveFields) {
  //     if (sanitized[field]) {
  //       sanitized[field] = '[REDACTED]';
  //     }
  //   }

  //   return sanitized;
  // }
}
