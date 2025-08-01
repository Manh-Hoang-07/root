import fs from 'fs';
import path from 'path';

// Mapping từ enum type sang static enum key
const enumMapping = {
  'UserStatus': 'user_status',
  'Gender': 'gender', 
  'BasicStatus': 'basic_status',
  'RoleStatus': 'role_status',
  'ProductStatus': 'product_status',
  'OrderStatus': 'order_status',
  'VariantStatus': 'variant_status'
};

// Files cần thay thế
const filesToReplace = [
  'resources/js/views/admin/Users/index.vue',
  'resources/js/views/admin/Warehouses/edit.vue',
  'resources/js/views/admin/Warehouses/create.vue',
  'resources/js/views/admin/Roles/index.vue',
  'resources/js/views/admin/Products/variant-form.vue',
  'resources/js/views/admin/Products/form.vue',
  'resources/js/views/admin/Permissions/edit.vue',
  'resources/js/views/admin/Permissions/create.vue',
  'resources/js/views/admin/Orders/edit.vue',
  'resources/js/views/admin/Orders/form.vue',
  'resources/js/views/admin/Orders/create.vue',
  'resources/js/views/admin/Categories/edit.vue',
  'resources/js/views/admin/Categories/create.vue',
  'resources/js/views/admin/Brands/edit.vue',
  'resources/js/views/admin/Brands/create.vue',
  'resources/js/views/admin/Attributes/edit.vue',
  'resources/js/views/admin/Attributes/create.vue',
  'resources/js/views/admin/AttributeValues/create.vue',
  'resources/js/views/admin/AttributeValues/edit.vue'
];

function replaceEnumApiCalls() {
  filesToReplace.forEach(filePath => {
    if (fs.existsSync(filePath)) {
      let content = fs.readFileSync(filePath, 'utf8');
      let modified = false;

      // Thêm import nếu chưa có
      if (!content.includes('getEnumSync')) {
        const importMatch = content.match(/import.*from.*['"]vue['"]/);
        if (importMatch) {
          content = content.replace(
            importMatch[0],
            `${importMatch[0]}\nimport { getEnumSync } from '@/composables/useFastEnums'`
          );
          modified = true;
        }
      }

      // Thay thế các API calls
      Object.entries(enumMapping).forEach(([apiType, staticType]) => {
        const regex = new RegExp(`endpoints\\.enums\\('${apiType}'\\)`, 'g');
        if (regex.test(content)) {
          content = content.replace(regex, `getEnumSync('${staticType}')`);
          modified = true;
        }
      });

      // Thay thế axios.get và apiClient.get calls
      Object.entries(enumMapping).forEach(([apiType, staticType]) => {
        const patterns = [
          new RegExp(`const response = await axios\\.get\\(endpoints\\.enums\\('${apiType}'\\)\\)`, 'g'),
          new RegExp(`const response = await apiClient\\.get\\(endpoints\\.enums\\('${apiType}'\\)\\)`, 'g'),
          new RegExp(`await axios\\.get\\(endpoints\\.enums\\('${apiType}'\\)\\)`, 'g'),
          new RegExp(`await apiClient\\.get\\(endpoints\\.enums\\('${apiType}'\\)\\)`, 'g')
        ];

        patterns.forEach(pattern => {
          if (pattern.test(content)) {
            content = content.replace(pattern, `const response = { data: getEnumSync('${staticType}') }`);
            modified = true;
          }
        });
      });

      // Thay thế try-catch blocks
      Object.entries(enumMapping).forEach(([apiType, staticType]) => {
        const tryCatchPattern = new RegExp(
          `async function fetchStatusOptions\\(\\) \\{[\\s\\S]*?const response = await [^}]+endpoints\\.enums\\('${apiType}'\\)[^}]+\\}[\\s\\S]*?\\}`,
          'g'
        );
        
        if (tryCatchPattern.test(content)) {
          content = content.replace(tryCatchPattern, 
            `function fetchStatusOptions() {
  // Sử dụng static enum thay vì gọi API
  const ${staticType} = getEnumSync('${staticType}')
  statusOptions.value = ${staticType}.reduce((acc, status) => {
    acc[status.name.toLowerCase()] = status.label
    return acc
  }, {})
}`
          );
          modified = true;
        }
      });

      if (modified) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`✅ Updated: ${filePath}`);
      } else {
        console.log(`⏭️  No changes needed: ${filePath}`);
      }
    } else {
      console.log(`❌ File not found: ${filePath}`);
    }
  });
}

// Chạy script
console.log('🔄 Replacing enum API calls with static enums...');
replaceEnumApiCalls();
console.log('✅ Done!'); 