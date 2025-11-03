import { Global, Module } from '@nestjs/common';
import { ConfigService } from '@nestjs/config';
import { DateUtil } from './utils/date.util';
import { ConfigModule } from '@nestjs/config';

// Config loaders
import appConfig from './config/app.config';
import databaseConfig from './config/database.config';
import jwtConfig from './config/jwt.config';
import mailConfig from './config/mail.config';

// Infrastructure modules
import { DatabaseModule } from './database/database.module';

@Global()
@Module({
  imports: [
    ConfigModule.forRoot({
      isGlobal: true,
      envFilePath: '.env',
      load: [appConfig, databaseConfig, jwtConfig, mailConfig],
    }),
    DatabaseModule,
  ],
  exports: [ConfigModule, DatabaseModule],
})
export class CoreModule {
  constructor(private readonly configService: ConfigService) {
    const tz = this.configService.get<string>('app.timezone') || process.env.APP_TIMEZONE || 'Asia/Ho_Chi_Minh';
    DateUtil.setTimezone(tz);
  }
}


