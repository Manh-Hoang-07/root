# Hướng Dẫn Database Migrations và Seeding

Tài liệu này hướng dẫn cách sử dụng Database Migrations và Seeding trong dự án NestJS Backend.

## Mục Lục

- [Tổng Quan](#tổng-quan)
- [Database Migrations](#database-migrations)
  - [Chạy Migrations](#chạy-migrations)
  - [Xem trạng thái Migrations](#xem-trạng-thái-migrations)
  - [Revert Migrations](#revert-migrations)
  - [Tạo Migration mới](#tạo-migration-mới)
- [Database Seeding](#database-seeding)
  - [Chạy Seeder](#chạy-seeder)
  - [Cấu trúc Seeder](#cấu-trúc-seeder)
- [Quy Trình Thiết Lập Database](#quy-trình-thiết-lập-database)
- [Troubleshooting](#troubleshooting)

---

## Tổng Quan

Dự án sử dụng **TypeORM** để quản lý database với hai công cụ chính:

1. **Migrations**: Quản lý schema database (tạo/sửa/xóa bảng, cột, indexes...)
2. **Seeders**: Tạo dữ liệu mẫu cho database (users, roles, permissions, categories...)

### Cấu Trúc Thư Mục

```
src/core/database/
├── migrations/          # Các file migration
│   ├── 1737000000000-CreateUsersTable.ts
│   ├── 1737000000100-CreateProfilesTable.ts
│   ├── 1737000001000-CreatePermissionsTable.ts
│   └── ...
├── seeder/             # Các file seeder
│   ├── seed-data.ts    # SeedService - điều phối tất cả seeders
│   ├── seed-users.ts
│   ├── seed-roles.ts
│   ├── seed-permissions.ts
│   └── ...
└── cli/
    └── seed.ts         # CLI script để chạy seeder
```

---

## Database Migrations

### Chạy Migrations

#### 1. Chạy tất cả migrations chưa được áp dụng

```bash
npm run migration:run
```

Lệnh này sẽ:
- Kết nối database theo config trong `.env`
- Kiểm tra các migrations đã chạy (bảng `migrations`)
- Chạy các migrations chưa được áp dụng theo thứ tự timestamp

**Ví dụ output:**
```
Migration 1737000000000-CreateUsersTable has been executed successfully.
Migration 1737000000100-CreateProfilesTable has been executed successfully.
Migration 1737000001000-CreatePermissionsTable has been executed successfully.
...
```

#### 2. Xem trạng thái migrations

```bash
npm run migration:show
```

Hiển thị danh sách tất cả migrations và trạng thái của chúng:
- ✅ Đã chạy
- ⏳ Chưa chạy

### Revert Migrations

#### Revert migration cuối cùng

```bash
npm run migration:revert
```

Lệnh này sẽ:
- Revert migration cuối cùng đã chạy
- Hoàn tác các thay đổi schema do migration đó tạo ra

⚠️ **Lưu ý**: Chỉ revert được migration cuối cùng. Nếu muốn revert nhiều migrations, cần chạy lệnh nhiều lần.

### Tạo Migration Mới

#### 1. Tạo migration từ thay đổi entities (Recommended)

```bash
npm run migration:generate -- src/core/database/migrations/TenMigration
```

Lệnh này sẽ:
- So sánh entities hiện tại với database schema
- Tự động generate code migration dựa trên sự khác biệt

**Ví dụ:**
```bash
npm run migration:generate -- src/core/database/migrations/AddEmailColumnToUsers
```

#### 2. Tạo file migration trống

```bash
npm run migration:create -- src/core/database/migrations/TenMigration
```

Tạo file migration trống để bạn tự viết code. File sẽ có format:
```
1737000000000-TenMigration.ts
```

**Format tên file migration:**
- Phải bắt đầu bằng timestamp (13 chữ số)
- Format: `YYYYMMDDHHMMSS-NameOfMigration.ts`
- Ví dụ: `1737000000000-CreateUsersTable.ts`

#### Ví dụ Migration File

```typescript
import { MigrationInterface, QueryRunner } from 'typeorm';

export class CreateUsersTable1737000000000 implements MigrationInterface {
  name = 'CreateUsersTable1737000000000'

  public async up(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(`
      CREATE TABLE \`users\` (
        \`id\` INT UNSIGNED NOT NULL AUTO_INCREMENT,
        \`username\` VARCHAR(255) NOT NULL,
        \`email\` VARCHAR(255) NOT NULL,
        PRIMARY KEY (\`id\`)
      ) ENGINE=InnoDB
    `);
  }

  public async down(queryRunner: QueryRunner): Promise<void> {
    await queryRunner.query(`DROP TABLE \`users\``);
  }
}
```

---

## Database Seeding

### Chạy Seeder

```bash
npm run seed
```

Lệnh này sẽ:
- Khởi tạo NestJS application context
- Chạy `SeedService.seedAll()`
- Seed dữ liệu theo thứ tự:
  1. Permissions
  2. Roles
  3. Users
  4. Post Categories
  5. Post Tags

**Ví dụ output:**
```
🚀 Starting database seeding...
[Nest] SeedService - Starting database seeding...
[Nest] SeedPermissions - Seeding permissions...
[Nest] SeedPermissions - Permissions seeding completed
[Nest] SeedRoles - Seeding roles...
[Nest] SeedRoles - Roles seeding completed
[Nest] SeedUsers - Seeding users...
[Nest] SeedUsers - Created user: admin
[Nest] SeedUsers - Created user: moderator
[Nest] SeedUsers - Created user: user
[Nest] SeedUsers - Users seeding completed
✅ Database seeding completed successfully!
```

### Cấu Trúc Seeder

#### SeedService (`seed-data.ts`)

Điều phối việc seed tất cả dữ liệu:

```typescript
async seedAll(): Promise<void> {
  await this.seedPermissions.seed();
  await this.seedRoles.seed();
  await this.seedUsers.seed();
  await this.seedPostCategories.seed();
  await this.seedPostTags.seed();
}
```

#### Individual Seeders

Mỗi seeder có hai methods:
- `seed()`: Tạo dữ liệu
- `clear()`: Xóa dữ liệu

**Đặc điểm:**
- ✅ **Idempotent**: Kiểm tra dữ liệu đã tồn tại trước khi seed
- ✅ **Safe**: Không seed lại nếu dữ liệu đã có
- ✅ **Ordered**: Seed theo thứ tự phụ thuộc

#### Dữ Liệu Mẫu Được Tạo

1. **Permissions**: Các quyền hệ thống (create, read, update, delete...)
2. **Roles**: 
   - Admin
   - Moderator
   - User
3. **Users**:
   - admin (email: admin@example.com, password: password123)
   - moderator (email: moderator@example.com, password: password123)
   - user (email: user@example.com, password: password123)
4. **Post Categories**: Các danh mục bài viết
5. **Post Tags**: Các tag bài viết

---

## Quy Trình Thiết Lập Database

### Lần Đầu Setup (Database Mới Hoàn Toàn)

Nếu bạn có database mới hoàn toàn (chưa có bảng nào), làm theo các bước sau:

1. **Cấu hình môi trường**
   ```bash
   # Copy file .env.example thành .env
   cp .env.example .env
   ```

2. **Cấu hình database trong `.env`**
   ```env
   DB_TYPE=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_USERNAME=root
   DB_PASSWORD=your_password
   DB_DATABASE=your_database_name
   DB_CHARSET=utf8mb4
   DB_TIMEZONE=+07:00
   DB_LOGGING=true
   ```

3. **Tạo database** (nếu chưa có)
   ```sql
   CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

4. **Chạy migrations để tạo bảng**
   ```bash
   npm run migration:run
   ```
   
   **Quá trình này sẽ:**
   - Migration `CreateMigrationsTable` chạy đầu tiên để tạo bảng `migrations` (nếu database mới hoàn toàn)
   - Sau đó tất cả các migration khác sẽ chạy theo thứ tự để tạo các bảng:
     - users
     - profiles
     - permissions
     - roles
     - role_has_permissions
     - user_roles
     - user_permissions
     - post_categories
     - post_tags
     - posts
     - post_post_category
     - post_post_tag
   
   **Lưu ý**: 
   - Nếu database mới hoàn toàn, migration `CreateMigrationsTable` sẽ tự động tạo bảng `migrations` với cấu trúc đúng
   - Nếu gặp lỗi về bảng migrations, chỉ cần chạy lại `npm run migration:run`

5. **Chạy seeder để tạo dữ liệu mẫu**
   ```bash
   npm run seed
   ```

### Khi Có Thay Đổi Schema

1. **Tạo migration mới**
   ```bash
   npm run migration:generate -- src/core/database/migrations/YourMigrationName
   ```

2. **Kiểm tra file migration được tạo**

3. **Chạy migration**
   ```bash
   npm run migration:run
   ```

### Khi Cần Seed Lại Dữ Liệu

⚠️ **Cảnh báo**: Seeder được thiết kế để không seed lại nếu dữ liệu đã tồn tại.

Nếu muốn seed lại:
1. Xóa dữ liệu trong database thủ công, hoặc
2. Sửa logic trong các seeder để cho phép seed lại

---

## Troubleshooting

### Lỗi: Database chưa được tạo

**Nguyên nhân**: Database chưa tồn tại trong MySQL/MariaDB.

**Giải pháp**:
```sql
-- Kết nối MySQL và tạo database
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Hoặc dùng command line
mysql -u root -p
CREATE DATABASE your_database_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Lỗi: Cannot connect to database

**Nguyên nhân**: Cấu hình database trong `.env` không đúng.

**Giải pháp**:
1. Kiểm tra file `.env` tồn tại
2. Kiểm tra các biến môi trường:
   - `DB_TYPE`
   - `DB_HOST`
   - `DB_PORT`
   - `DB_USERNAME`
   - `DB_PASSWORD`
   - `DB_DATABASE`
3. Test kết nối database bằng MySQL client

### Lỗi: Unknown column 'timestamp' in 'field list'

**Nguyên nhân**: Bảng migrations đã tồn tại nhưng có cấu trúc sai (thiếu cột timestamp).

**Giải pháp**:

**Cách 1: Tự động (Khuyến nghị)**
```bash
# Chạy lại migrations, migration CreateMigrationsTable sẽ tự động fix
npm run migration:run
```
Migration `1736000000000-CreateMigrationsTable` sẽ tự động phát hiện và sửa bảng migrations.

**Cách 2: Thủ công**
```sql
-- Kết nối MySQL và chạy:
DROP TABLE IF EXISTS migrations;
```
Sau đó chạy lại migrations:
```bash
npm run migration:run
```

**Lưu ý**: Nếu bạn đã có data trong database, hãy backup trước khi xóa bảng migrations.

### Lỗi: Migration already executed

**Nguyên nhân**: Migration đã được chạy trước đó.

**Giải pháp**: 
- Không cần làm gì, đây là hành vi bình thường
- Nếu muốn chạy lại, cần xóa record trong bảng `migrations`

### Lỗi: Cannot find module 'data-source'

**Nguyên nhân**: File `data-source.ts` không tồn tại hoặc đường dẫn sai.

**Giải pháp**:
1. Kiểm tra file `data-source.ts` ở root của project
2. Đảm bảo TypeORM có thể load file này

### Lỗi: Table already exists

**Nguyên nhân**: Bảng đã được tạo trước đó (có thể do `synchronize: true` hoặc chạy migration thủ công).

**Giải pháp**:
1. Kiểm tra bảng `migrations` để xem migrations nào đã chạy
2. Nếu bảng đã tồn tại nhưng migration chưa được ghi nhận, cần thêm record vào bảng `migrations` thủ công

### Lỗi khi chạy seed: Data already exists

**Nguyên nhân**: Dữ liệu đã được seed trước đó.

**Giải pháp**: 
- Đây là hành vi bình thường, seeder được thiết kế để không seed lại
- Nếu muốn seed lại, cần xóa dữ liệu trong các bảng tương ứng

---

## Scripts Tóm Tắt

| Script | Mô Tả |
|--------|-------|
| `npm run migration:run` | Chạy tất cả migrations chưa được áp dụng |
| `npm run migration:revert` | Revert migration cuối cùng |
| `npm run migration:show` | Hiển thị trạng thái các migrations |
| `npm run migration:generate` | Tạo migration tự động từ thay đổi entities |
| `npm run migration:create` | Tạo file migration trống |
| `npm run seed` | Chạy seeder để tạo dữ liệu mẫu |

---

## Best Practices

1. ✅ **Luôn tạo migration** khi thay đổi schema, không dùng `synchronize: true` trong production
2. ✅ **Đặt tên migration có ý nghĩa** để dễ hiểu sau này
3. ✅ **Test migrations** trên môi trường development trước khi deploy
4. ✅ **Backup database** trước khi chạy migrations quan trọng
5. ✅ **Viết `down()` method** đúng cách để có thể revert
6. ✅ **Seeder nên idempotent** để có thể chạy lại an toàn
7. ✅ **Không commit dữ liệu nhạy cảm** trong seeders

---

## Tài Liệu Tham Khảo

- [TypeORM Migrations](https://typeorm.io/migrations)
- [NestJS Database](https://docs.nestjs.com/techniques/database)
- [TypeORM DataSource](https://typeorm.io/data-source)

---

**Lưu ý**: Tài liệu này được cập nhật lần cuối vào thời điểm hiện tại. Nếu có thay đổi trong cấu trúc project, vui lòng cập nhật tài liệu này.

