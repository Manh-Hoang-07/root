# API Response Helpers

## Tổng quan

Hệ thống cung cấp 2 cách để tạo API responses:

1. **ResponseTrait** - Sử dụng trong controllers
2. **ApiResponse Helper** - Sử dụng ở bất kỳ đâu

## 1. ResponseTrait

### Cách sử dụng trong Controller:

```php
<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\BaseController;
use App\Http\Controllers\Api\ResponseTrait;

class ExampleController extends BaseController
{
    use ResponseTrait;

    public function index()
    {
        try {
            $data = $this->service->list();
            return $this->successResponse($data, 'Lấy dữ liệu thành công');
        } catch (\Exception $e) {
            return $this->errorResponse('Có lỗi xảy ra');
        }
    }

    public function store(Request $request)
    {
        try {
            $data = $this->service->create($request->validated());
            return $this->createdResponse($data, 'Tạo thành công');
        } catch (\Exception $e) {
            return $this->validationErrorResponse($e->getMessage());
        }
    }

    public function show($id)
    {
        $item = $this->service->find($id);
        
        if (!$item) {
            return $this->notFoundResponse('Không tìm thấy dữ liệu');
        }
        
        return $this->successResponse($item);
    }

    public function update($id, Request $request)
    {
        try {
            $data = $this->service->update($id, $request->validated());
            return $this->updatedResponse($data, 'Cập nhật thành công');
        } catch (\Exception $e) {
            return $this->errorResponse('Cập nhật thất bại');
        }
    }

    public function destroy($id)
    {
        $result = $this->service->delete($id);
        
        if ($result) {
            return $this->deletedResponse('Xóa thành công');
        }
        
        return $this->errorResponse('Xóa thất bại');
    }
}
```

### Các methods có sẵn:

- `successResponse($data, $message, $statusCode)`
- `errorResponse($message, $statusCode)`
- `notFoundResponse($message)`
- `validationErrorResponse($errors, $message)`
- `unauthorizedResponse($message)`
- `forbiddenResponse($message)`
- `badRequestResponse($message)`
- `serverErrorResponse($message)`
- `createdResponse($data, $message)`
- `updatedResponse($data, $message)`
- `deletedResponse($message)`
- `noContentResponse()`
- `conflictResponse($message)`
- `tooManyRequestsResponse($message)`
- `serviceUnavailableResponse($message)`

## 2. ApiResponse Helper

### Cách sử dụng:

```php
<?php

use App\Helpers\ApiResponse;

// Trong controller
public function index()
{
    try {
        $data = $this->service->list();
        return ApiResponse::success($data, 'Lấy dữ liệu thành công');
    } catch (\Exception $e) {
        return ApiResponse::error('Có lỗi xảy ra');
    }
}

// Trong service
public function someMethod()
{
    if (!$this->validate()) {
        return ApiResponse::validationError($errors);
    }
    
    return ApiResponse::success($data);
}

// Trong middleware
public function handle($request, $next)
{
    if (!$this->checkPermission()) {
        return ApiResponse::forbidden('Không có quyền truy cập');
    }
    
    return $next($request);
}
```

### Các methods có sẵn:

- `ApiResponse::success($data, $message, $statusCode)`
- `ApiResponse::error($message, $statusCode)`
- `ApiResponse::notFound($message)`
- `ApiResponse::validationError($errors, $message)`
- `ApiResponse::unauthorized($message)`
- `ApiResponse::forbidden($message)`
- `ApiResponse::badRequest($message)`
- `ApiResponse::serverError($message)`
- `ApiResponse::created($data, $message)`
- `ApiResponse::updated($data, $message)`
- `ApiResponse::deleted($message)`
- `ApiResponse::noContent()`
- `ApiResponse::conflict($message)`
- `ApiResponse::tooManyRequests($message)`
- `ApiResponse::serviceUnavailable($message)`
- `ApiResponse::custom($data, $message, $statusCode)`
- `ApiResponse::paginated($data, $message)`

## 3. Response Format

Tất cả responses đều có format chuẩn:

```json
{
    "success": true,
    "message": "Thành công",
    "data": {...}
}
```

### Success Response:
```json
{
    "success": true,
    "message": "Lấy dữ liệu thành công",
    "data": [...]
}
```

### Error Response:
```json
{
    "success": false,
    "message": "Có lỗi xảy ra"
}
```

### Validation Error Response:
```json
{
    "success": false,
    "message": "Dữ liệu không hợp lệ",
    "errors": {
        "field": ["Error message"]
    }
}
```

### Paginated Response:
```json
{
    "success": true,
    "message": "Thành công",
    "data": [...],
    "pagination": {
        "current_page": 1,
        "per_page": 20,
        "total": 100,
        "last_page": 5,
        "from": 1,
        "to": 20
    }
}
```

## 4. HTTP Status Codes

- `200` - Success
- `201` - Created
- `204` - No Content
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `409` - Conflict
- `422` - Validation Error
- `429` - Too Many Requests
- `500` - Server Error
- `503` - Service Unavailable

## 5. Best Practices

1. **Sử dụng ResponseTrait** cho controllers kế thừa từ BaseController
2. **Sử dụng ApiResponse Helper** cho các trường hợp đặc biệt
3. **Luôn có message** rõ ràng cho user
4. **Sử dụng đúng status code** cho từng loại response
5. **Validation errors** nên có chi tiết lỗi
6. **Paginated data** sử dụng `ApiResponse::paginated()`

## 6. Ví dụ thực tế

```php
// Controller với ResponseTrait
class ProductController extends BaseController
{
    use ResponseTrait;

    public function store(ProductRequest $request)
    {
        try {
            $product = $this->service->create($request->validated());
            return $this->createdResponse($product, 'Sản phẩm đã được tạo');
        } catch (ValidationException $e) {
            return $this->validationErrorResponse($e->errors());
        } catch (\Exception $e) {
            return $this->serverErrorResponse('Không thể tạo sản phẩm');
        }
    }
}

// Service với ApiResponse Helper
class ProductService
{
    public function find($id)
    {
        $product = Product::find($id);
        
        if (!$product) {
            return ApiResponse::notFound('Sản phẩm không tồn tại');
        }
        
        return ApiResponse::success($product);
    }
}
``` 