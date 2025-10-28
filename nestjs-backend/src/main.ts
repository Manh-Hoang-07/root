import { NestFactory } from '@nestjs/core';
import { ValidationPipe } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { AppModule } from './app.module';
import { HttpExceptionFilter } from './common/filters/http-exception.filter';
import { TransformInterceptor } from './common/interceptors/transform.interceptor';
import { logToFile } from './shared/utils/file-logger.util';

async function bootstrap() {
  const app = await NestFactory.create(AppModule);
  
  const configService = app.get(ConfigService);
  
  // Enable CORS
  app.enableCors({
    origin: '*',
    methods: 'GET, POST, PUT, DELETE, PATCH, OPTIONS',
    allowedHeaders: 'Origin, X-Requested-With, Content-Type, Accept, Authorization, X-XSRF-TOKEN',
    credentials: true,
  });

  // Global prefix
  app.setGlobalPrefix('api');

  // Global validation pipe
  app.useGlobalPipes(
    new ValidationPipe({
      whitelist: true,
      forbidNonWhitelisted: false, // Temporarily disabled to debug route issue
      transform: true,
      transformOptions: {
        enableImplicitConversion: true,
      },
    }),
  );

  // Global filters
  app.useGlobalFilters(new HttpExceptionFilter());

  // Global interceptors
  app.useGlobalInterceptors(new TransformInterceptor());

  // Request logging to file and console
  app.use((req: any, _res: any, next: any) => {
    console.log('========================================');
    console.log(`[REQUEST] ${req.method} ${req.originalUrl}`);
    console.log(`[REQUEST] Path: ${req.path}`);
    console.log(`[REQUEST] Base URL: ${req.baseUrl}`);
    console.log('========================================');
    logToFile(`${req.method} ${req.originalUrl}`);
    next();
  });

  const port = configService.get('app.port') || configService.get('PORT') || 3000;
  
  await app.listen(port);
  
  // Log registered routes using NestJS reflection
  try {
    const server = app.getHttpServer();
    const router = (server as any)._events?.request?._router || server._router;
    
    if (router && router.stack) {
      console.log('\n=== Registered Routes ===');
      const routePaths: string[] = [];
      
      function extractRoutes(layers: any[], parentPath: string = '') {
        layers.forEach((layer: any) => {
          if (layer?.route) {
            const methods = Object.keys(layer.route.methods || {}).join(', ').toUpperCase() || 'ALL';
            const fullPath = parentPath + layer.route.path;
            routePaths.push(`${methods} ${fullPath}`);
            console.log(`${methods} ${fullPath}`);
          } else if (layer.name === 'router' && layer.handle?.stack) {
            // Nested router
            const path = layer.regexp?.source?.replace(/\\/g, '')?.replace(/^\^|\$$/g, '') || '';
            extractRoutes(layer.handle.stack, parentPath + path);
          }
        });
      }
      
      extractRoutes(router.stack);
      console.log(`\nTotal routes found: ${routePaths.length}`);
      console.log('Routes containing "product":', routePaths.filter(r => r.toLowerCase().includes('product')));
      console.log('==========================\n');
    } else {
      console.log('Could not access router object');
    }
  } catch (err: any) {
    console.log('Could not list routes:', err?.message || err);
  }
  
  console.log(`\n=== Application is running on: http://localhost:${port} ===`);
  logToFile(`Server started on port ${port}`);
  console.log('Check console logs above for module initialization messages\n');
}

bootstrap();

