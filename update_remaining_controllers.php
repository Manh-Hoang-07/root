<?php

// Lấy danh sách tất cả các controller
$controllers = glob('app/Http/Controllers/Api/**/*Controller.php');

// Loại bỏ BaseController
$controllers = array_filter($controllers, function($file) {
    return !str_contains($file, 'BaseController.php');
});

foreach ($controllers as $controller) {
    // Đọc nội dung file
    $content = file_get_contents($controller);
    
    // Kiểm tra xem controller đã được cập nhật chưa
    if (strpos($content, 'protected static $serviceClass') !== false) {
        echo "Controller đã được cập nhật: $controller\n";
        continue;
    }
    
    // Tìm tên class
    preg_match('/class\s+(\w+)\s+extends\s+BaseController/', $content, $classMatches);
    if (empty($classMatches)) {
        echo "Không tìm thấy class kế thừa từ BaseController trong file $controller\n";
        continue;
    }
    $className = $classMatches[1];
    
    // Tìm constructor
    preg_match('/public\s+function\s+__construct\(([^)]+)\)[\s\n]*{([^}]+)}/', $content, $constructorMatches);
    if (empty($constructorMatches)) {
        echo "Không tìm thấy constructor trong file $controller\n";
        continue;
    }
    
    // Tìm service class từ constructor
    preg_match('/([^\s,]+)Service\s+\$service/', $constructorMatches[1], $serviceMatches);
    if (empty($serviceMatches)) {
        echo "Không tìm thấy service class trong file $controller\n";
        continue;
    }
    $serviceClass = $serviceMatches[1] . 'Service';
    
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
    
    // Tìm các thuộc tính được khai báo trước constructor
    preg_match_all('/protected\s+\$(\w+)\s*=\s*([^;]+);/', $content, $declaredProps, PREG_SET_ORDER);
    foreach ($declaredProps as $match) {
        $propName = $match[1];
        $propValue = trim($match[2]);
        
        if (!isset($properties[$propName])) {
            $properties[$propName] = $propValue;
        }
    }
    
    // Tạo mẫu mới cho class
    $newClassDefinition = "class $className extends BaseController\n{\n";
    $newClassDefinition .= "    protected static \$serviceClass = $serviceClass::class;\n";
    
    // Thêm các thuộc tính khác
    foreach ($properties as $propName => $propValue) {
        $newClassDefinition .= "    protected \$$propName = $propValue;\n";
    }
    
    // Tìm phần code sau constructor
    $afterConstructor = substr($content, strpos($content, $constructorMatches[0]) + strlen($constructorMatches[0]));
    
    // Tạo nội dung file mới
    $pattern = '/class\s+' . $className . '\s+extends\s+BaseController\s*\{[\s\S]*?(?=\s*public\s+function\s+(?!__construct))|\s*\}/';
    $replacement = $newClassDefinition;
    $newContent = preg_replace($pattern, $replacement, $content);
    
    // Lưu nội dung mới
    file_put_contents($controller, $newContent);
    echo "Đã cập nhật controller: $controller\n";
}

echo "Hoàn thành cập nhật tất cả các controller!\n";
