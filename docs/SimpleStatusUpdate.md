# Chức năng Thay đổi Trạng thái và Field Đơn giản

## BaseService

```php
/**
 * Update any field of a record
 * 
 * @param mixed $id The ID of the record
 * @param mixed $value The new value
 * @param string $field The field name
 * @return array|null The updated record or null if not found
 */
public function updateField($id, $value, string $field): ?array
{
    return $this->repo->update($id, [$field => $value]);
}

/**
 * Update status of a record (only if status is different)
 * 
 * @param mixed $id The ID of the record
 * @param mixed $newStatus The new status value
 * @param string $field The field name for status (default: 'status')
 * @return array|null The updated record or null if not found
 */
public function updateStatus($id, $newStatus, string $field = 'status'): ?array
{
    // Get current record
    $currentRecord = $this->find($id);
    if (!$currentRecord) {
        return null;
    }

    // Check if status is different
    $currentStatus = $currentRecord[$field] ?? null;
    if ($currentStatus === $newStatus) {
        // Status is the same, return current record without update
        return $currentRecord;
    }

    // Status is different, update it
    return $this->updateField($id, $newStatus, $field);
}
```

## BaseController

```php
/**
 * Update status of a resource (only if different)
 * @param int|string $id
 * @return JsonResponse
 */
public function updateStatus($id): JsonResponse
{
    try {
        $request = app($this->getStatusUpdateRequestClass());
        $field = $request->get('field', 'status');
        $result = $this->service->updateStatus($id, $request->status, $field);
        
        if (!$result) {
            return $this->apiResponse(false, null, 'Không tìm thấy dữ liệu để cập nhật', 404);
        }
        
        return $this->apiResponse(true, $result, 'Cập nhật trạng thái thành công');
    } catch (ValidationException|HttpResponseException $e) {
        throw $e; // Let the framework return 422 with validation errors
    } catch (Exception $e) {
        $this->logError('UpdateStatus', $e, ['id' => $id]);
        return $this->apiResponse(false, null, 'Không thể cập nhật trạng thái', 500);
    }
}
```


## Cách sử dụng

### 1. Trong Controller (sử dụng trực tiếp)

```php
class ProductController extends BaseController
{
    // Sử dụng request class mặc định
    public function updateStatus($id)
    {
        return parent::updateStatus($id);
    }
    
    // Hoặc sử dụng request class tùy chỉnh
    protected $statusUpdateRequestClass = ProductStatusUpdateRequest::class;
}
```

### 2. Tạo Request Class tùy chỉnh (nếu cần)

```php
class ProductStatusUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:active,inactive,draft'],
            'field' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Trạng thái là bắt buộc.',
            'status.in' => 'Trạng thái sản phẩm không hợp lệ.',
            'field.string' => 'Tên field phải là chuỗi ký tự.',
            'field.max' => 'Tên field không được vượt quá 50 ký tự.',
            'notes.max' => 'Ghi chú không được vượt quá 500 ký tự.',
        ];
    }
}
```

### 3. Trong Service (sử dụng trực tiếp)

```php
class ProductService extends BaseService
{
    // Cập nhật trạng thái (chỉ khi khác)
    public function updateProductStatus($id, $status)
    {
        return $this->updateStatus($id, $status, 'product_status');
    }
    
    // Cập nhật bất kỳ field nào (nếu cần)
    public function updateProductField($id, $value, $field)
    {
        return $this->updateField($id, $value, $field);
    }
}
```

### 4. Request example

```http
PUT /api/admin/products/1/status
Content-Type: application/json

{
    "status": "active",
    "field": "product_status"
}
```

### 5. Response examples

#### Khi status khác (cập nhật)
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Product Name",
        "product_status": "active",
        "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    "message": "Cập nhật trạng thái thành công"
}
```

#### Khi status giống (không cập nhật)
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "Product Name",
        "product_status": "active",
        "updated_at": "2024-01-15T09:00:00.000000Z"
    },
    "message": "Cập nhật trạng thái thành công"
}
```

## Routes

```php
// routes/api/admin.php
Route::put('products/{id}/status', [ProductController::class, 'updateStatus']);
Route::put('orders/{id}/status', [OrderController::class, 'updateStatus']);
Route::put('contacts/{id}/status', [ContactController::class, 'updateStatus']);
```

## Lợi ích

- **updateStatus**: Chỉ cập nhật khi trạng thái thực sự khác → Tiết kiệm database queries
- **updateField**: Luôn cập nhật → Phù hợp cho các field khác ngoài status (chỉ trong Service)
- **Đơn giản**: Chỉ cần gọi parent method
- **Hiệu quả**: Tránh cập nhật không cần thiết

Đơn giản và hiệu quả! 😊
