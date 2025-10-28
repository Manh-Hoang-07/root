import { ApiProperty } from '@nestjs/swagger';

export class BaseResponseDto {
  @ApiProperty({
    description: 'Indicates if the request was successful',
    example: true,
  })
  success: boolean;

  @ApiProperty({
    description: 'Response message',
    example: 'Operation completed successfully',
  })
  message: string;

  @ApiProperty({
    description: 'Timestamp of the response',
    example: '2023-12-01T10:00:00.000Z',
  })
  timestamp: string;

  constructor(success: boolean, message: string) {
    this.success = success;
    this.message = message;
    this.timestamp = new Date().toISOString();
  }
}

export class SuccessResponseDto<T> extends BaseResponseDto {
  @ApiProperty({
    description: 'Response data',
  })
  data?: T;

  @ApiProperty({
    description: 'Additional metadata',
    required: false,
  })
  meta?: any;

  constructor(data?: T, message: string = 'Success', meta?: any) {
    super(true, message);
    this.data = data;
    this.meta = meta;
  }
}

export class ErrorResponseDto extends BaseResponseDto {
  @ApiProperty({
    description: 'Error details',
    required: false,
  })
  errors?: any;

  @ApiProperty({
    description: 'Error code',
    required: false,
    example: 'VALIDATION_ERROR',
  })
  errorCode?: string;

  constructor(message: string, errors?: any, errorCode?: string) {
    super(false, message);
    this.errors = errors;
    this.errorCode = errorCode;
  }
}

export class ValidationErrorResponseDto extends ErrorResponseDto {
  constructor(errors: any) {
    super('Validation failed', errors, 'VALIDATION_ERROR');
  }
}

export class NotFoundResponseDto extends ErrorResponseDto {
  constructor(resource: string = 'Resource') {
    super(`${resource} not found`, null, 'NOT_FOUND');
  }
}

export class UnauthorizedResponseDto extends ErrorResponseDto {
  constructor(message: string = 'Unauthorized') {
    super(message, null, 'UNAUTHORIZED');
  }
}

export class ForbiddenResponseDto extends ErrorResponseDto {
  constructor(message: string = 'Forbidden') {
    super(message, null, 'FORBIDDEN');
  }
}

export class ConflictResponseDto extends ErrorResponseDto {
  constructor(message: string = 'Conflict') {
    super(message, null, 'CONFLICT');
  }
}

export class InternalServerErrorResponseDto extends ErrorResponseDto {
  constructor(message: string = 'Internal server error') {
    super(message, null, 'INTERNAL_SERVER_ERROR');
  }
}

export class BadRequestResponseDto extends ErrorResponseDto {
  constructor(message: string = 'Bad request', errors?: any) {
    super(message, errors, 'BAD_REQUEST');
  }
}

export class TooManyRequestsResponseDto extends ErrorResponseDto {
  constructor(message: string = 'Too many requests') {
    super(message, null, 'TOO_MANY_REQUESTS');
  }
}

/**
 * Paginated response DTO
 */
export class PaginatedResponseDto<T> extends SuccessResponseDto<T[]> {
  @ApiProperty({
    description: 'Pagination metadata',
  })
  meta: {
    currentPage: number;
    itemCount: number;
    itemsPerPage: number;
    totalItems: number;
    totalPages: number;
    hasNextPage: boolean;
    hasPreviousPage: boolean;
  };

  constructor(
    data: T[],
    currentPage: number,
    itemsPerPage: number,
    totalItems: number,
    message: string = 'Success'
  ) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const hasNextPage = currentPage < totalPages;
    const hasPreviousPage = currentPage > 1;

    const meta = {
      currentPage,
      itemCount: data.length,
      itemsPerPage,
      totalItems,
      totalPages,
      hasNextPage,
      hasPreviousPage,
    };

    super(data, message, meta);
  }
}

/**
 * Created response DTO
 */
export class CreatedResponseDto<T> extends SuccessResponseDto<T> {
  constructor(data: T, message: string = 'Created successfully') {
    super(data, message);
  }
}

