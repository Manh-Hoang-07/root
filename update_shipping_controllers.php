<?php

// Lấy danh sách các controller trong thư mục Shipping
$shippingControllers = glob('app/Http/Controllers/Api/Admin/Shipping/*Controller.php');

foreach ($shippingControllers as $controller) {
    // Đọc nội dung file
    $content = file_get_contents($controller);
    
    // Tìm tên class
    preg_match('/class\s+(\w+)\s+extends\s+BaseController/', $content, $classMatches);
    if (empty($classMatches)) {
        echo "Không tìm thấy class kế thừa từ BaseController trong file $controller\n";
        continue;
    }
    $className = $classMatches[1];
    
    // Tìm service class
    preg_match('/use\s+App\\\\Services\\\\Admin\\\\Shipping\\\\(\w+);/', $content, $serviceMatches);
    if (empty($serviceMatches)) {
        echo "Không tìm thấy service class trong file $controller\n";
        continue;
    }
    $serviceClass = $serviceMatches[1];
    
    // Tìm request class
    preg_match('/use\s+App\\\\Http\\\\Requests\\\\Admin\\\\Shipping\\\\(\w+);/', $content, $requestMatches);
    $requestClass = !empty($requestMatches) ? $requestMatches[1] : null;
    
    // Tìm constructor
    preg_match('/public\s+function\s+__construct\(([^)]+)\)[\s\n]*{([^}]+)}/', $content, $constructorMatches);
    if (empty($constructorMatches)) {
        echo "Không tìm thấy constructor trong file $controller\n";
        continue;
    }
    
    // Tìm các thuộc tính được thiết lập trong constructor
    preg_match_all('/\$this->(\w+)\s*=\s*([^;]+);/', $constructorMatches[2], $propertyMatches, PREG_SET_ORDER);
    
    $properties = [];
    foreach ($propertyMatches as $match) {
        $propName = $match[1];
        $propValue = trim($match[2]);
        
        if ($propName !== 'service') {
            $properties[$propName] = $propValue;
        }
    }
    
    // Tạo nội dung mới
    $newContent = str_replace(
        "public function __construct({$serviceClass}Service \$service)\n    {\n        parent::__construct(\$service);",
        "protected static \$serviceClass = {$serviceClass}Service::class;",
        $content
    );
    
    // Xóa phần còn lại của constructor
    $newContent = preg_replace('/\s*\$this->\w+\s*=\s*[^;]+;\s*}/', '', $newContent);
    
    // Thêm các thuộc tính
    $propertyLines = '';
    foreach ($properties as $propName => $propValue) {
        $propertyLines .= "    protected \${$propName} = {$propValue};\n";
    }
    
    // Thêm các thuộc tính vào sau dòng serviceClass
    $newContent = str_replace(
        "protected static \$serviceClass = {$serviceClass}Service::class;",
        "protected static \$serviceClass = {$serviceClass}Service::class;\n{$propertyLines}",
        $newContent
    );
    
    // Lưu file
    file_put_contents($controller, $newContent);
    echo "Đã cập nhật file $controller\n";
}

echo "Hoàn thành cập nhật các controller trong thư mục Shipping!\n";
