import { Module, MiddlewareConsumer, NestModule } from '@nestjs/common';
import { APP_FILTER, APP_INTERCEPTOR, APP_GUARD } from '@nestjs/core';

// Core Modules
import { CoreModule } from './core/core.module';
import { CommonModule } from './common/common.module';

// Core Services
import { CustomLoggerService } from './core/logger/logger.service';

// Common Filters, Interceptors, Guards
import { HttpExceptionFilter } from './common/filters/http-exception.filter';
import { QueryFailedFilter } from './common/filters/query-failed.filter';
import { TransformInterceptor } from './common/interceptors/transform.interceptor';
import { LoggingInterceptor } from './common/interceptors/logging.interceptor';
import { TimeoutInterceptor } from './common/interceptors/timeout.interceptor';
import { JwtAuthGuard } from './common/guards/jwt-auth.guard';
import { AuthModule } from './modules/auth/auth.module';
import { PublicModule } from './modules/public/public.module';
import { AdminModule } from './modules/admin/admin.module';
import { RbacModule } from './modules/rbac/rbac.module';
import { UserModule } from './modules/user/user/user.module';
import { RbacGuard } from './common/guards/rbac.guard';
import { RequestContextMiddleware } from './common/middlewares/request-context.middleware';

@Module({
  imports: [
    CoreModule,
    CommonModule,
    AuthModule,
    PublicModule,
    AdminModule,
    RbacModule,
    UserModule,
  ],
  controllers: [],
  providers: [
    CustomLoggerService,
    // Global Exception Filters
    {
      provide: APP_FILTER,
      useClass: HttpExceptionFilter,
    },
    {
      provide: APP_FILTER,
      useClass: QueryFailedFilter,
    },
    // Global Interceptors
    {
      provide: APP_INTERCEPTOR,
      useClass: LoggingInterceptor,
    },
    {
      provide: APP_INTERCEPTOR,
      useClass: TransformInterceptor,
    },
    {
      provide: APP_INTERCEPTOR,
      useClass: TimeoutInterceptor,
    },
    // Global Guards
    {
      provide: APP_GUARD,
      useClass: JwtAuthGuard,
    },
    {
      provide: APP_GUARD,
      useClass: RbacGuard,
    },
  ],
})
export class AppModule implements NestModule {
  configure(consumer: MiddlewareConsumer) {
    consumer.apply(RequestContextMiddleware).forRoutes('*');
  }
}

