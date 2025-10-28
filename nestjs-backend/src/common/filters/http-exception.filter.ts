import {
  ExceptionFilter,
  Catch,
  ArgumentsHost,
  HttpException,
  HttpStatus,
  Logger,
} from '@nestjs/common';
import { Request, Response } from 'express';

@Catch()
export class HttpExceptionFilter implements ExceptionFilter {
  private readonly logger = new Logger(HttpExceptionFilter.name);

  catch(exception: unknown, host: ArgumentsHost) {
    const ctx = host.switchToHttp();
    const response = ctx.getResponse<Response>();
    const request = ctx.getRequest<Request>();

    const status =
      exception instanceof HttpException
        ? exception.getStatus()
        : HttpStatus.INTERNAL_SERVER_ERROR;

    const errorBody =
      exception instanceof HttpException ? exception.getResponse() : undefined;

    // Extract message and optional code from Nest/Validation/Custom exceptions
    let message: string | string[] = 'Internal server error';
    let code: string | number | undefined = undefined;
    if (typeof errorBody === 'string') {
      message = errorBody;
    } else if (errorBody && typeof errorBody === 'object') {
      const m = (errorBody as any).message;
      const c = (errorBody as any).code ?? (errorBody as any).errorCode;
      message = m ?? 'Internal server error';
      code = c;
    }

    const errorResponse = {
      success: false,
      httpStatus: status,
      message,
      code,
      data: null,
      timestamp: new Date().toISOString(),
      path: request.url,
      method: request.method,
    } as any;

    this.logger.error(
      `${request.method} ${request.url}`,
      JSON.stringify(errorResponse),
    );

    response.status(status).json(errorResponse);
  }
}

