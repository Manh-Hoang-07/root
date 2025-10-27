import { Module } from '@nestjs/common';
import { ConfigModule, ConfigService } from '@nestjs/config';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ThrottlerModule } from '@nestjs/throttler';
import { APP_GUARD } from '@nestjs/core';
import { ThrottlerGuard } from '@nestjs/throttler';
import { join } from 'path';

// Zone Modules
import { AdminModule } from './modules/admin/admin.module';
import { UserModule } from './modules/user/user.module';
import { PublicModule } from './modules/public/public.module';

// Auth Module
import { AuthModule } from './modules/auth/auth.module';

// Enum Module
import { EnumModule } from './modules/enum/enum.module';

// File Module
import { FileModule } from './modules/file/file.module';

@Module({
  imports: [
    // Configuration
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: '.env',
    }),

    // TypeORM Configuration
    TypeOrmModule.forRootAsync({
      imports: [ConfigModule],
      useFactory: (configService: ConfigService) => ({
        type: 'mysql',
        host: configService.get<string>('DB_HOST', 'localhost'),
        port: configService.get<number>('DB_PORT', 3306),
        username: configService.get<string>('DB_USERNAME', 'root'),
        password: configService.get<string>('DB_PASSWORD', ''),
        database: configService.get<string>('DB_DATABASE', 'laravel'),
        entities: [join(__dirname, 'entities', '*.entity{.ts,.js}')],
        synchronize: configService.get('DB_SYNCHRONIZE', 'false') === 'true',
        logging: configService.get('DB_LOGGING', 'false') === 'true',
        migrations: [join(__dirname, 'migrations', '*{.ts,.js}')],
        migrationsRun: true,
      }),
      inject: [ConfigService],
    }),

    // Throttler Configuration
    ThrottlerModule.forRoot([
      {
        ttl: 60000,
        limit: 100,
      },
    ]),

    // Zone Modules
    AdminModule,
    UserModule,
    PublicModule,
    
    // Core Modules
    AuthModule,
    EnumModule,
    FileModule,
  ],
  providers: [
    {
      provide: APP_GUARD,
      useClass: ThrottlerGuard,
    },
  ],
})
export class AppModule {}

