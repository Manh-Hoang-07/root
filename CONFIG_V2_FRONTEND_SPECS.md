# Specs Frontend - SystemConfigV2

## Cấu trúc giao diện

### 1. Layout chính
```
┌─────────────────────────────────────────────────────────┐
│                    Cấu hình hệ thống                    │
├─────────────────────────────────────────────────────────┤
│ [Tab: Cấu hình chung] [Tab: Cấu hình email]            │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  Nội dung tab được chọn                                │
│                                                         │
├─────────────────────────────────────────────────────────┤
│ [Lưu] [Hủy] [Reset về mặc định]                        │
└─────────────────────────────────────────────────────────┘
```

### 2. Tab "Cấu hình chung"

#### 2.1. Section "Thông tin cơ bản"
```
┌─ Thông tin cơ bản ─────────────────────────────────────┐
│ Tên ứng dụng: [Laravel App                    ]        │
│ Tên website:  [Website Demo                   ]        │
│ Logo website: [📁 Chọn file] [/images/logo.png]       │
│ Favicon:      [📁 Chọn file] [/images/favicon.ico]    │
└────────────────────────────────────────────────────────┘
```

#### 2.2. Section "Thông tin liên hệ"
```
┌─ Thông tin liên hệ ────────────────────────────────────┐
│ Email:        [admin@demo.com                 ]        │
│ Số điện thoại: [0123456789                    ]        │
│ Địa chỉ:      [123 Đường ABC, Quận XYZ, TP.HCM]       │
│               [                                ]       │
└────────────────────────────────────────────────────────┘
```

#### 2.3. Section "Cấu hình khác"
```
┌─ Cấu hình khác ────────────────────────────────────────┐
│ Mô tả website: [Website bán hàng trực tuyến...]       │
│                [                                ]       │
│ Múi giờ:       [Asia/Ho_Chi_Minh ▼]                   │
│ Ngôn ngữ:      [Tiếng Việt ▼]                         │
│ Chế độ bảo trì: [○] Tắt  [●] Bật                      │
│ Số item/trang: [20] (1-100)                           │
└────────────────────────────────────────────────────────┘
```

### 3. Tab "Cấu hình email"

#### 3.1. Section "SMTP Settings"
```
┌─ SMTP Settings ────────────────────────────────────────┐
│ SMTP Host:     [smtp.gmail.com                ]        │
│ SMTP Port:     [587] (1-65535)                        │
│ Username:      [your-email@gmail.com          ]        │
│ Password:      [••••••••••••••••••••••••••••••]        │
│ Encryption:    [TLS ▼] (TLS/SSL/None)                 │
└────────────────────────────────────────────────────────┘
```

#### 3.2. Section "Thông tin gửi email"
```
┌─ Thông tin gửi email ──────────────────────────────────┐
│ Email gửi đi:  [noreply@example.com           ]        │
│ Tên người gửi: [Laravel App                   ]        │
└────────────────────────────────────────────────────────┘
```

## Component Structure

### 1. Main Component: ConfigManager
```typescript
interface ConfigManagerProps {
  onSave?: (data: any) => void;
  onCancel?: () => void;
  onReset?: () => void;
}
```

### 2. Tab Component: ConfigTab
```typescript
interface ConfigTabProps {
  activeTab: 'general' | 'email';
  onTabChange: (tab: 'general' | 'email') => void;
}
```

### 3. Form Component: GeneralConfigForm
```typescript
interface GeneralConfigFormProps {
  data: GeneralConfig;
  onChange: (field: string, value: any) => void;
  onSave: () => void;
  loading?: boolean;
}

interface GeneralConfig {
  app_name: string;
  site_name: string;
  site_logo: string;
  site_favicon: string;
  site_email: string;
  site_phone: string;
  site_address: string;
  site_description: string;
  timezone: string;
  language: string;
  maintenance_mode: boolean;
  items_per_page: number;
}
```

### 4. Form Component: EmailConfigForm
```typescript
interface EmailConfigFormProps {
  data: EmailConfig;
  onChange: (field: string, value: any) => void;
  onSave: () => void;
  loading?: boolean;
}

interface EmailConfig {
  smtp_host: string;
  smtp_port: number;
  smtp_username: string;
  smtp_password: string;
  smtp_encryption: 'tls' | 'ssl' | 'none';
  from_email: string;
  from_name: string;
}
```

## Form Fields Specification

### General Config Fields

| Field | Type | Component | Validation | Default |
|-------|------|-----------|------------|---------|
| `app_name` | string | Input | max 255 chars | "Laravel App" |
| `site_name` | string | Input | max 255 chars | "Website Demo" |
| `site_logo` | string | FileUpload | max 500 chars | "/images/logo.png" |
| `site_favicon` | string | FileUpload | max 500 chars | "/images/favicon.ico" |
| `site_email` | string | Input (email) | email format | "admin@demo.com" |
| `site_phone` | string | Input (tel) | max 20 chars | "0123456789" |
| `site_address` | string | Textarea | max 1000 chars | "" |
| `site_description` | string | Textarea | max 2000 chars | "" |
| `timezone` | string | Select | valid timezone | "Asia/Ho_Chi_Minh" |
| `language` | string | Select | 2 chars | "vi" |
| `maintenance_mode` | boolean | Toggle | - | false |
| `items_per_page` | number | Input (number) | 1-100 | 20 |

