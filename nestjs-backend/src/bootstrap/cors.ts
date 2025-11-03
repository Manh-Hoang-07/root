import { INestApplication } from '@nestjs/common';

export function applyCors(app: INestApplication, options: {
  enabled: boolean;
  origins: string[];
}) {
  if (!options.enabled) return;
  app.enableCors({
    origin: options.origins,
    methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],
    allowedHeaders: ['Content-Type', 'Authorization', 'X-Requested-With'],
    credentials: true,
  });
}


