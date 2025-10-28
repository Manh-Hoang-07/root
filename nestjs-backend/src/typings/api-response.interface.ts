/**
 * Standard API Response Interface
 */
export interface ApiResponse<T = any> {
  success: boolean;
  message: string;
  data?: T;
  meta?: ApiMeta;
  errors?: ApiError[];
  timestamp: string;
}

/**
 * API Metadata Interface (for pagination, etc.)
 */
export interface ApiMeta {
  currentPage?: number;
  itemCount?: number;
  itemsPerPage?: number;
  totalItems?: number;
  totalPages?: number;
  hasNextPage?: boolean;
  hasPreviousPage?: boolean;
  [key: string]: any;
}

/**
 * API Error Interface
 */
export interface ApiError {
  field?: string;
  message: string;
  code?: string;
  value?: any;
}

/**
 * Paginated Response Interface
 */
export interface PaginatedResponse<T> extends ApiResponse<T[]> {
  meta: PaginationMeta;
}

/**
 * Pagination Metadata Interface
 */
export interface PaginationMeta extends ApiMeta {
  currentPage: number;
  itemCount: number;
  itemsPerPage: number;
  totalItems: number;
  totalPages: number;
  hasNextPage: boolean;
  hasPreviousPage: boolean;
}

/**
 * File Upload Response Interface
 */
export interface FileUploadResponse {
  filename: string;
  originalName: string;
  mimetype: string;
  size: number;
  url: string;
  path?: string;
}

/**
 * Bulk Operation Response Interface
 */
export interface BulkOperationResponse {
  total: number;
  successful: number;
  failed: number;
  errors?: ApiError[];
}

/**
 * Search Result Interface
 */
export interface SearchResult<T> {
  items: T[];
  total: number;
  searchTerm: string;
  filters?: Record<string, any>;
}

/**
 * Health Check Response Interface
 */
export interface HealthCheckResponse {
  status: 'ok' | 'error';
  info?: Record<string, any>;
  error?: Record<string, any>;
  details?: Record<string, any>;
}

/**
 * JWT Token Response Interface
 */
export interface TokenResponse {
  accessToken: string;
  refreshToken?: string;
  tokenType: string;
  expiresIn: number;
  user?: any;
}

/**
 * Statistics Interface
 */
export interface Statistics {
  [key: string]: number | string | boolean | Statistics;
}

/**
 * Sort Options Interface
 */
export interface SortOptions {
  field: string;
  direction: 'ASC' | 'DESC';
}

/**
 * Filter Options Interface
 */
export interface FilterOptions {
  field: string;
  operator: 'eq' | 'ne' | 'gt' | 'gte' | 'lt' | 'lte' | 'in' | 'nin' | 'like' | 'between';
  value: any;
}

/**
 * Query Options Interface
 */
export interface QueryOptions {
  page?: number;
  limit?: number;
  sort?: SortOptions[];
  filters?: FilterOptions[];
  search?: string;
  searchFields?: string[];
  relations?: string[];
  select?: string[];
}

/**
 * Entity Audit Interface
 */
export interface EntityAudit {
  id: string;
  createdAt: Date;
  updatedAt: Date;
  deletedAt?: Date;
  createdBy?: string;
  updatedBy?: string;
  deletedBy?: string;
}

/**
 * User Context Interface
 */
export interface UserContext {
  id: string;
  email: string;
  username?: string;
  roles: string[];
  permissions?: string[];
  isActive: boolean;
  lastLoginAt?: Date;
  [key: string]: any;
}

/**
 * Request Context Interface
 */
export interface RequestContext {
  user?: UserContext;
  requestId: string;
  ip: string;
  userAgent: string;
  timestamp: Date;
  route: string;
  method: string;
}

/**
 * Cache Interface
 */
export interface CacheOptions {
  key: string;
  ttl?: number; // Time to live in seconds
  tags?: string[];
}

/**
 * Email Template Interface
 */
export interface EmailTemplate {
  to: string | string[];
  subject: string;
  template: string;
  context: Record<string, any>;
  from?: string;
  cc?: string | string[];
  bcc?: string | string[];
  attachments?: EmailAttachment[];
}

/**
 * Email Attachment Interface
 */
export interface EmailAttachment {
  filename: string;
  path?: string;
  content?: Buffer | string;
  contentType?: string;
}

/**
 * Notification Interface
 */
export interface Notification {
  id: string;
  type: 'email' | 'sms' | 'push' | 'in-app';
  recipient: string;
  title: string;
  content: string;
  data?: Record<string, any>;
  scheduledAt?: Date;
  sentAt?: Date;
  status: 'pending' | 'sent' | 'failed' | 'cancelled';
}

/**
 * Configuration Interface
 */
export interface AppConfig {
  app: {
    name: string;
    environment: string;
    port: number;
    url: string;
    version: string;
    globalPrefix: string;
  };
  database: {
    type: string;
    host: string;
    port: number;
    username: string;
    password: string;
    database: string;
    synchronize: boolean;
    logging: boolean;
  };
  jwt: {
    secret: string;
    expiresIn: string;
    refreshSecret: string;
    refreshExpiresIn: string;
  };
  mail: {
    host: string;
    port: number;
    secure: boolean;
    auth: {
      user: string;
      pass: string;
    };
    from: {
      name: string;
      address: string;
    };
  };
}

/**
 * Generic Repository Interface
 */
export interface IRepository<T> {
  create(entity: Partial<T>): Promise<T>;
  findAll(): Promise<T[]>;
  findById(id: string): Promise<T | null>;
  update(id: string, updates: Partial<T>): Promise<T>;
  delete(id: string): Promise<void>;
  count(): Promise<number>;
}

/**
 * Generic Service Interface
 */
export interface IService<T, CreateDto, UpdateDto> {
  create(createDto: CreateDto): Promise<T>;
  findAll(): Promise<T[]>;
  findById(id: string): Promise<T>;
  update(id: string, updateDto: UpdateDto): Promise<T>;
  delete(id: string): Promise<void>;
}
