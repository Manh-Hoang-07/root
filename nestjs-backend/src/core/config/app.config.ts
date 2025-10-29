import { registerAs } from '@nestjs/config';

export default registerAs('app', () => ({
  name: process.env.APP_NAME || 'NestJS Backend',
  environment: process.env.NODE_ENV || 'development',
  port: parseInt(process.env.PORT, 10) || 3000,
  version: process.env.APP_VERSION || '1.0.0',
  url: process.env.APP_URL || 'http://localhost:3000',
  globalPrefix: process.env.GLOBAL_PREFIX || 'api',
  corsEnabled: process.env.CORS_ENABLED === 'true' || true,
  corsOrigins: process.env.CORS_ORIGINS?.split(',') || ['*'],
  timezone: process.env.APP_TIMEZONE || 'Asia/Ho_Chi_Minh',
}));
