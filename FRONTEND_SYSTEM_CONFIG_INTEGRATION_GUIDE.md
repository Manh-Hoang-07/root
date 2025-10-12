# 🎯 Hướng Dẫn Tích Hợp System Config cho Frontend

## 📋 Tổng Quan

System Config là module quản lý cấu hình hệ thống cho phép:
- **Admin**: Quản lý đầy đủ các cấu hình (CRUD, bulk update, audit logs)
- **Public**: Truy cập các cấu hình công khai (read-only)
- **Caching**: Tự động cache để tối ưu performance
- **Encryption**: Mã hóa các cấu hình nhạy cảm
- **Audit**: Theo dõi lịch sử thay đổi

---

## 🔗 API Endpoints

### **Base URLs**
- **Admin API**: `/api/admin/system-configs`
- **Public API**: `/api/system-configs`

### **Authentication**
- **Admin APIs**: Cần Bearer Token
- **Public APIs**: Không cần authentication

---

## 🛠️ Admin APIs (Cần Authentication)

### **1. CRUD Operations**

#### **GET** `/api/admin/system-configs/` - Lấy danh sách configs
```javascript
// Request
const response = await fetch('/api/admin/system-configs/', {
  method: 'GET',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json'
  }
});

// Query Parameters
const params = new URLSearchParams({
  page: 1,                    // Pagination
  per_page: 15,              // Items per page (max: 100)
  group: 'general',          // Filter by group
  is_public: true,          // Filter by public status
  status: 'active',         // Filter by status
  search: 'app_name',       // Search by key/description
  sort_by: 'created_at',     // Sort field
  sort_direction: 'desc',   // Sort direction
  fields: 'id,key,value,group' // Select specific fields
});

const response = await fetch(`/api/admin/system-configs/?${params}`);
```

#### **GET** `/api/admin/system-configs/{id}` - Lấy config theo ID
```javascript
const response = await fetch('/api/admin/system-configs/1', {
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json'
  }
});
```

#### **POST** `/api/admin/system-configs/store` - Tạo config mới
```javascript
const configData = {
  key: 'app_name',
  value: 'My Application',
  type: 'string',
  group: 'general',
  description: 'Tên ứng dụng',
  is_public: true,
  is_encrypted: false,
  status: 'active',
  validation_rules: ['required', 'max:255'],
  default_value: 'Default App Name',
  sort_order: 0
};

const response = await fetch('/api/admin/system-configs/store', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(configData)
});
```

#### **PUT** `/api/admin/system-configs/{id}` - Cập nhật config
```javascript
const updateData = {
  key: 'app_name',
  value: 'Updated Application Name',
  type: 'string',
  group: 'general',
  description: 'Tên ứng dụng đã cập nhật',
  is_public: true,
  is_encrypted: false,
  status: 'active'
};

const response = await fetch('/api/admin/system-configs/1', {
  method: 'PUT',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(updateData)
});
```

#### **DELETE** `/api/admin/system-configs/{id}` - Xóa config
```javascript
const response = await fetch('/api/admin/system-configs/1', {
  method: 'DELETE',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json'
  }
});
```

### **2. Special Operations**

#### **GET** `/api/admin/system-configs/group?group=general` - Lấy configs theo group
```javascript
const response = await fetch('/api/admin/system-configs/group?group=general', {
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json'
  }
});
```

#### **GET** `/api/admin/system-configs/key?key=app_name` - Lấy config theo key
```javascript
const response = await fetch('/api/admin/system-configs/key?key=app_name', {
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json'
  }
});
```

#### **POST** `/api/admin/system-configs/bulk-update` - Cập nhật nhiều configs
```javascript
const bulkData = {
  configs: [
    {
      key: 'app_name',
      value: 'Bulk Updated App'
    },
    {
      key: 'app_version',
      value: '2.0.0'
    }
  ]
};

const response = await fetch('/api/admin/system-configs/bulk-update', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify(bulkData)
});
```

#### **POST** `/api/admin/system-configs/clear-cache` - Xóa cache
```javascript
const response = await fetch('/api/admin/system-configs/clear-cache', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer YOUR_TOKEN',
    'Accept': 'application/json'
  }
});
```

---

## 🌐 Public APIs (Không cần Authentication)

