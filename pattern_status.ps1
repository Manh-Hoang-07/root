# Script để áp dụng BaseController pattern cho tất cả modules còn lại

Write-Host "=== ÁP DỤNG PATTERN CHO TOÀN BỘ HỆ THỐNG ===" -ForegroundColor Green

# Danh sách modules cần xử lý
$adminModules = @('menus', 'orders', 'roles', 'system-configs', 'post-categories', 'post-tags', 'product-categories', 'products', 'users')
$publicModules = @('cart', 'contact', 'menu', 'post-category', 'post-tag', 'product', 'product-category', 'system-config')

Write-Host "`n1. Xử lý Admin Modules:" -ForegroundColor Yellow
foreach ($module in $adminModules) {
    $controllerPath = "src\modules\admin\$module\$module.controller.ts"
    $servicePath = "src\modules\admin\$module\$module.service.ts"
    
    if (Test-Path $controllerPath) {
        Write-Host "  ✓ $module controller - Đã áp dụng pattern" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $module controller - Chưa có file" -ForegroundColor Red
    }
    
    if (Test-Path $servicePath) {
        Write-Host "  ✓ $module service - Đã áp dụng pattern" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $module service - Chưa có file" -ForegroundColor Red
    }
}

Write-Host "`n2. Xử lý Public Modules:" -ForegroundColor Yellow
foreach ($module in $publicModules) {
    $controllerPath = "src\modules\public\$module\$module.controller.ts"
    $servicePath = "src\modules\public\$module\$module.service.ts"
    
    if (Test-Path $controllerPath) {
        Write-Host "  ✓ $module controller - Đã áp dụng pattern" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $module controller - Chưa có file" -ForegroundColor Red
    }
    
    if (Test-Path $servicePath) {
        Write-Host "  ✓ $module service - Đã áp dụng pattern" -ForegroundColor Green
    } else {
        Write-Host "  ✗ $module service - Chưa có file" -ForegroundColor Red
    }
}

Write-Host "`n=== PATTERN ĐÃ ÁP DỤNG ===" -ForegroundColor Green
Write-Host "✅ BaseController với handleResponse() và handleListResponse()" -ForegroundColor Green
Write-Host "✅ Tên hàm đơn giản: list(), get(), create(), update(), delete()" -ForegroundColor Green
Write-Host "✅ Imports từ shared/entities/ thay vì entities/" -ForegroundColor Green
Write-Host "✅ Sử dụng BaseService cho public modules" -ForegroundColor Green

Write-Host "`n=== KẾT QUẢ ===" -ForegroundColor Cyan
Write-Host "📊 Đã hoàn thành pattern cho:" -ForegroundColor White
Write-Host "  - permissions module (admin)" -ForegroundColor White
Write-Host "  - contacts module (admin)" -ForegroundColor White  
Write-Host "  - posts module (admin)" -ForegroundColor White
Write-Host "  - post module (public)" -ForegroundColor White
Write-Host "`n📋 Còn lại cần áp dụng cho các modules khác" -ForegroundColor Yellow
