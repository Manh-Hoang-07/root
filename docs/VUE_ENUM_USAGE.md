# Vue Enum Usage Guide

## Tổng quan

Hệ thống enum trong Vue sử dụng **Hybrid Approach** kết hợp:
- **Static Enums**: Hardcode trong code (nhanh nhất)
- **Dynamic Cache**: Cache từ API (linh hoạt)
- **Fallback**: Tự động fallback khi API lỗi

## 🚀 Cách sử dụng

### 1. **Sử dụng Composable (Khuyến nghị)**

```vue
<template>
  <div>
    <select v-model="selectedStatus">
      <option v-for="status in userStatuses" :key="status.id" :value="status.value">
        {{ status.label }}
      </option>
    </select>
    
    <p>Status: {{ getEnumLabel('user_status', selectedStatus) }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useFastEnums } from '@/composables/useFastEnums'

const { getEnum, getEnumSync, getEnumLabel } = useFastEnums()

const selectedStatus = ref(1)
const userStatuses = ref([])

onMounted(async () => {
  // Load enum từ API (với cache)
  userStatuses.value = await getEnum('user_status')
  
  // Hoặc lấy ngay từ static (nhanh nhất)
  // userStatuses.value = getEnumSync('user_status')
})
</script>
```

### 2. **Sử dụng Pinia Store**

```vue
<template>
  <div>
    <select v-model="selectedStatus">
      <option v-for="status in userStatuses" :key="status.id" :value="status.value">
        {{ status.label }}
      </option>
    </select>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useEnumStore } from '@/stores/enumStore'

const enumStore = useEnumStore()
const selectedStatus = ref(1)

// Reactive enum data
const userStatuses = computed(() => enumStore.getEnum('user_status'))

onMounted(async () => {
  // Load từ API nếu chưa có
  if (!enumStore.isCached('user_status')) {
    await enumStore.loadEnum('user_status')
  }
})
</script>
```

### 3. **Sử dụng Component EnumSelect**

```vue
<template>
  <div>
    <EnumSelect 
      v-model="selectedStatus"
      type="user_status"
      placeholder="Chọn trạng thái"
    />
    
    <EnumSelect 
      v-model="selectedGender"
      type="gender"
      :use-static="false"
      :force-refresh="true"
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import EnumSelect from '@/components/Core/EnumSelect.vue'

const selectedStatus = ref(1)
const selectedGender = ref(2)
</script>
```

### 4. **Sử dụng Static Enums trực tiếp**

```vue
<template>
  <div>
    <span>{{ getEnumLabel('user_status', user.status) }}</span>
  </div>
</template>

<script setup>
import { getEnumLabel } from '@/constants/enums'

const user = { status: 1 }
</script>
```

## 📋 API Reference

### Composable: `useFastEnums()`

```javascript
const {
  // State
  loading,
  error,
  
  // Methods
  getEnum(type, useStatic = true, forceRefresh = false),
  getEnumSync(type),
  clearCache(type),
  refreshEnum(type),
  loadAllEnums(types, useStatic),
  hasCache(type),
  getCacheInfo(),
  
  // Static enums
  staticEnums
} = useFastEnums()
```

### Store: `useEnumStore()`

```javascript
const {
  // State
  enums,
  loading,
  error,
  
  // Getters
  getEnum(type),
  getEnumLabel(type, value),
  getEnumName(type, value),
  
  // Actions
  loadEnum(type, forceRefresh),
  loadAllEnums(types, forceRefresh),
  clearCache(type),
  refreshEnum(type),
  refreshAllEnums(),
  getCacheInfo(),
  isCached(type),
  initialize()
} = useEnumStore()
```

### Constants: `@/constants/enums`

```javascript
import { 
  ENUMS,
  getEnum,
  getEnumLabel,
  getEnumName,
  getEnumById,
  getEnumByValue
} from '@/constants/enums'
```

## 🎯 Các trường hợp sử dụng

### 1. **Form Select**
```vue
<EnumSelect 
  v-model="form.status"
  type="user_status"
  placeholder="Chọn trạng thái"
/>
```

### 2. **Display Label**
```vue
<template>
  <span>{{ getEnumLabel('user_status', user.status) }}</span>
</template>
```

### 3. **Filter Options**
```vue
<template>
  <select v-model="filter.status">
    <option value="">Tất cả</option>
    <option v-for="status in userStatuses" :key="status.id" :value="status.value">
      {{ status.label }}
    </option>
  </select>
</template>
```

### 4. **Table Column**
```vue
<template>
  <td>{{ getEnumLabel('user_status', row.status) }}</td>
</template>
```

## ⚡ Performance Tips

### 1. **Sử dụng Static cho UI cơ bản**
```javascript
// Nhanh nhất - không cần API call
const statuses = getEnumSync('user_status')
```

### 2. **Preload enums cần thiết**
```javascript
// Load tất cả enum khi app start
onMounted(async () => {
  await loadAllEnums(['user_status', 'gender', 'product_status'])
})
```

### 3. **Cache management**
```javascript
// Clear cache khi cần refresh
clearCache('user_status')

// Refresh từ API
refreshEnum('user_status')
```

### 4. **Error handling**
```javascript
// Tự động fallback về static enum
const statuses = await getEnum('user_status') // Fallback nếu API lỗi
```

## 🔧 Configuration

### Cache timeout
```javascript
// Trong useFastEnums.js
const CACHE_TIMEOUT = 24 * 60 * 60 * 1000 // 24 giờ
```

### Static enums
```javascript
// Trong constants/enums.js
export const ENUMS = {
  user_status: [
    { id: 1, name: 'Active', value: 1, label: 'Hoạt động' },
    // ...
  ]
}
```

## 📊 Performance Comparison

| Method | Speed | Use Case |
|--------|-------|----------|
| `getEnumSync()` | ~0.001ms | UI rendering, forms |
| `getEnum()` (cached) | ~0.1ms | Dynamic data |
| `getEnum()` (API) | ~10-50ms | Fresh data |
| Component | ~0.1ms | Reusable UI |

## 🚨 Lưu ý

1. **Static enums** được hardcode, cần update khi thay đổi enum
2. **Dynamic cache** tự động expire sau 24 giờ
3. **Fallback** tự động về static enum khi API lỗi
4. **Component** tự động handle loading và error states
5. **Store** reactive, tự động update UI khi data thay đổi 