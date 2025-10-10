# Hook Methods trong BaseService

## 📋 Tổng Quan

BaseService đã được cập nhật với các hook methods cho phép các Service con override để thực hiện logic bổ sung sau khi các operations thành công hoặc thất bại.

## 🎯 Các Hook Methods Available

### 1. Create Operations
- `onCreateSuccess(array $result, array $data)` - Gọi khi create thành công
- `onCreateFail(array $data)` - Gọi khi create thất bại

### 2. Update Operations  
- `onUpdateSuccess(array $result, $id, array $data)` - Gọi khi update thành công
- `onUpdateFail($id, array $data)` - Gọi khi update thất bại

### 3. Delete Operations
- `onDeleteSuccess(?array $item, $id)` - Gọi khi delete thành công
- `onDeleteFail($id, ?array $item)` - Gọi khi delete thất bại

### 4. CreateOrUpdate Operations
- `onCreateOrUpdateSuccess(array $result, array $conditions, array $data)` - Gọi khi createOrUpdate thành công
- `onCreateOrUpdateFail(array $conditions, array $data)` - Gọi khi createOrUpdate thất bại

## 💡 Ví Dụ Sử Dụng

### Ví dụ 1: SystemConfigService

```php
class SystemConfigService extends BaseService
{
    /**
     * Hook method called when create operation succeeds
     * Clear cache and perform post-creation tasks
     */
    protected function onCreateSuccess(array $result, array $data): void
    {
        // Clear cache for this config group
        $this->clearCacheByGroup($result['group']);
        
        // Log creation
        Log::info("Config created: {$result['key']} = {$result['value']}");
        
        // Send notification to admin
        $this->notifyAdmin("New config created: {$result['key']}");
    }

    /**
     * Hook method called when update operation succeeds
     * Clear cache and perform post-update tasks
     */
    protected function onUpdateSuccess(array $result, $id, array $data): void
    {
        // Clear cache for this config group
        $this->clearCacheByGroup($result['group']);
        
        // Log update
        Log::info("Config updated: {$result['key']} = {$result['value']}");
        
        // Audit log
        $this->auditService->logChange($result['key'], $data['old_value'], $result['value']);
    }

    /**
     * Hook method called when delete operation succeeds
     * Clear cache and perform post-deletion tasks
     */
    protected function onDeleteSuccess(?array $item, $id): void
    {
        if ($item) {
            // Clear cache for this config group
            $this->clearCacheByGroup($item['group']);
            
            // Log deletion
            Log::info("Config deleted: {$item['key']}");
            
            // Archive the config
            $this->archiveConfig($item);
        }
    }

    /**
     * Hook method called when create operation fails
     * Handle error scenarios
     */
    protected function onCreateFail(array $data): void
    {
        // Log error
        Log::error("Failed to create config", [
            'data' => $data
        ]);
        
        // Send alert to admin
        $this->alertAdmin("Config creation failed");
    }

    private function clearCacheByGroup(string $group): void
    {
        Cache::forget("config_group_{$group}");
        Cache::forget('all_configs');
    }
}
```

### Ví dụ 2: UserService

```php
class UserService extends BaseService
{
    /**
     * Hook method called when user is created successfully
     */
    protected function onCreateSuccess(array $result, array $data): void
    {
        // Send welcome email
        Mail::to($result['email'])->send(new WelcomeEmail($result));
        
        // Create user profile
        $this->createUserProfile($result['id']);
        
        // Log user creation
        Log::info("New user registered: {$result['email']}");
        
        // Update statistics
        $this->updateUserStats();
    }

    /**
     * Hook method called when user is updated successfully
     */
    protected function onUpdateSuccess(array $result, $id, array $data): void
    {
        // Log profile update
        Log::info("User profile updated: {$result['email']}");
        
        // Clear user cache
        Cache::forget("user_{$id}");
        
        // Notify user of changes
        if (isset($data['email']) && $data['email'] !== $result['email']) {
            Mail::to($data['email'])->send(new EmailChangedNotification($result));
        }
    }

    /**
     * Hook method called when user is deleted successfully
     */
    protected function onDeleteSuccess(?array $item, $id): void
    {
        if ($item) {
            // Soft delete user data
            $this->softDeleteUserData($id);
            
            // Log user deletion
            Log::info("User deleted: {$item['email']}");
            
            // Send goodbye email
            Mail::to($item['email'])->send(new GoodbyeEmail($item));
        }
    }
}
```

