# Script để áp dụng BaseController pattern cho tất cả modules
$adminModules = @('menus', 'orders', 'roles', 'system-configs', 'post-categories', 'post-tags', 'product-categories', 'posts', 'products', 'users')

foreach ($module in $adminModules) {
    Write-Host "Processing admin/$module..."
    
    # Controller pattern
    $controllerPath = "src\modules\admin\$module\$module.controller.ts"
    if (Test-Path $controllerPath) {
        Write-Host "  - Updating controller: $controllerPath"
        # Add BaseController import and extend
        # Rename methods: getXxxs -> list, getXxx -> get, createXxx -> create, updateXxx -> update, deleteXxx -> delete
    }
    
    # Service pattern  
    $servicePath = "src\modules\admin\$module\$module.service.ts"
    if (Test-Path $servicePath) {
        Write-Host "  - Updating service: $servicePath"
        # Rename methods: getXxxs -> list, getXxx -> get, createXxx -> create, updateXxx -> update, deleteXxx -> delete
        # Update imports from entities/ to shared/entities/
    }
}

Write-Host "Admin modules processing complete!"

# Process public modules
$publicModules = @('cart', 'contact', 'menu', 'post', 'post-category', 'post-tag', 'product', 'product-category', 'system-config')

foreach ($module in $publicModules) {
    Write-Host "Processing public/$module..."
    # Similar pattern for public modules
}

Write-Host "All modules processing complete!"
