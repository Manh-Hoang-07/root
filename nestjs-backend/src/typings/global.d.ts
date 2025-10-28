import { AuthUser } from '../common/decorators/user.decorator';

declare global {
  namespace Express {
    interface Request {
      user?: AuthUser;
      requestId?: string;
    }
  }

  namespace NodeJS {
    interface ProcessEnv {
      // App Configuration
      NODE_ENV: 'development' | 'production' | 'test';
      APP_NAME: string;
      APP_VERSION: string;
      APP_URL: string;
      PORT: string;
      GLOBAL_PREFIX: string;
      CORS_ENABLED: string;
      CORS_ORIGINS: string;

      // Database Configuration
      DB_TYPE: 'mysql' | 'postgres' | 'sqlite' | 'mariadb';
      DB_HOST: string;
      DB_PORT: string;
      DB_USERNAME: string;
      DB_PASSWORD: string;
      DB_DATABASE: string;
      DB_SYNCHRONIZE: string;
      DB_LOGGING: string;
      DB_SSL: string;
      DB_CHARSET: string;
      DB_TIMEZONE: string;

      // JWT Configuration
      JWT_SECRET: string;
      JWT_EXPIRES_IN: string;
      JWT_REFRESH_SECRET: string;
      JWT_REFRESH_EXPIRES_IN: string;
      JWT_ISSUER: string;
      JWT_AUDIENCE: string;

      // Mail Configuration
      MAIL_HOST: string;
      MAIL_PORT: string;
      MAIL_SECURE: string;
      MAIL_USERNAME: string;
      MAIL_PASSWORD: string;
      MAIL_FROM_NAME: string;
      MAIL_FROM_ADDRESS: string;
      MAIL_TEMPLATE_DIR: string;
      MAIL_TEMPLATE_ADAPTER: string;

      // Logging Configuration
      LOG_LEVEL: 'error' | 'warn' | 'info' | 'debug' | 'verbose';
      LOG_DIR: string;

      // File Upload Configuration
      UPLOAD_DIR: string;
      MAX_FILE_SIZE: string;
      ALLOWED_FILE_TYPES: string;

      // Redis Configuration (if used)
      REDIS_HOST?: string;
      REDIS_PORT?: string;
      REDIS_PASSWORD?: string;
      REDIS_DB?: string;

      // External API Keys
      STRIPE_SECRET_KEY?: string;
      STRIPE_PUBLISHABLE_KEY?: string;
      PAYPAL_CLIENT_ID?: string;
      PAYPAL_CLIENT_SECRET?: string;
      GOOGLE_CLIENT_ID?: string;
      GOOGLE_CLIENT_SECRET?: string;
      FACEBOOK_APP_ID?: string;
      FACEBOOK_APP_SECRET?: string;

      // Storage Configuration
      STORAGE_TYPE?: 'local' | 's3' | 'gcs';
      AWS_ACCESS_KEY_ID?: string;
      AWS_SECRET_ACCESS_KEY?: string;
      AWS_REGION?: string;
      AWS_S3_BUCKET?: string;

      // Rate Limiting
      RATE_LIMIT_TTL?: string;
      RATE_LIMIT_MAX?: string;

      // Security
      BCRYPT_ROUNDS?: string;
      SESSION_SECRET?: string;
      COOKIE_SECRET?: string;

      // Feature Flags
      ENABLE_SWAGGER?: string;
      ENABLE_METRICS?: string;
      ENABLE_HEALTH_CHECK?: string;
    }
  }
}

export {};
