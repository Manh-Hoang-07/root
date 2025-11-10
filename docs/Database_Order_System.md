# Database Schema - Hệ Thống Đặt Hàng

Tài liệu mô tả chi tiết cấu trúc database cho hệ thống đặt hàng, bao gồm sản phẩm, thuộc tính, biến thể, giỏ hàng và đơn hàng.

## Tổng Quan

Hệ thống được thiết kế với kiến trúc **Product - Variant** trong đó:
- **Product**: Chứa thông tin chung của sản phẩm (tên, mô tả, danh mục, SEO)
- **Variant**: Chứa thông tin cụ thể có thể thay đổi (giá, tồn kho, thuộc tính)
- **Attributes**: Hệ thống thuộc tính linh hoạt cho biến thể sản phẩm

## Sơ Đồ Quan Hệ

```
product_categories (1) ──< (N) product_category (N) >── (1) products
                                                              │
                                                              │ (1)
                                                              │
                                                              │ (N)
                                                              ▼
                                                    product_variants (1) ──< (N) product_variant_attributes (N) >── (1) product_attribute_values
                                                                                                                          │
                                                                                                                          │ (N)
                                                                                                                          │
                                                                                                                          │ (1)
                                                                                                                          ▼
                                                                                                              product_attributes

products (1) ──< (N) carts ──> (1) cart_headers
      │
      │ (1)
      │
      │ (N)
      ▼
order_items (N) >── (1) orders
```

---

## 1. Bảng: `product_categories`

Lưu trữ danh mục sản phẩm với cấu trúc cây (parent-child).

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | BIGINT UNSIGNED | Primary key |
| `name` | VARCHAR(255) | Tên danh mục |
| `slug` | VARCHAR(255) UNIQUE | URL slug |
| `description` | TEXT | Mô tả danh mục |
| `parent_id` | BIGINT UNSIGNED NULL | ID danh mục cha (self-reference) |
| `image` | VARCHAR(500) | Hình ảnh danh mục |
| `icon` | VARCHAR(100) | Icon danh mục |
| `status` | ENUM('active', 'inactive') | Trạng thái |
| `sort_order` | INTEGER | Thứ tự sắp xếp |
| `meta_title` | VARCHAR(255) | SEO title |
| `meta_description` | TEXT | SEO description |
| `canonical_url` | VARCHAR(500) | Canonical URL |
| `og_image` | VARCHAR(500) | Open Graph image |
| `deleted_at` | TIMESTAMP NULL | Soft delete |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |
| `created_user_id` | BIGINT UNSIGNED NULL | User tạo |
| `updated_user_id` | BIGINT UNSIGNED NULL | User cập nhật |

### Quan Hệ

- **Parent Category**: `parent_id` → `product_categories.id` (self-reference)
- **Products**: Many-to-many qua bảng `product_category`

### Indexes

- `name`, `slug`, `parent_id`, `status`, `sort_order`
- Composite: `['status', 'sort_order']`, `['parent_id', 'status']`

---

## 2. Bảng: `product_attributes`

Lưu trữ các thuộc tính có thể dùng cho sản phẩm (ví dụ: Màu sắc, Kích thước, RAM, Storage).

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | BIGINT UNSIGNED | Primary key |
| `name` | VARCHAR(255) | Tên thuộc tính (VD: "Màu sắc", "Kích thước") |
| `slug` | VARCHAR(255) UNIQUE | URL slug |
| `type` | ENUM('text', 'select', 'multiselect', 'color', 'image') | Loại thuộc tính |
| `is_required` | BOOLEAN | Bắt buộc hay không |
| `is_variation` | BOOLEAN | Có dùng để tạo biến thể không |
| `is_filterable` | BOOLEAN | Có thể dùng để lọc không |
| `sort_order` | INTEGER | Thứ tự sắp xếp |
| `status` | ENUM('active', 'inactive') | Trạng thái |
| `deleted_at` | TIMESTAMP NULL | Soft delete |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |
| `created_user_id` | BIGINT UNSIGNED NULL | User tạo |
| `updated_user_id` | BIGINT UNSIGNED NULL | User cập nhật |

### Quan Hệ

- **Attribute Values**: One-to-many → `product_attribute_values.product_attribute_id`

### Indexes

