import { registerAs } from '@nestjs/config';
import { join } from 'path';

export default registerAs('database', () => ({
  type: process.env.DB_TYPE || 'better-sqlite3',
  
  // SQLite Configuration
  database: process.env.DB_DATABASE || 'database/nestjs-database.sqlite',
  
  // MySQL Configuration
  host: process.env.DB_HOST || 'localhost',
  port: parseInt(process.env.DB_PORT, 10) || 3306,
  username: process.env.DB_USERNAME || 'root',
  password: process.env.DB_PASSWORD || '',
  dbName: process.env.DB_DATABASE || 'laravel',
  
  // Common Configuration
  synchronize: process.env.DB_SYNCHRONIZE === 'true',
  logging: process.env.DB_LOGGING === 'true',
  entities: [join(__dirname, '../../shared/entities', '*.entity{.ts,.js}')],
  migrations: [join(__dirname, '../../migrations', '*{.ts,.js}')],
  migrationsRun: true,
}));
