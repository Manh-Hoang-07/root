import fs from 'fs';

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

// Files cần sửa
const filesToFix = [
  'resources/js/views/admin/Warehouses/edit.vue',
  'resources/js/views/admin/Permissions/edit.vue',
  'resources/js/views/admin/Warehouses/create.vue',
  'resources/js/views/admin/Permissions/create.vue',
  'resources/js/views/admin/Roles/index.vue',
  'resources/js/views/admin/Products/variant-form.vue',
  'resources/js/views/admin/Orders/create.vue',
  'resources/js/views/admin/Orders/edit.vue',
  'resources/js/views/admin/Categories/edit.vue',
  'resources/js/views/admin/Categories/create.vue',
  'resources/js/views/admin/AttributeValues/create.vue',
  'resources/js/views/admin/AttributeValues/edit.vue',
  'resources/js/views/admin/Brands/edit.vue',
  'resources/js/views/admin/Brands/create.vue',
  'resources/js/views/admin/Attributes/edit.vue',
  'resources/js/views/admin/Attributes/create.vue'
];

function fixEnumImports() {
  filesToFix.forEach(filePath => {
    if (fs.existsSync(filePath)) {
      let content = fs.readFileSync(filePath, 'utf8');
      let modified = false;

      // Thay thế axios.get(getEnumSync(...)) thành getEnumSync(...)
      Object.entries(enumMapping).forEach(([apiType, staticType]) => {
        const patterns = [
          new RegExp(`const response = await axios\\.get\\(getEnumSync\\('${staticType}'\\)\\)`, 'g'),
          new RegExp(`await axios\\.get\\(getEnumSync\\('${staticType}'\\)\\)`, 'g')
        ];

        patterns.forEach(pattern => {
          if (pattern.test(content)) {
            content = content.replace(pattern, `const response = { data: getEnumSync('${staticType}') }`);
            modified = true;
          }
        });
      });

      // Thay thế apiClient.get(getEnumSync(...)) thành getEnumSync(...)
      Object.entries(enumMapping).forEach(([apiType, staticType]) => {
        const patterns = [
          new RegExp(`const response = await apiClient\\.get\\(getEnumSync\\('${staticType}'\\)\\)`, 'g'),
          new RegExp(`await apiClient\\.get\\(getEnumSync\\('${staticType}'\\)\\)`, 'g')
        ];

        patterns.forEach(pattern => {
          if (pattern.test(content)) {
            content = content.replace(pattern, `const response = { data: getEnumSync('${staticType}') }`);
            modified = true;
          }
        });
      });

      // Thay thế try-catch blocks với axios.get(getEnumSync(...))
      Object.entries(enumMapping).forEach(([apiType, staticType]) => {
        const tryCatchPattern = new RegExp(
          `async function fetchStatusOptions\\(\\) \\{[\\s\\S]*?const response = await [^}]+getEnumSync\\('${staticType}'\\)[^}]+\\}[\\s\\S]*?\\}`,
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
        console.log(`✅ Fixed: ${filePath}`);
      } else {
        console.log(`⏭️  No changes needed: ${filePath}`);
      }
    } else {
      console.log(`❌ File not found: ${filePath}`);
    }
  });
}

// Chạy script
console.log('🔄 Fixing enum import errors...');
fixEnumImports();
console.log('✅ Done!'); 