- `name`, `slug`, `type`, `is_required`, `is_variation`, `is_filterable`, `status`, `sort_order`
- Composite: `['status', 'sort_order']`, `['is_variation', 'status']`

### Ví Dụ

```
id: 1
name: "Màu sắc"
slug: "mau-sac"
type: "color"
is_variation: true
is_filterable: true
```

---

## 3. Bảng: `product_attribute_values`

Lưu trữ các giá trị của thuộc tính (ví dụ: "Đỏ", "Xanh", "8GB", "16GB").

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | BIGINT UNSIGNED | Primary key |
| `product_attribute_id` | BIGINT UNSIGNED | Foreign key → `product_attributes.id` |
| `value` | VARCHAR(255) | Giá trị (VD: "Đỏ", "8GB") |
| `color_code` | VARCHAR(7) NULL | Mã màu hex (nếu type = color) |
| `image` | VARCHAR(500) NULL | Hình ảnh (nếu type = image) |
| `sort_order` | INTEGER | Thứ tự sắp xếp |
| `status` | ENUM('active', 'inactive') | Trạng thái |
| `deleted_at` | TIMESTAMP NULL | Soft delete |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |
| `created_user_id` | BIGINT UNSIGNED NULL | User tạo |
| `updated_user_id` | BIGINT UNSIGNED NULL | User cập nhật |

### Quan Hệ

- **Attribute**: Many-to-one → `product_attributes.id`
- **Variant Attributes**: One-to-many → `product_variant_attributes.product_attribute_value_id`

### Indexes

- `product_attribute_id`, `value`, `color_code`, `status`, `sort_order`
- Composite: `['product_attribute_id', 'status']`, `['product_attribute_id', 'sort_order']`

### Ví Dụ

```
id: 1
product_attribute_id: 1 (Màu sắc)
value: "Đỏ"
color_code: "#FF0000"
```

---

## 4. Bảng: `products`

Lưu trữ thông tin chung của sản phẩm. **Lưu ý**: Giá và tồn kho không còn ở bảng này, chỉ có ở `product_variants`.

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | BIGINT UNSIGNED | Primary key |
| `name` | VARCHAR(255) | Tên sản phẩm |
| `slug` | VARCHAR(255) UNIQUE | URL slug |
| `sku` | VARCHAR(100) UNIQUE | SKU chính (base SKU) |
| `description` | TEXT | Mô tả chi tiết |
| `short_description` | TEXT | Mô tả ngắn |
| `min_stock_level` | INTEGER | Mức tồn kho tối thiểu cảnh báo |
| `image` | VARCHAR(500) | Hình ảnh chính |
| `gallery` | JSON | Mảng đường dẫn hình ảnh |
| `status` | ENUM('active', 'inactive', 'draft') | Trạng thái |
| `is_featured` | BOOLEAN | Sản phẩm nổi bật |
| `is_variable` | BOOLEAN | Có biến thể hay không |
| `is_digital` | BOOLEAN | Sản phẩm số |
| `download_limit` | INTEGER NULL | Giới hạn download (nếu digital) |
| `meta_title` | VARCHAR(255) | SEO title |
| `meta_description` | TEXT | SEO description |
| `canonical_url` | VARCHAR(500) | Canonical URL |
| `og_title` | VARCHAR(255) | Open Graph title |
| `og_description` | TEXT | Open Graph description |
| `og_image` | VARCHAR(500) | Open Graph image |
| `deleted_at` | TIMESTAMP NULL | Soft delete |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |
| `created_user_id` | BIGINT UNSIGNED NULL | User tạo |
| `updated_user_id` | BIGINT UNSIGNED NULL | User cập nhật |

### Quan Hệ

- **Categories**: Many-to-many qua bảng `product_category`
- **Variants**: One-to-many → `product_variants.product_id`
- **Cart Items**: One-to-many → `carts.product_id`
- **Order Items**: One-to-many → `order_items.product_id`

### Indexes

- `name`, `slug`, `sku`, `status`, `is_featured`, `is_variable`, `is_digital`
- Composite: `['status', 'is_featured']`, `['status', 'created_at']`

### Lưu Ý Quan Trọng

⚠️ **Bảng này KHÔNG còn các trường:**
- `price`, `sale_price`, `cost_price` → Chuyển sang `product_variants`
- `stock_quantity` → Chuyển sang `product_variants`
- `weight`, `dimensions` → Chuyển sang `product_variants`

