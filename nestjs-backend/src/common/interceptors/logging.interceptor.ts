import {
  Injectable,
  NestInterceptor,
  ExecutionContext,
  CallHandler,
  Logger,
} from '@nestjs/common';
import { Observable } from 'rxjs';
import { tap, catchError } from 'rxjs/operators';
import { Request, Response } from 'express';

@Injectable()
export class LoggingInterceptor implements NestInterceptor {
  private readonly logger = new Logger(LoggingInterceptor.name);

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
    const requestId = headers['x-request-id'] || this.generateRequestId();
    
    // Add request ID to response headers
    response.setHeader('X-Request-ID', requestId);

    // // Log incoming request
    // this.logger.log(
    //   `Incoming Request: ${method} ${url}`,
    //   JSON.stringify({
    //     requestId,
    //     method,
    //     url,
    //     userAgent,
    //     ip,
    //     params: Object.keys(params).length ? params : undefined,
    //     query: Object.keys(query).length ? query : undefined,
    //     body: this.sanitizeBody(body),
    //     timestamp: new Date().toISOString(),
    //   }),
    // );

    return next.handle().pipe(
      tap(() => {
        const duration = Date.now() - startTime;
        const { statusCode } = response;
        
        // Log successful response
        this.logger.log(
          `Outgoing Response: ${method} ${url} - ${statusCode} - ${duration}ms`,
          JSON.stringify({
            requestId,
            method,
            url,
            statusCode,
            duration: `${duration}ms`,
            timestamp: new Date().toISOString(),
          }),
        );
      }),
      catchError((error) => {
        const duration = Date.now() - startTime;
        
        // Log error response
        this.logger.error(
          `Error Response: ${method} ${url} - ${error.status || 500} - ${duration}ms`,
          JSON.stringify({
            requestId,
            method,
            url,
            statusCode: error.status || 500,
            duration: `${duration}ms`,
            error: error.message,
            timestamp: new Date().toISOString(),
          }),
          error.stack,
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