### **1. Basic Operations**

#### **GET** `/api/system-configs/` - Lấy danh sách public configs
```javascript
const response = await fetch('/api/system-configs/', {
  headers: {
    'Accept': 'application/json'
  }
});

// Query Parameters
const params = new URLSearchParams({
  group: 'general',         // Filter by group
  group: ['general', 'api'], // Multiple groups
  search: 'app',            // Search by key
  page: 1,                  // Pagination
  per_page: 10,            // Items per page
  fields: 'key,value,group' // Select fields
});
```

#### **GET** `/api/system-configs/groups` - Lấy danh sách public groups
```javascript
const response = await fetch('/api/system-configs/groups', {
  headers: {
    'Accept': 'application/json'
  }
});
```

#### **GET** `/api/system-configs/{id}` - Lấy config theo ID
```javascript
const response = await fetch('/api/system-configs/1', {
  headers: {
    'Accept': 'application/json'
  }
});
```

#### **GET** `/api/system-configs/key?key=app_name` - Lấy config theo key
```javascript
const response = await fetch('/api/system-configs/key?key=app_name', {
  headers: {
    'Accept': 'application/json'
  }
});
```

---

## 📊 Data Types & Validation

### **Config Types**
```javascript
const CONFIG_TYPES = [
  { value: 'string', label: 'Chuỗi' },
  { value: 'integer', label: 'Số nguyên' },
  { value: 'boolean', label: 'Boolean' },
  { value: 'json', label: 'JSON' },
  { value: 'array', label: 'Mảng' },
  { value: 'float', label: 'Số thực' }
];
```

### **Config Groups**
```javascript
const CONFIG_GROUPS = [
  { value: 'general', label: 'Cài đặt chung', is_public: true },
  { value: 'email', label: 'Cấu hình Email', is_public: false },
  { value: 'database', label: 'Cài đặt Database', is_public: false },
  { value: 'storage', label: 'Cấu hình lưu trữ', is_public: false },
  { value: 'security', label: 'Cài đặt bảo mật', is_public: false },
  { value: 'api', label: 'Cài đặt API', is_public: true },
  { value: 'cache', label: 'Cài đặt Cache', is_public: false },
  { value: 'notification', label: 'Cài đặt thông báo', is_public: false },
  { value: 'payment', label: 'Cài đặt thanh toán', is_public: false },
  { value: 'custom', label: 'Cài đặt tùy chỉnh', is_public: true }
];
```

### **Validation Rules**
```javascript
// Key validation
const KEY_REGEX = /^[a-zA-Z0-9._-]+$/;

// Value validation by type
const validateValue = (value, type) => {
  switch (type) {
    case 'integer':
      return Number.isInteger(Number(value));
    case 'float':
      return !isNaN(Number(value));
    case 'boolean':
      return ['true', 'false', '1', '0', 1, 0].includes(value);
    case 'json':
      try {
        JSON.parse(value);
        return true;
      } catch {
        return false;
      }
    case 'array':
      return Array.isArray(value) || isValidJSONArray(value);
    default:
      return true;
  }
};
```

---

## 🎨 Frontend Implementation Examples

### **1. React Hook cho System Config**

```javascript
// hooks/useSystemConfig.js
import { useState, useEffect, useCallback } from 'react';

export const useSystemConfig = () => {
  const [configs, setConfigs] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const fetchConfigs = useCallback(async (params = {}) => {
    setLoading(true);
    setError(null);
    
    try {
      const queryParams = new URLSearchParams(params);
      const response = await fetch(`/api/system-configs/?${queryParams}`, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const data = await response.json();
      setConfigs(data.data);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }, []);

  const getConfigByKey = useCallback(async (key) => {
    try {
      const response = await fetch(`/api/system-configs/key?key=${key}`, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const data = await response.json();
      return data.data;
    } catch (err) {
      console.error(`Error fetching config ${key}:`, err);
      return null;
    }
  }, []);

  const getConfigsByGroup = useCallback(async (group) => {
    try {
      const response = await fetch(`/api/system-configs/?group=${group}`, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const data = await response.json();
      return data.data;
    } catch (err) {
      console.error(`Error fetching configs for group ${group}:`, err);
      return [];
    }
  }, []);

  useEffect(() => {
    fetchConfigs();
  }, [fetchConfigs]);

  return {
    configs,
    loading,
    error,
    fetchConfigs,
    getConfigByKey,
    getConfigsByGroup
  };
};
```

