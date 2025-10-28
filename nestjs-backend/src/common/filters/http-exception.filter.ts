import {
  ExceptionFilter,
  Catch,
  ArgumentsHost,
  HttpException,
  HttpStatus,
  Logger,
} from '@nestjs/common';
import { Request, Response } from 'express';
import { ResponseUtil } from '../../core/utils/response.util';

@Catch(HttpException)
export class HttpExceptionFilter implements ExceptionFilter {
  private readonly logger = new Logger(HttpExceptionFilter.name);

  catch(exception: HttpException, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();
    const request = ctx.getRequest<Request>();
    const status = exception.getStatus();
    const exceptionResponse = exception.getResponse();

    // Extract error details
    let message = exception.message;
    let errors: any = null;

    if (typeof exceptionResponse === 'object' && exceptionResponse !== null) {
      const errorObj = exceptionResponse as any;
      message = errorObj.message || message;
      errors = errorObj.errors || errorObj.error || null;
      
      // Handle validation errors from class-validator
      if (Array.isArray(errorObj.message)) {
        message = 'Validation failed';
        errors = errorObj.message;
      }
    }

    // Log the error
    this.logger.error(
      `HTTP Exception: ${status} - ${message}`,
      JSON.stringify({
        path: request.url,
        method: request.method,
        userAgent: request.get('User-Agent'),
        ip: request.ip,
        body: request.body,
        params: request.params,
        query: request.query,
        headers: this.sanitizeHeaders(request.headers),
        timestamp: new Date().toISOString(),
      }),
      exception.stack,
    );

    // Create standardized error response
    const errorResponse = ResponseUtil.error(message, errors);

    response.status(status).json(errorResponse);
  }

  private sanitizeHeaders(headers: any): any {
    const sensitiveHeaders = ['authorization', 'cookie', 'x-api-key'];
    const sanitized = { ...headers };
    
    sensitiveHeaders.forEach(header => {
      if (sanitized[header]) {
        sanitized[header] = '[REDACTED]';
      }
    });
    
    return sanitized;
  }
}
