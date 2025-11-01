import 'reflect-metadata';
import { DataSource } from 'typeorm';
import { config } from 'dotenv';
import * as path from 'path';
import * as mysql from 'mysql2/promise';

// Load environment variables
config({ path: path.resolve(process.cwd(), '.env') });
config({ path: path.resolve(process.cwd(), '.env.local') });
config({ path: path.resolve(process.cwd(), 'env.mysql') });

async function createDatabaseIfNotExists() {
  const dbName = process.env.DB_DATABASE || 'nestjs';
  const dbHost = process.env.DB_HOST || 'localhost';
  const dbPort = parseInt(process.env.DB_PORT || '3306', 10);
  const dbUsername = process.env.DB_USERNAME || 'root';
  const dbPassword = process.env.DB_PASSWORD || '';

  console.log(`📦 Đang tạo database "${dbName}"...`);

  try {
    // Kết nối MySQL mà không chỉ định database
    const connection = await mysql.createConnection({
      host: dbHost,
      port: dbPort,
      user: dbUsername,
      password: dbPassword,
    });

    // Tạo database nếu chưa tồn tại
    await connection.query(
      `CREATE DATABASE IF NOT EXISTS \`${dbName}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci`,
    );

    await connection.end();

    console.log(`✅ Database "${dbName}" đã sẵn sàng!`);
    process.exit(0);
  } catch (error: any) {
    console.error(`❌ Lỗi khi tạo database:`, error.message);
    console.error('\n💡 Bạn có thể tạo database thủ công:');
    console.error(`   CREATE DATABASE ${dbName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;`);
    process.exit(1);
  }
}

createDatabaseIfNotExists();