### **2. Admin Hook cho System Config**

```javascript
// hooks/useAdminSystemConfig.js
import { useState, useCallback } from 'react';

export const useAdminSystemConfig = (token) => {
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);

  const headers = {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  };

  const createConfig = useCallback(async (configData) => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await fetch('/api/admin/system-configs/store', {
        method: 'POST',
        headers,
        body: JSON.stringify(configData)
      });
      
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to create config');
      }
      
      const data = await response.json();
      return data.data;
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  }, [headers]);

  const updateConfig = useCallback(async (id, configData) => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await fetch(`/api/admin/system-configs/${id}`, {
        method: 'PUT',
        headers,
        body: JSON.stringify(configData)
      });
      
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to update config');
      }
      
      const data = await response.json();
      return data.data;
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  }, [headers]);

  const deleteConfig = useCallback(async (id) => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await fetch(`/api/admin/system-configs/${id}`, {
        method: 'DELETE',
        headers
      });
      
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to delete config');
      }
      
      return true;
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  }, [headers]);

  const bulkUpdateConfigs = useCallback(async (configs) => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await fetch('/api/admin/system-configs/bulk-update', {
        method: 'POST',
        headers,
        body: JSON.stringify({ configs })
      });
      
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to bulk update configs');
      }
      
      const data = await response.json();
      return data.data;
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  }, [headers]);

  const clearCache = useCallback(async () => {
    setLoading(true);
    setError(null);
    
    try {
      const response = await fetch('/api/admin/system-configs/clear-cache', {
        method: 'POST',
        headers
      });
      
      if (!response.ok) {
        const errorData = await response.json();
        throw new Error(errorData.message || 'Failed to clear cache');
      }
      
      return true;
    } catch (err) {
      setError(err.message);
      throw err;
    } finally {
      setLoading(false);
    }
  }, [headers]);

  return {
    loading,
    error,
    createConfig,
    updateConfig,
    deleteConfig,
    bulkUpdateConfigs,
    clearCache
  };
};
```

### **3. React Component Examples**

#### **Config List Component**
```jsx
// components/SystemConfigList.jsx
import React, { useState, useEffect } from 'react';
import { useSystemConfig } from '../hooks/useSystemConfig';

const SystemConfigList = () => {
  const { configs, loading, error, fetchConfigs } = useSystemConfig();
  const [filters, setFilters] = useState({
    group: '',
    search: '',
    page: 1
  });

  useEffect(() => {
    fetchConfigs(filters);
  }, [filters, fetchConfigs]);

  const handleFilterChange = (key, value) => {
    setFilters(prev => ({
      ...prev,
      [key]: value,
      page: 1 // Reset to first page when filtering
    }));
  };

  if (loading) return <div>Loading...</div>;
  if (error) return <div>Error: {error}</div>;

  return (
    <div className="config-list">
      <div className="filters">
        <input
          type="text"
          placeholder="Search configs..."
          value={filters.search}
          onChange={(e) => handleFilterChange('search', e.target.value)}
        />
        <select
          value={filters.group}
          onChange={(e) => handleFilterChange('group', e.target.value)}
        >
          <option value="">All Groups</option>
          <option value="general">General</option>
          <option value="api">API</option>
          <option value="custom">Custom</option>
        </select>
      </div>

      <div className="config-grid">
        {configs.map(config => (
          <div key={config.id} className="config-card">
            <h3>{config.key}</h3>
            <p className="value">{config.value}</p>
            <p className="group">{config.group}</p>
            <p className="description">{config.description}</p>
            <div className="badges">
              {config.is_public && <span className="badge public">Public</span>}
              {config.is_encrypted && <span className="badge encrypted">Encrypted</span>}
              <span className="badge type">{config.type}</span>
            </div>
          </div>
        ))}
      </div>
    </div>
  );
};

export default SystemConfigList;
```

