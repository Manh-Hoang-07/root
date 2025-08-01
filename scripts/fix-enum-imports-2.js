import fs from 'fs';

// Files cần sửa import
const filesToFix = [
  'resources/js/views/admin/Users/index.vue',
  'resources/js/views/admin/Warehouses/edit.vue',
  'resources/js/views/admin/Warehouses/create.vue',
  'resources/js/views/admin/Permissions/edit.vue',
  'resources/js/views/admin/Permissions/create.vue',
  'resources/js/views/admin/Roles/index.vue',
  'resources/js/views/admin/Products/edit-page.vue',
  'resources/js/views/admin/Products/variant-form.vue',
  'resources/js/views/admin/Products/form.vue',
  'resources/js/views/admin/Products/edit.vue',
  'resources/js/views/admin/Products/create.vue',
  'resources/js/views/admin/Categories/edit.vue',
  'resources/js/views/admin/Categories/create.vue',
  'resources/js/views/admin/Orders/edit.vue',
  'resources/js/views/admin/Orders/create.vue',
  'resources/js/views/admin/Brands/edit.vue',
  'resources/js/views/admin/AttributeValues/create.vue',
  'resources/js/views/admin/AttributeValues/edit.vue',
  'resources/js/views/admin/Brands/create.vue',
  'resources/js/views/admin/Attributes/create.vue',
  'resources/js/views/admin/Attributes/edit.vue'
];

function fixEnumImports() {
  filesToFix.forEach(filePath => {
    if (fs.existsSync(filePath)) {
      let content = fs.readFileSync(filePath, 'utf8');
      let modified = false;

      // Thay thế import từ useFastEnums sang constants/enums
      const importPattern = /import \{ getEnumSync \} from ['"]@\/composables\/useFastEnums['"]/g;
      if (importPattern.test(content)) {
        content = content.replace(importPattern, `import { getEnumSync } from '@/constants/enums'`);
        modified = true;
      }

      if (modified) {
        fs.writeFileSync(filePath, content, 'utf8');
        console.log(`✅ Fixed import: ${filePath}`);
      } else {
        console.log(`⏭️  No changes needed: ${filePath}`);
      }
    } else {
      console.log(`❌ File not found: ${filePath}`);
    }
  });
}

// Chạy script
console.log('🔄 Fixing enum imports...');
fixEnumImports();
console.log('✅ Done!'); 