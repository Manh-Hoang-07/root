import 'reflect-metadata';
import { DataSource } from 'typeorm';
import { config } from 'dotenv';
import * as path from 'path';

// Load environment variables (thứ tự quan trọng: file load sau sẽ override file trước)
// Ưu tiên: env.mysql > .env.local > .env
config({ path: path.resolve(process.cwd(), '.env') });
config({ path: path.resolve(process.cwd(), '.env.local') });
config({ path: path.resolve(process.cwd(), 'env.mysql') }); // env.mysql có priority cao nhất

export default new DataSource({
  type: (process.env.DB_TYPE || 'mysql') as any,
  host: process.env.DB_HOST || 'localhost',
  port: parseInt(process.env.DB_PORT || '3306', 10) || 3306,
  username: process.env.DB_USERNAME || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_DATABASE || 'base',
  synchronize: false,
  logging: process.env.DB_LOGGING === 'true' || false,
  entities: [path.join(__dirname, 'src', '**', '*.entity{.ts,.js}')],
  migrations: [path.join(__dirname, 'src', 'core', 'database', 'migrations', '*{.ts,.js}')],
  subscribers: [path.join(__dirname, 'src', 'core', 'database', 'subscribers', '*{.ts,.js}')],
  extra: {
    charset: process.env.DB_CHARSET || 'utf8mb4',
    timezone: process.env.DB_TIMEZONE || '+07:00',
  },
});

