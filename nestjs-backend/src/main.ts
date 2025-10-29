import 'reflect-metadata';
import { NestFactory } from '@nestjs/core';
import { ValidationPipe, Logger } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { AppModule } from './app.module';
import { CustomLoggerService } from './core/logger/logger.service';

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
  if (appConfig.corsEnabled) {
    app.enableCors({
      origin: appConfig.corsOrigins,
      methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
      allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'],
      credentials: true,
    });
    logger.log('CORS enabled', { origins: appConfig.corsOrigins });
  }

  // Set global prefix
  app.setGlobalPrefix(appConfig.globalPrefix);

  // Global validation pipe with enhanced configuration
  app.useGlobalPipes(
    new ValidationPipe({
      whitelist: true,
      transform: true,
      forbidNonWhitelisted: true,
      transformOptions: {
        enableImplicitConversion: true,
      },
      disableErrorMessages: appConfig.environment === 'production',
    }),
  );

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
}

// Handle unhandled promise rejections
process.on('unhandledRejection', (reason: unknown, promise: Promise<unknown>) => {
  console.error('Unhandled Rejection at:', promise, 'reason:', reason);
  process.exit(1);
});

// Handle uncaught exceptions
process.on('uncaughtException', (error: Error) => {
  console.error('Uncaught Exception:', error);
  process.exit(1);
});

// Handle SIGTERM for graceful shutdown
process.on('SIGTERM', () => {
  console.log('SIGTERM received, shutting down gracefully');
  process.exit(0);
});

// Handle SIGINT for graceful shutdown
process.on('SIGINT', () => {
  console.log('SIGINT received, shutting down gracefully');
  process.exit(0);
});

bootstrap().catch((error) => {
  console.error('Failed to start application:', error);
  process.exit(1);
});

