import { Global, Module } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { DateUtil } from './utils/date.util';
import { ConfigModule } from '@nestjs/config';
import * as Joi from 'joi';

// Config loaders
import appConfig from './config/app.config';
import databaseConfig from './config/database.config';
import jwtConfig from './config/jwt.config';
import mailConfig from './config/mail.config';
import { ModuleRef } from '@nestjs/core';

// Infrastructure modules
import { DatabaseModule } from './database/database.module';
import { RedisUtil } from './utils/redis.util';

@Global()
@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: '.env',
      load: [appConfig, databaseConfig, jwtConfig, mailConfig],
      validationSchema: Joi.object({
        // App
        NODE_ENV: Joi.string().valid('development', 'test', 'staging', 'production').default('development'),
        PORT: Joi.number().port().default(3000),
        GLOBAL_PREFIX: Joi.string().default('api'),
        APP_TIMEZONE: Joi.string().default('Asia/Ho_Chi_Minh'),

        // CORS
        CORS_ENABLED: Joi.boolean().truthy('true').falsy('false').default(true),
        CORS_ORIGINS: Joi.alternatives(
          Joi.string().allow(''),
          Joi.array().items(Joi.string())
        ).optional(),

        // JWT (required)
        JWT_SECRET: Joi.string().min(16).required(),
        JWT_EXPIRES_IN: Joi.string().default('1h'),
        JWT_REFRESH_SECRET: Joi.string().min(16).required(),
        JWT_REFRESH_EXPIRES_IN: Joi.string().default('1d'),
        JWT_ISSUER: Joi.string().allow(''),
        JWT_AUDIENCE: Joi.string().allow(''),

        // Database
        DB_TYPE: Joi.string().default('mysql'),
        DB_HOST: Joi.string().hostname().default('localhost'),
        DB_PORT: Joi.number().default(3306),
        DB_USERNAME: Joi.string().required(),
        DB_PASSWORD: Joi.string().allow(''),
        DB_DATABASE: Joi.string().required(),
        DB_SYNCHRONIZE: Joi.boolean().truthy('true').falsy('false').default(false),
        DB_LOGGING: Joi.boolean().truthy('true').falsy('false').default(false),
        DB_SSL: Joi.boolean().truthy('true').falsy('false').default(false),
        DB_CHARSET: Joi.string().default('utf8mb4'),
        DB_TIMEZONE: Joi.string().default('+07:00'),
        DB_CONNECTION_LIMIT: Joi.number().default(10),
        DB_ACQUIRE_TIMEOUT: Joi.number().default(60000),
        DB_TIMEOUT: Joi.number().default(60000),
        DB_RECONNECT: Joi.boolean().truthy('true').falsy('false').default(true),
      }),
    }),
    DatabaseModule,
  ],
  providers: [RedisUtil],
  exports: [ConfigModule, DatabaseModule, RedisUtil],
})
export class CoreModule {
  constructor(private readonly configService: ConfigService) {
    const tz = this.configService.get<string>('app.timezone') || process.env.APP_TIMEZONE || 'Asia/Ho_Chi_Minh';
    DateUtil.setTimezone(tz);
  }
}