Tất cả sản phẩm phải có ít nhất 1 variant. Giá và tồn kho chỉ lấy từ variant.

---

## 5. Bảng: `product_variants`

Lưu trữ các biến thể của sản phẩm. Mỗi biến thể có giá, tồn kho và thuộc tính riêng.

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | BIGINT UNSIGNED | Primary key |
| `product_id` | BIGINT UNSIGNED | Foreign key → `products.id` |
| `sku` | VARCHAR(100) UNIQUE | SKU của variant |
| `name` | VARCHAR(255) | Tên variant |
| `price` | DECIMAL(15,2) | Giá gốc |
| `sale_price` | DECIMAL(15,2) NULL | Giá khuyến mãi |
| `cost_price` | DECIMAL(15,2) NULL | Giá vốn |
| `stock_quantity` | INTEGER | Số lượng tồn kho |
| `weight` | DECIMAL(8,2) NULL | Trọng lượng (kg) |
| `image` | VARCHAR(500) NULL | Hình ảnh variant |
| `status` | ENUM('active', 'inactive') | Trạng thái |
| `deleted_at` | TIMESTAMP NULL | Soft delete |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |
| `created_user_id` | BIGINT UNSIGNED NULL | User tạo |
| `updated_user_id` | BIGINT UNSIGNED NULL | User cập nhật |

### Quan Hệ

- **Product**: Many-to-one → `products.id`
- **Variant Attributes**: One-to-many → `product_variant_attributes.product_variant_id`
- **Cart Items**: One-to-many → `carts.product_variant_id`
- **Order Items**: One-to-many → `order_items.product_variant_id`

### Indexes

- `product_id`, `sku`, `name`, `price`, `sale_price`, `stock_quantity`, `status`
- Composite: `['product_id', 'status']`, `['status', 'stock_quantity']`

### Ví Dụ

```
id: 1
product_id: 1
sku: "IPHONE15-RED-128GB"
name: "iPhone 15 - Đỏ - 128GB"
price: 20000000
sale_price: 19000000
stock_quantity: 50
weight: 0.2
```

---

## 6. Bảng: `product_variant_attributes`

Bảng pivot liên kết biến thể với thuộc tính và giá trị thuộc tính.

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | BIGINT UNSIGNED | Primary key |
| `product_variant_id` | BIGINT UNSIGNED | Foreign key → `product_variants.id` |
| `product_attribute_id` | BIGINT UNSIGNED | Foreign key → `product_attributes.id` |
| `product_attribute_value_id` | BIGINT UNSIGNED | Foreign key → `product_attribute_values.id` |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |

### Quan Hệ

- **Variant**: Many-to-one → `product_variants.id`
- **Attribute**: Many-to-one → `product_attributes.id`
- **Attribute Value**: Many-to-one → `product_attribute_values.id`

### Constraints

- **Unique**: `['product_variant_id', 'product_attribute_id']` - Mỗi variant chỉ có 1 giá trị cho mỗi thuộc tính

### Indexes

- `product_variant_id`, `product_attribute_id`, `product_attribute_value_id`
- Composite: `['product_variant_id', 'product_attribute_id']`

### Ví Dụ

```
id: 1
product_variant_id: 1
product_attribute_id: 1 (Màu sắc)
product_attribute_value_id: 1 (Đỏ)

id: 2
product_variant_id: 1
product_attribute_id: 2 (Storage)
product_attribute_value_id: 5 (128GB)
```

---

## 7. Bảng: `product_category`

Bảng pivot liên kết sản phẩm với danh mục (Many-to-Many).

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | BIGINT UNSIGNED | Primary key |
| `product_id` | BIGINT UNSIGNED | Foreign key → `products.id` |
| `product_category_id` | BIGINT UNSIGNED | Foreign key → `product_categories.id` |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |

### Quan Hệ

- **Product**: Many-to-one → `products.id`
- **Category**: Many-to-one → `product_categories.id`

### Constraints

- **Unique**: `['product_id', 'product_category_id']` - Mỗi sản phẩm chỉ thuộc 1 lần vào 1 danh mục

### Indexes

- `product_id`, `product_category_id`

---

## 8. Bảng: `cart_headers`

