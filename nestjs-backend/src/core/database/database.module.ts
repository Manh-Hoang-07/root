import { Module } from '@nestjs/common';
import { TypeOrmModule } from '@nestjs/typeorm';
import { ConfigModule, ConfigService } from '@nestjs/config';
import databaseConfig from '../config/database.config';

@Module({
  imports: [
    TypeOrmModule.forRootAsync({
      imports: [ConfigModule.forFeature(databaseConfig)],
      useFactory: (configService: ConfigService) => {
        const config = configService.get('database');
        
        if (config.type === 'better-sqlite3') {
          return {
            type: 'better-sqlite3',
            database: config.database,
            entities: config.entities,
            synchronize: config.synchronize,
            logging: config.logging,
          };
        }
        
        return {
          type: 'mysql',
          host: config.host,
          port: config.port,
          username: config.username,
          password: config.password,
          database: config.dbName,
          entities: config.entities,
          synchronize: config.synchronize,
          logging: config.logging,
          migrations: config.migrations,
          migrationsRun: config.migrationsRun,
        };
      },
      inject: [ConfigService],
    }),
  ],
})
export class DatabaseModule {}