### Email Config Fields

| Field | Type | Component | Validation | Default |
|-------|------|-----------|------------|---------|
| `smtp_host` | string | Input | max 255 chars | "smtp.gmail.com" |
| `smtp_port` | number | Input (number) | 1-65535 | 587 |
| `smtp_username` | string | Input | max 255 chars | "" |
| `smtp_password` | string | Input (password) | max 255 chars | "" |
| `smtp_encryption` | string | Select | enum | "tls" |
| `from_email` | string | Input (email) | email format | "noreply@example.com" |
| `from_name` | string | Input | max 255 chars | "Laravel App" |

## Dropdown Options

### Timezone Options
```javascript
const timezoneOptions = [
  { value: 'Asia/Ho_Chi_Minh', label: 'Asia/Ho_Chi_Minh (UTC+7)' },
  { value: 'Asia/Bangkok', label: 'Asia/Bangkok (UTC+7)' },
  { value: 'Asia/Singapore', label: 'Asia/Singapore (UTC+8)' },
  { value: 'UTC', label: 'UTC (UTC+0)' },
  // ... thêm các timezone khác
];
```

### Language Options
```javascript
const languageOptions = [
  { value: 'vi', label: 'Tiếng Việt' },
  { value: 'en', label: 'English' },
  { value: 'ja', label: '日本語' },
  { value: 'ko', label: '한국어' },
];
```

### SMTP Encryption Options
```javascript
const encryptionOptions = [
  { value: 'tls', label: 'TLS' },
  { value: 'ssl', label: 'SSL' },
  { value: 'none', label: 'None' },
];
```

## API Integration

### 1. Load Data (Public - Không cần authentication)
```typescript
// Load general config
const loadGeneralConfig = async () => {
  const response = await fetch('/api/config-v2/general');
  const data = await response.json();
  return data.data;
};

// Load email config
const loadEmailConfig = async () => {
  const response = await fetch('/api/config-v2/email');
  const data = await response.json();
  return data.data;
};
```

### 2. Save Data (Admin only - Cần authentication)
```typescript
// Save general config (Admin only)
const saveGeneralConfig = async (data: GeneralConfig, adminToken: string) => {
  const response = await fetch('/api/admin/config-v2/general', {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${adminToken}`
    },
    body: JSON.stringify(data)
  });
  return response.json();
};

// Save email config (Admin only)
const saveEmailConfig = async (data: EmailConfig, adminToken: string) => {
  const response = await fetch('/api/admin/config-v2/email', {
    method: 'POST',
    headers: { 
      'Content-Type': 'application/json',
      'Authorization': `Bearer ${adminToken}`
    },
    body: JSON.stringify(data)
  });
  return response.json();
};
```

## State Management

### 1. State Structure
```typescript
interface ConfigState {
  general: {
    data: GeneralConfig;
    loading: boolean;
    error: string | null;
    dirty: boolean;
  };
  email: {
    data: EmailConfig;
    loading: boolean;
    error: string | null;
    dirty: boolean;
  };
  activeTab: 'general' | 'email';
  saving: boolean;
}
```

### 2. Actions
```typescript
const actions = {
  loadGeneralConfig: () => void;
  loadEmailConfig: () => void;
  updateGeneralField: (field: string, value: any) => void;
  updateEmailField: (field: string, value: any) => void;
  saveGeneralConfig: () => Promise<void>;
  saveEmailConfig: () => Promise<void>;
  resetGeneralConfig: () => void;
  resetEmailConfig: () => void;
  setActiveTab: (tab: 'general' | 'email') => void;
};
```

## UI/UX Guidelines

### 1. Layout
- Sử dụng 2 cột cho form (label bên trái, input bên phải)
- Khoảng cách giữa các field: 16px
- Padding section: 24px
- Border radius: 8px

### 2. Colors
- Primary: #007bff
- Success: #28a745
- Error: #dc3545
- Warning: #ffc107
- Background: #f8f9fa
- Border: #dee2e6

### 3. Typography
- Heading: 18px, font-weight: 600
- Label: 14px, font-weight: 500
- Input: 14px, font-weight: 400
- Helper text: 12px, color: #6c757d

### 4. Interactions
- Hover effects trên buttons
- Focus states cho inputs
- Loading spinners khi đang save
- Toast notifications cho success/error
- Confirmation dialog khi reset

### 5. Responsive
- Mobile: 1 cột, full width
- Tablet: 1 cột, max-width 600px
- Desktop: 2 cột, max-width 800px