Lưu trữ thông tin header của giỏ hàng (tổng tiền, mã giảm giá, v.v.).

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | VARCHAR (Primary Key) | ID giỏ hàng (thường là UUID) |
| `user_id` | BIGINT UNSIGNED NULL | Foreign key → `users.id` (nếu user đã login) |
| `session_id` | VARCHAR NULL | Session ID (nếu chưa login) |
| `currency` | VARCHAR(10) | Mã tiền tệ (mặc định: VND) |
| `subtotal` | DECIMAL(10,2) | Tổng tiền hàng |
| `tax_amount` | DECIMAL(10,2) | Thuế |
| `shipping_amount` | DECIMAL(10,2) | Phí vận chuyển |
| `discount_amount` | DECIMAL(10,2) | Giảm giá |
| `total_amount` | DECIMAL(10,2) | Tổng cộng |
| `coupon_code` | VARCHAR NULL | Mã giảm giá |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |
| `created_user_id` | BIGINT UNSIGNED NULL | User tạo |
| `updated_user_id` | BIGINT UNSIGNED NULL | User cập nhật |

### Quan Hệ

- **User**: Many-to-one → `users.id` (nullable)
- **Cart Items**: One-to-many → `carts.cart_header_id`

### Indexes

- `user_id`, `session_id`

### Lưu Ý

- Nếu user đã login: `user_id` có giá trị, `session_id` = NULL
- Nếu user chưa login: `user_id` = NULL, `session_id` có giá trị

---

## 9. Bảng: `carts`

Lưu trữ các sản phẩm trong giỏ hàng.

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | BIGINT UNSIGNED | Primary key |
| `cart_header_id` | VARCHAR | Foreign key → `cart_headers.id` |
| `product_id` | BIGINT UNSIGNED | Foreign key → `products.id` |
| `product_variant_id` | BIGINT UNSIGNED NULL | Foreign key → `product_variants.id` |
| `product_name` | VARCHAR(255) | Tên sản phẩm (snapshot) |
| `product_sku` | VARCHAR(100) | SKU sản phẩm (snapshot) |
| `variant_name` | VARCHAR NULL | Tên variant (snapshot) |
| `quantity` | INTEGER | Số lượng |
| `unit_price` | DECIMAL(15,2) | Giá đơn vị (snapshot từ variant) |
| `total_price` | DECIMAL(15,2) | Tổng tiền (quantity × unit_price) |
| `product_attributes` | JSON NULL | Thuộc tính variant (snapshot) |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |
| `created_user_id` | BIGINT UNSIGNED NULL | User tạo |
| `updated_user_id` | BIGINT UNSIGNED NULL | User cập nhật |

### Quan Hệ

- **Cart Header**: Many-to-one → `cart_headers.id`
- **Product**: Many-to-one → `products.id`
- **Variant**: Many-to-one → `product_variants.id` (nullable nhưng **bắt buộc** trong logic)

### Constraints

- **Unique**: `['cart_header_id', 'product_id', 'product_variant_id']` - Mỗi variant chỉ xuất hiện 1 lần trong giỏ hàng

### Indexes

- `cart_header_id`, `product_id`, `product_variant_id`, `quantity`
- Composite: `['cart_header_id', 'product_id', 'product_variant_id']`

### Lưu Ý Quan Trọng

⚠️ **`product_variant_id` là BẮT BUỘC** trong logic ứng dụng:
- Mọi sản phẩm phải có variant khi thêm vào giỏ hàng
- Giá và tồn kho lấy từ variant, không từ product
- `product_name`, `product_sku`, `variant_name`, `unit_price` là snapshot để đảm bảo tính nhất quán khi sản phẩm thay đổi

---

## 10. Bảng: `orders`