### Ví dụ 3: PostService

```php
class PostService extends BaseService
{
    /**
     * Hook method called when post is created successfully
     */
    protected function onCreateSuccess(array $result, array $data): void
    {
        // Generate slug if not provided
        if (empty($result['slug'])) {
            $this->generateSlug($result['id']);
        }
        
        // Update category post count
        $this->updateCategoryPostCount($result['category_id']);
        
        // Clear category cache
        Cache::forget("category_{$result['category_id']}_posts");
        
        // Log post creation
        Log::info("New post created: {$result['title']}");
    }

    /**
     * Hook method called when post is updated successfully
     */
    protected function onUpdateSuccess(array $result, $id, array $data): void
    {
        // Regenerate slug if title changed
        if (isset($data['title']) && $data['title'] !== $result['title']) {
            $this->generateSlug($id);
        }
        
        // Clear post cache
        Cache::forget("post_{$id}");
        
        // Update search index
        $this->updateSearchIndex($result);
        
        // Log post update
        Log::info("Post updated: {$result['title']}");
    }

    /**
     * Hook method called when post is deleted successfully
     */
    protected function onDeleteSuccess(?array $item, $id): void
    {
        if ($item) {
            // Update category post count
            $this->updateCategoryPostCount($item['category_id'], -1);
            
            // Remove from search index
            $this->removeFromSearchIndex($id);
            
            // Clear all related cache
            Cache::forget("post_{$id}");
            Cache::forget("category_{$item['category_id']}_posts");
            
            // Log post deletion
            Log::info("Post deleted: {$item['title']}");
        }
    }
}
```

## 🔧 Cách Sử Dụng

### 1. Override Hook Methods
Chỉ cần override các hook methods trong Service con của bạn:

```php
class YourService extends BaseService
{
    protected function onCreateSuccess(array $result, array $data): void
    {
        // Your custom logic here
    }
    
    protected function onUpdateSuccess(array $result, $id, array $data): void
    {
        // Your custom logic here
    }
    
    // ... other hook methods
}
```

### 2. Sử Dụng Trong Controller
Controller không cần thay đổi gì, hooks sẽ tự động được gọi:

```php
class YourController extends BaseController
{
    public function store(): JsonResponse
    {
        $request = app($this->getStoreRequestClass());
        $data = $this->service->create($request->validated()); // Hooks tự động gọi
        return $this->successResponseWithFormat($data, 'Created successfully');
    }
}
```

## ⚡ Lợi Ích

1. **Separation of Concerns**: Logic bổ sung được tách riêng khỏi core CRUD operations
2. **Consistency**: Tất cả services đều có cùng pattern cho post-operations
3. **Flexibility**: Mỗi service có thể implement logic riêng mà không ảnh hưởng đến BaseService
4. **Maintainability**: Dễ maintain và debug khi có vấn đề
5. **Reusability**: BaseService có thể được sử dụng cho nhiều loại entities khác nhau

## 🎯 Best Practices

1. **Keep hooks lightweight**: Không nên thực hiện operations nặng trong hooks
2. **Handle exceptions**: Đảm bảo hooks không throw exceptions không cần thiết
3. **Use for side effects**: Hooks tốt nhất cho logging, caching, notifications
4. **Document your hooks**: Luôn document logic trong hooks để team hiểu rõ
5. **Test your hooks**: Viết tests cho hooks để đảm bảo chúng hoạt động đúng