#### **Config Form Component**
```jsx
// components/SystemConfigForm.jsx
import React, { useState } from 'react';
import { useAdminSystemConfig } from '../hooks/useAdminSystemConfig';

const CONFIG_TYPES = [
  { value: 'string', label: 'String' },
  { value: 'integer', label: 'Integer' },
  { value: 'boolean', label: 'Boolean' },
  { value: 'json', label: 'JSON' },
  { value: 'array', label: 'Array' },
  { value: 'float', label: 'Float' }
];

const CONFIG_GROUPS = [
  { value: 'general', label: 'General' },
  { value: 'email', label: 'Email' },
  { value: 'api', label: 'API' },
  { value: 'custom', label: 'Custom' }
];

const SystemConfigForm = ({ config = null, onSuccess, token }) => {
  const { createConfig, updateConfig, loading, error } = useAdminSystemConfig(token);
  const [formData, setFormData] = useState({
    key: config?.key || '',
    value: config?.value || '',
    type: config?.type || 'string',
    group: config?.group || 'general',
    description: config?.description || '',
    is_public: config?.is_public || false,
    is_encrypted: config?.is_encrypted || false,
    status: config?.status || 'active',
    validation_rules: config?.validation_rules || [],
    default_value: config?.default_value || '',
    sort_order: config?.sort_order || 0
  });

  const handleSubmit = async (e) => {
    e.preventDefault();
    
    try {
      if (config) {
        await updateConfig(config.id, formData);
      } else {
        await createConfig(formData);
      }
      onSuccess?.();
    } catch (err) {
      console.error('Form submission error:', err);
    }
  };

  const handleInputChange = (field, value) => {
    setFormData(prev => ({
      ...prev,
      [field]: value
    }));
  };

  const renderValueInput = () => {
    switch (formData.type) {
      case 'boolean':
        return (
          <select
            value={formData.value}
            onChange={(e) => handleInputChange('value', e.target.value)}
          >
            <option value="true">True</option>
            <option value="false">False</option>
          </select>
        );
      case 'json':
        return (
          <textarea
            value={formData.value}
            onChange={(e) => handleInputChange('value', e.target.value)}
            placeholder="Enter JSON..."
            rows={4}
          />
        );
      case 'array':
        return (
          <textarea
            value={formData.value}
            onChange={(e) => handleInputChange('value', e.target.value)}
            placeholder="Enter array as JSON..."
            rows={3}
          />
        );
      default:
        return (
          <input
            type={formData.type === 'integer' || formData.type === 'float' ? 'number' : 'text'}
            value={formData.value}
            onChange={(e) => handleInputChange('value', e.target.value)}
          />
        );
    }
  };

  return (
    <form onSubmit={handleSubmit} className="config-form">
      {error && <div className="error">{error}</div>}
      
      <div className="form-group">
        <label>Key *</label>
        <input
          type="text"
          value={formData.key}
          onChange={(e) => handleInputChange('key', e.target.value)}
          pattern="[a-zA-Z0-9._-]+"
          required
        />
      </div>

      <div className="form-group">
        <label>Type *</label>
        <select
          value={formData.type}
          onChange={(e) => handleInputChange('type', e.target.value)}
        >
          {CONFIG_TYPES.map(type => (
            <option key={type.value} value={type.value}>
              {type.label}
            </option>
          ))}
        </select>
      </div>

      <div className="form-group">
        <label>Group *</label>
        <select
          value={formData.group}
          onChange={(e) => handleInputChange('group', e.target.value)}
        >
          {CONFIG_GROUPS.map(group => (
            <option key={group.value} value={group.value}>
              {group.label}
            </option>
          ))}
        </select>
      </div>

      <div className="form-group">
        <label>Value</label>
        {renderValueInput()}
      </div>

      <div className="form-group">
        <label>Description</label>
        <textarea
          value={formData.description}
          onChange={(e) => handleInputChange('description', e.target.value)}
          rows={3}
        />
      </div>

      <div className="form-group">
        <label>
          <input
            type="checkbox"
            checked={formData.is_public}
            onChange={(e) => handleInputChange('is_public', e.target.checked)}
          />
          Is Public
        </label>
      </div>

      <div className="form-group">
        <label>
          <input
            type="checkbox"
            checked={formData.is_encrypted}
            onChange={(e) => handleInputChange('is_encrypted', e.target.checked)}
          />
          Is Encrypted
        </label>
      </div>

      <div className="form-group">
        <label>Status</label>
        <select
          value={formData.status}
          onChange={(e) => handleInputChange('status', e.target.value)}
        >
          <option value="active">Active</option>
          <option value="inactive">Inactive</option>
        </select>
      </div>

      <button type="submit" disabled={loading}>
        {loading ? 'Saving...' : (config ? 'Update' : 'Create')}
      </button>
    </form>
  );
};

export default SystemConfigForm;
```