Lưu trữ thông tin đơn hàng.

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | BIGINT UNSIGNED | Primary key |
| `order_number` | VARCHAR(50) UNIQUE | Số đơn hàng (unique) |
| `user_id` | BIGINT UNSIGNED NULL | Foreign key → `users.id` |
| `customer_name` | VARCHAR(255) | Tên khách hàng |
| `customer_email` | VARCHAR(255) | Email khách hàng |
| `customer_phone` | VARCHAR(20) | Số điện thoại |
| `shipping_address` | JSON | Địa chỉ giao hàng |
| `billing_address` | JSON | Địa chỉ thanh toán |
| `status` | ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled') | Trạng thái đơn hàng |
| `payment_status` | ENUM('pending', 'paid', 'failed', 'refunded', 'partially_refunded') | Trạng thái thanh toán |
| `shipping_status` | ENUM('pending', 'preparing', 'shipped', 'delivered', 'returned') | Trạng thái vận chuyển |
| `subtotal` | DECIMAL(15,2) | Tổng tiền hàng |
| `tax_amount` | DECIMAL(15,2) | Thuế |
| `shipping_amount` | DECIMAL(15,2) | Phí vận chuyển |
| `discount_amount` | DECIMAL(15,2) | Giảm giá |
| `total_amount` | DECIMAL(15,2) | Tổng cộng |
| `currency` | VARCHAR(3) | Mã tiền tệ (mặc định: VND) |
| `notes` | TEXT NULL | Ghi chú |
| `tracking_number` | VARCHAR(100) NULL | Mã vận đơn |
| `shipped_at` | TIMESTAMP NULL | Ngày giao hàng |
| `delivered_at` | TIMESTAMP NULL | Ngày nhận hàng |
| `deleted_at` | TIMESTAMP NULL | Soft delete |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |
| `created_user_id` | BIGINT UNSIGNED NULL | User tạo |
| `updated_user_id` | BIGINT UNSIGNED NULL | User cập nhật |

### Quan Hệ

- **User**: Many-to-one → `users.id` (nullable)
- **Order Items**: One-to-many → `order_items.order_id`

### Indexes

- `order_number`, `user_id`, `customer_email`, `customer_phone`, `status`, `payment_status`, `shipping_status`, `total_amount`, `tracking_number`
- Composite: `['status', 'created_at']`, `['payment_status', 'created_at']`, `['user_id', 'status']`

### Ví Dụ JSON Address

```json
{
  "name": "Nguyễn Văn A",
  "phone": "0123456789",
  "address": "123 Đường ABC",
  "ward": "Phường 1",
  "district": "Quận 1",
  "province": "TP.HCM"
}
```

---

## 11. Bảng: `order_items`

Lưu trữ các sản phẩm trong đơn hàng.

### Cấu Trúc

| Cột | Kiểu | Mô Tả |
|-----|------|-------|
| `id` | BIGINT UNSIGNED | Primary key |
| `order_id` | BIGINT UNSIGNED | Foreign key → `orders.id` |
| `product_id` | BIGINT UNSIGNED | Foreign key → `products.id` |
| `product_variant_id` | BIGINT UNSIGNED NULL | Foreign key → `product_variants.id` |
| `product_name` | VARCHAR(255) | Tên sản phẩm (snapshot) |
| `product_sku` | VARCHAR(100) | SKU sản phẩm (snapshot) |
| `variant_name` | VARCHAR(255) NULL | Tên variant (snapshot) |
| `quantity` | INTEGER | Số lượng |
| `unit_price` | DECIMAL(15,2) | Giá đơn vị (snapshot) |
| `total_price` | DECIMAL(15,2) | Tổng tiền (quantity × unit_price) |
| `product_attributes` | JSON NULL | Thuộc tính variant (snapshot) |
| `created_at` | TIMESTAMP | Ngày tạo |
| `updated_at` | TIMESTAMP | Ngày cập nhật |
| `created_user_id` | BIGINT UNSIGNED NULL | User tạo |
| `updated_user_id` | BIGINT UNSIGNED NULL | User cập nhật |

### Quan Hệ

- **Order**: Many-to-one → `orders.id`
- **Product**: Many-to-one → `products.id`
- **Variant**: Many-to-one → `product_variants.id` (nullable nhưng **bắt buộc** trong logic)

### Indexes

- `order_id`, `product_id`, `product_variant_id`, `product_sku`, `quantity`, `unit_price`, `total_price`
- Composite: `['order_id', 'product_id']`

### Lưu Ý Quan Trọng

⚠️ **Tất cả thông tin là SNAPSHOT** (ảnh chụp tại thời điểm đặt hàng):
- `product_name`, `product_sku`, `variant_name`, `unit_price`, `product_attributes` được lưu lại
- Đảm bảo tính nhất quán dữ liệu ngay cả khi sản phẩm thay đổi sau đó
- `product_variant_id` là bắt buộc trong logic (mọi sản phẩm phải có variant)

---

## Flow Đặt Hàng

### 1. Thêm Vào Giỏ Hàng

