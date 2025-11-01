# profiles

**Mục đích**: Lưu thông tin chi tiết của người dùng

## Columns

```sql
id                      BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
user_id                 BIGINT UNSIGNED NOT NULL
name                    VARCHAR(255) NULL
image                   VARCHAR(255) NULL
birthday                DATE NULL
gender                  VARCHAR(50) NULL
address                 TEXT NULL
about                   TEXT NULL
created_user_id         BIGINT UNSIGNED NULL
updated_user_id         BIGINT UNSIGNED NULL
created_at              DATETIME DEFAULT CURRENT_TIMESTAMP
updated_at              DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
deleted_at              DATETIME NULL
```

## Relations

- many-to-one → users

## Foreign Keys

- user_id → users.id (CASCADE)

## Lưu ý

Có `deleted_at` để hỗ trợ soft delete

