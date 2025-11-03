import { INestApplication, ValidationPipe } from '@nestjs/common';

export function applyGlobalPipes(app: INestApplication, options: { production: boolean }) {
  app.useGlobalPipes(
    new ValidationPipe({
      whitelist: true,
      transform: true,
      forbidNonWhitelisted: true,
      transformOptions: { enableImplicitConversion: true },
      disableErrorMessages: options.production,
    }),
  );
}