```
User chọn sản phẩm với variant
    ↓
Validate variant tồn tại và còn hàng
    ↓
Tạo/Update cart_headers
    ↓
Tạo/Update carts (lưu snapshot: product_name, variant_name, unit_price)
    ↓
Recalculate cart_headers (subtotal, total_amount)
```

### 2. Tạo Đơn Hàng

```
User checkout từ giỏ hàng
    ↓
Validate stock của tất cả variants trong cart
    ↓
Tạo orders (lưu thông tin khách hàng, địa chỉ, tổng tiền)
    ↓
Tạo order_items (copy từ carts, lưu snapshot)
    ↓
Trừ stock từ product_variants
    ↓
Xóa carts và cart_headers
```

### 3. Quản Lý Đơn Hàng

```
Admin xử lý đơn hàng
    ↓
Update orders.status, orders.payment_status, orders.shipping_status
    ↓
Nếu hủy đơn: Cộng lại stock vào product_variants
    ↓
Nếu giao hàng: Update orders.shipped_at, orders.tracking_number
```

---

## Quy Tắc Quan Trọng

### 1. Variant là Bắt Buộc

- ✅ Mọi sản phẩm phải có ít nhất 1 variant
- ✅ Khi thêm vào cart/order, bắt buộc phải chọn variant
- ✅ Giá và tồn kho chỉ lấy từ variant, không từ product

### 2. Snapshot Data

- ✅ Cart và Order Items lưu snapshot của product_name, variant_name, unit_price
- ✅ Đảm bảo tính nhất quán khi sản phẩm thay đổi sau đó

### 3. Stock Management

- ✅ Stock chỉ quản lý ở `product_variants.stock_quantity`
- ✅ Khi tạo order: Trừ stock từ variant
- ✅ Khi hủy order: Cộng lại stock vào variant

### 4. Price Calculation

- ✅ Giá bán = `sale_price` (nếu có) hoặc `price` (nếu không có sale_price)
- ✅ Giá được lưu snapshot trong cart và order_items

---

## Ví Dụ Thực Tế

### Sản Phẩm: iPhone 15

**Product:**
```
id: 1
name: "iPhone 15"
slug: "iphone-15"
sku: "IPHONE15"
is_variable: true
```

**Variants:**
```
Variant 1:
- id: 1
- product_id: 1
- sku: "IPHONE15-RED-128GB"
- name: "iPhone 15 - Đỏ - 128GB"
- price: 20000000
- sale_price: 19000000
- stock_quantity: 50

Variant 2:
- id: 2
- product_id: 1
- sku: "IPHONE15-BLUE-256GB"
- name: "iPhone 15 - Xanh - 256GB"
- price: 22000000
- sale_price: null
- stock_quantity: 30
```

**Variant Attributes:**
```
Variant 1:
- Color: Đỏ (product_attribute_value_id: 1)
- Storage: 128GB (product_attribute_value_id: 5)

Variant 2:
- Color: Xanh (product_attribute_value_id: 2)
- Storage: 256GB (product_attribute_value_id: 6)
```

**Cart:**
```
cart_header_id: "abc-123"
user_id: 1
total_amount: 19000000

cart_items:
- product_id: 1
- product_variant_id: 1
- quantity: 1
- unit_price: 19000000
- total_price: 19000000
```

**Order:**
```
order_number: "ORD-2025-001"
user_id: 1
total_amount: 19000000
status: "pending"

order_items:
- product_id: 1
- product_variant_id: 1
- product_name: "iPhone 15" (snapshot)
- variant_name: "iPhone 15 - Đỏ - 128GB" (snapshot)
- quantity: 1
- unit_price: 19000000 (snapshot)
- total_price: 19000000
```

---

## Tổng Kết

Hệ thống database được thiết kế với các nguyên tắc:

1. **Tách biệt Product và Variant**: Product chứa thông tin chung, Variant chứa thông tin cụ thể
2. **Attributes linh hoạt**: Hệ thống thuộc tính có thể mở rộng dễ dàng
3. **Snapshot Data**: Đảm bảo tính nhất quán dữ liệu trong cart và order
4. **Stock Management**: Quản lý tồn kho chỉ ở variant level
5. **Price Management**: Giá bán chỉ ở variant level

Tất cả các bảng đều có soft delete (`deleted_at`) và user tracking (`created_user_id`, `updated_user_id`) để hỗ trợ audit trail.