### **4. Vue.js Implementation**

```javascript
// composables/useSystemConfig.js
import { ref, computed } from 'vue';

export function useSystemConfig() {
  const configs = ref([]);
  const loading = ref(false);
  const error = ref(null);

  const fetchConfigs = async (params = {}) => {
    loading.value = true;
    error.value = null;
    
    try {
      const queryParams = new URLSearchParams(params);
      const response = await fetch(`/api/system-configs/?${queryParams}`, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const data = await response.json();
      configs.value = data.data;
    } catch (err) {
      error.value = err.message;
    } finally {
      loading.value = false;
    }
  };

  const getConfigByKey = async (key) => {
    try {
      const response = await fetch(`/api/system-configs/key?key=${key}`, {
        headers: {
          'Accept': 'application/json'
        }
      });
      
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      
      const data = await response.json();
      return data.data;
    } catch (err) {
      console.error(`Error fetching config ${key}:`, err);
      return null;
    }
  };

  return {
    configs: computed(() => configs.value),
    loading: computed(() => loading.value),
    error: computed(() => error.value),
    fetchConfigs,
    getConfigByKey
  };
}
```

---

## 📱 Response Format

### **Success Response**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "key": "app_name",
    "value": "My Application",
    "type": "string",
    "group": "general",
    "description": "Tên ứng dụng",
    "is_public": true,
    "is_encrypted": false,
    "status": "active",
    "validation_rules": ["required", "max:255"],
    "default_value": "Default App Name",
    "sort_order": 0,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  },
  "message": "Thành công",
  "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

### **Error Response**
```json
{
  "success": false,
  "data": null,
  "message": "Dữ liệu không hợp lệ",
  "errors": {
    "key": ["Khóa cấu hình đã tồn tại"],
    "value": ["Giá trị phải là số nguyên"]
  },
  "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

### **List Response**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "key": "app_name",
      "value": "My Application",
      "type": "string",
      "group": "general",
      "description": "Tên ứng dụng",
      "is_public": true,
      "is_encrypted": false,
      "status": "active"
    }
  ],
  "meta": {
    "current_page": 1,
    "per_page": 15,
    "total": 1,
    "last_page": 1
  },
  "message": "Lấy danh sách thành công",
  "timestamp": "2024-01-01T00:00:00.000000Z"
}
```

---

## 🎯 Best Practices

### **1. Caching Strategy**
```javascript
// Implement client-side caching
const configCache = new Map();
const CACHE_TTL = 5 * 60 * 1000; // 5 minutes

const getCachedConfig = (key) => {
  const cached = configCache.get(key);
  if (cached && Date.now() - cached.timestamp < CACHE_TTL) {
    return cached.data;
  }
  return null;
};

const setCachedConfig = (key, data) => {
  configCache.set(key, {
    data,
    timestamp: Date.now()
  });
};
```

### **2. Error Handling**
```javascript
const handleApiError = (error, response) => {
  if (response?.status === 401) {
    // Handle unauthorized
    redirectToLogin();
  } else if (response?.status === 422) {
    // Handle validation errors
    return response.errors;
  } else if (response?.status >= 500) {
    // Handle server errors
    showNotification('Server error. Please try again later.', 'error');
  }
};
```

### **3. Type Safety (TypeScript)**
```typescript
interface SystemConfig {
  id: number;
  key: string;
  value: string | number | boolean | object;
  type: 'string' | 'integer' | 'boolean' | 'json' | 'array' | 'float';
  group: string;
  description?: string;
  is_public: boolean;
  is_encrypted: boolean;
  status: 'active' | 'inactive';
  validation_rules?: string[];
  default_value?: string;
  sort_order: number;
  created_at: string;
  updated_at: string;
}

interface ApiResponse<T> {
  success: boolean;
  data: T;
  message: string;
  timestamp: string;
  errors?: Record<string, string[]>;
}
```

