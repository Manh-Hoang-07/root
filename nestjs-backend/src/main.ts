import 'reflect-metadata';
import { NestFactory } from '@nestjs/core';
import { ValidationPipe, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { AppModule } from './app.module';
import { CustomLoggerService } from './core/logger/logger.service';
import { applyCors } from './bootstrap/cors';
import { applyHttpHardening } from './bootstrap/http-hardening';
import { applyGlobalPipes } from './bootstrap/pipes';
import { applyRateLimiting } from './bootstrap/rate-limit';
import { registerShutdown } from './bootstrap/shutdown';

async function bootstrap() {
  const app = await NestFactory.create(AppModule, {
    bufferLogs: true,
  });

  // Get configuration service
  const configService = app.get(ConfigService);
  
  // Use custom logger
  const logger = app.get(CustomLoggerService);
  app.useLogger(logger);

  // Get configuration values
  const appConfig = {
    port: configService.get('app.port', 3000),
    globalPrefix: configService.get('app.globalPrefix', 'api'),
    corsEnabled: configService.get('app.corsEnabled', true),
    corsOrigins: configService.get('app.corsOrigins', ['*']),
    environment: configService.get('app.environment', 'development'),
    name: configService.get('app.name', 'NestJS Backend'),
    version: configService.get('app.version', '1.0.0'),
    timezone: configService.get('app.timezone', 'Asia/Ho_Chi_Minh'),
  };

  // Set process timezone (best effort; DB timezone configured separately)
  try {
    process.env.TZ = appConfig.timezone;
    Logger.log(`Timezone set to ${appConfig.timezone}`, 'Application');
  } catch {}

  // Enable CORS if configured
  applyCors(app, { enabled: appConfig.corsEnabled, origins: appConfig.corsOrigins });
  if (appConfig.corsEnabled) {
    logger.log('CORS enabled', { origins: appConfig.corsOrigins });
  }

  // HTTP hardening middlewares
  applyHttpHardening(app, '5mb');

  // Basic rate limiting (in-memory). For production, prefer Redis store.
  applyRateLimiting(app, { points: 100, durationSec: 60 });

  // Suppress all native console outputs globally only in production (use CustomLoggerService instead)
  // Keep console.error and console.warn in development for debugging
  if (appConfig.environment === 'production') {
    try {
      const noop = () => {};
      (console as any).log = noop;
      (console as any).info = noop;
      (console as any).warn = noop;
      (console as any).debug = noop;
      // Keep console.error for critical errors even in production
      // (console as any).error = noop; // Uncomment if you want to suppress all console outputs
    } catch {}
  }

  // Set global prefix
  app.setGlobalPrefix(appConfig.globalPrefix);

  // Global validation pipe with enhanced configuration
  applyGlobalPipes(app, { production: appConfig.environment === 'production' });

  // Graceful shutdown
  app.enableShutdownHooks();

  // Start the application
  await app.listen(appConfig.port);
  
  const appUrl = `http://localhost:${appConfig.port}/${appConfig.globalPrefix}`;
  
  logger.log(`🚀 ${appConfig.name} v${appConfig.version} is running!`, {
    environment: appConfig.environment,
    port: appConfig.port,
    url: appUrl,
    globalPrefix: appConfig.globalPrefix,
    cors: appConfig.corsEnabled,
  });

  // Development-specific logging
  if (appConfig.environment === 'development') {
    console.log(`\n🔗 Application URL: ${appUrl}`);
    console.log(`📖 Environment: ${appConfig.environment}`);
    console.log(`🌐 CORS: ${appConfig.corsEnabled ? 'Enabled' : 'Disabled'}`);
  }

  // Graceful error and signal handling
  registerShutdown(app, logger);
}

// Register process handlers inside bootstrap to allow graceful shutdown
// Note: We keep minimal top-level handlers and attach detailed ones after app starts

bootstrap().catch((error) => {
  console.error('Failed to start application:', error);
  process.exit(1);
});