/**
 * Updated response DTO
 */
export class UpdatedResponseDto<T> extends SuccessResponseDto<T> {
  constructor(data: T, message: string = 'Updated successfully') {
    super(data, message);
  }
}

/**
 * Deleted response DTO
 */
export class DeletedResponseDto extends SuccessResponseDto<null> {
  constructor(message: string = 'Deleted successfully') {
    super(null, message);
  }
}

/**
 * Upload response DTO
 */
export class UploadResponseDto extends SuccessResponseDto<{
  filename: string;
  originalName: string;
  mimetype: string;
  size: number;
  url: string;
}> {
  constructor(fileInfo: {
    filename: string;
    originalName: string;
    mimetype: string;
    size: number;
    url: string;
  }) {
    super(fileInfo, 'File uploaded successfully');
  }
}

/**
 * Bulk operation response DTO
 */
export class BulkOperationResponseDto extends SuccessResponseDto<{
  total: number;
  successful: number;
  failed: number;
  errors?: any[];
}> {
  constructor(
    total: number,
    successful: number,
    failed: number,
    errors?: any[],
    message?: string
  ) {
    const defaultMessage = failed > 0 
      ? `Bulk operation completed with ${failed} failures`
      : 'Bulk operation completed successfully';
    
    super(
      { total, successful, failed, errors },
      message || defaultMessage
    );
  }
}

/**
 * Health check response DTO
 */
export class HealthCheckResponseDto extends SuccessResponseDto<{
  status: 'ok' | 'error';
  info?: Record<string, any>;
  error?: Record<string, any>;
  details?: Record<string, any>;
}> {
  constructor(health: {
    status: 'ok' | 'error';
    info?: Record<string, any>;
    error?: Record<string, any>;
    details?: Record<string, any>;
  }) {
    super(health, health.status === 'ok' ? 'Health check passed' : 'Health check failed');
  }
}

/**
 * Statistics response DTO
 */
export class StatisticsResponseDto<T> extends SuccessResponseDto<T> {
  constructor(stats: T, message: string = 'Statistics retrieved successfully') {
    super(stats, message);
  }
}

/**
 * Response utility class
 */
export class ResponseBuilder {
  static success<T>(data?: T, message: string = 'Success', meta?: any): SuccessResponseDto<T> {
    return new SuccessResponseDto(data, message, meta);
  }

  static error(message: string, errors?: any, errorCode?: string): ErrorResponseDto {
    return new ErrorResponseDto(message, errors, errorCode);
  }

  static created<T>(data: T, message?: string): CreatedResponseDto<T> {
    return new CreatedResponseDto(data, message);
  }

  static updated<T>(data: T, message?: string): UpdatedResponseDto<T> {
    return new UpdatedResponseDto(data, message);
  }

  static deleted(message?: string): DeletedResponseDto {
    return new DeletedResponseDto(message);
  }

  static paginated<T>(
    data: T[],
    page: number,
    limit: number,
    total: number,
    message?: string
  ): PaginatedResponseDto<T> {
    return new PaginatedResponseDto(data, page, limit, total, message);
  }

  static notFound(resource?: string): NotFoundResponseDto {
    return new NotFoundResponseDto(resource);
  }

  static unauthorized(message?: string): UnauthorizedResponseDto {
    return new UnauthorizedResponseDto(message);
  }

  static forbidden(message?: string): ForbiddenResponseDto {
    return new ForbiddenResponseDto(message);
  }

  static conflict(message?: string): ConflictResponseDto {
    return new ConflictResponseDto(message);
  }

  static badRequest(message?: string, errors?: any): BadRequestResponseDto {
    return new BadRequestResponseDto(message, errors);
  }

  static validation(errors: any): ValidationErrorResponseDto {
    return new ValidationErrorResponseDto(errors);
  }

  static internalServerError(message?: string): InternalServerErrorResponseDto {
    return new InternalServerErrorResponseDto(message);
  }
}