### **4. Performance Optimization**
```javascript
// Debounce search
const debouncedSearch = useMemo(
  () => debounce((searchTerm) => {
    fetchConfigs({ search: searchTerm });
  }, 300),
  [fetchConfigs]
);

// Virtual scrolling for large lists
const VirtualizedConfigList = ({ configs }) => {
  return (
    <FixedSizeList
      height={600}
      itemCount={configs.length}
      itemSize={80}
      itemData={configs}
    >
      {ConfigItem}
    </FixedSizeList>
  );
};
```

---

## 🧪 Testing Examples

### **Unit Tests**
```javascript
// tests/systemConfig.test.js
import { render, screen, fireEvent, waitFor } from '@testing-library/react';
import { SystemConfigForm } from '../components/SystemConfigForm';

describe('SystemConfigForm', () => {
  test('should validate required fields', async () => {
    render(<SystemConfigForm token="test-token" />);
    
    const submitButton = screen.getByRole('button', { name: /create/i });
    fireEvent.click(submitButton);
    
    await waitFor(() => {
      expect(screen.getByText(/key is required/i)).toBeInTheDocument();
    });
  });

  test('should handle form submission', async () => {
    const mockOnSuccess = jest.fn();
    render(<SystemConfigForm onSuccess={mockOnSuccess} token="test-token" />);
    
    fireEvent.change(screen.getByLabelText(/key/i), {
      target: { value: 'test_key' }
    });
    fireEvent.change(screen.getByLabelText(/value/i), {
      target: { value: 'test_value' }
    });
    
    fireEvent.click(screen.getByRole('button', { name: /create/i }));
    
    await waitFor(() => {
      expect(mockOnSuccess).toHaveBeenCalled();
    });
  });
});
```

### **Integration Tests**
```javascript
// tests/api/systemConfig.test.js
import { rest } from 'msw';
import { setupServer } from 'msw/node';

const server = setupServer(
  rest.get('/api/system-configs/', (req, res, ctx) => {
    return res(
      ctx.json({
        success: true,
        data: [
          {
            id: 1,
            key: 'app_name',
            value: 'Test App',
            type: 'string',
            group: 'general'
          }
        ],
        message: 'Success'
      })
    );
  })
);

beforeAll(() => server.listen());
afterEach(() => server.resetHandlers());
afterAll(() => server.close());
```

---

## 🚀 Deployment Checklist

### **Environment Variables**
```bash
# .env
API_BASE_URL=https://your-api-domain.com
ADMIN_TOKEN=your-admin-token
CACHE_TTL=300000
```

### **Build Configuration**
```javascript
// webpack.config.js or vite.config.js
module.exports = {
  define: {
    'process.env.API_BASE_URL': JSON.stringify(process.env.API_BASE_URL),
    'process.env.CACHE_TTL': JSON.stringify(process.env.CACHE_TTL)
  }
};
```

### **Production Optimizations**
- Enable compression
- Implement service worker for offline caching
- Use CDN for static assets
- Monitor API performance
- Set up error tracking

---

## 📞 Support & Troubleshooting

### **Common Issues**

1. **CORS Errors**
   - Ensure API server allows your domain
   - Check preflight requests

2. **Authentication Issues**
   - Verify token format and expiration
   - Check middleware configuration

3. **Cache Issues**
   - Clear browser cache
   - Use API cache clear endpoint

4. **Validation Errors**
   - Check data types match config type
   - Verify required fields

### **Debug Tools**
```javascript
// Enable debug mode
const DEBUG = process.env.NODE_ENV === 'development';

const debugLog = (message, data) => {
  if (DEBUG) {
    console.log(`[SystemConfig] ${message}`, data);
  }
};
```

---

## 📚 Additional Resources

- [API Documentation](./API_TEST_ENDPOINTS.md)
- [Backend System Config Service](../app/Services/Core/SystemConfig/)
- [Database Schema](../database/migrations/)
- [Enum Definitions](../app/Enums/)

---

**Happy Coding! 🚀**

*Nếu có thắc mắc gì, hãy liên hệ team Backend để được hỗ trợ.*


