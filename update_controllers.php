<?php

// Đường dẫn tới thư mục controllers
$controllerPath = 'app/Http/Controllers/Api';

// Lấy tất cả các file controller
$controllers = glob($controllerPath . '/**/*Controller.php');

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
    
    // Tìm namespace của controller
    preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
    $namespace = $namespaceMatches[1];
    
    // Tìm tên class của controller
    preg_match('/class\s+(\w+)\s+extends\s+BaseController/', $content, $classMatches);
    $className = $classMatches[1];
    
    // Tìm constructor
    preg_match('/public\s+function\s+__construct\(([^)]+)\)[\s\n]*{([^}]+)}/', $content, $constructorMatches);
    
    if (empty($constructorMatches)) {
        echo "Không tìm thấy constructor: $controller\n";
        continue;
    }
    
    $constructorParams = $constructorMatches[1];
    $constructorBody = $constructorMatches[2];
    
    // Tìm service class từ constructor
    preg_match('/([^\s]+)Service\s+\$service/', $constructorParams, $serviceMatches);
    
    if (empty($serviceMatches)) {
        echo "Không tìm thấy service class: $controller\n";
        continue;
    }
    
    $serviceClass = $serviceMatches[1] . 'Service';
    
    // Tìm các thuộc tính được thiết lập trong constructor
    preg_match_all('/\$this->(\w+)\s*=\s*([^;]+);/', $constructorBody, $propertyMatches, PREG_SET_ORDER);
    
    $properties = [];
    foreach ($propertyMatches as $match) {
        $propName = $match[1];
        $propValue = trim($match[2]);
        
        // Bỏ qua service vì sẽ được thiết lập tự động
        if ($propName !== 'service') {
            $properties[$propName] = $propValue;
        }
    }
    
    // Tìm các thuộc tính được khai báo trước constructor
    preg_match_all('/protected\s+\$(\w+)\s*=\s*([^;]+);/', $content, $declaredProps, PREG_SET_ORDER);
    
    foreach ($declaredProps as $match) {
        $propName = $match[1];
        $propValue = trim($match[2]);
        
        // Chỉ thêm vào nếu chưa có trong properties
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
    $newContent = preg_replace(
        '/class\s+' . $className . '\s+extends\s+BaseController\s*\{.*?(?=\s*public\s+function\s+(?!__construct))/',
        $newClassDefinition,
        $content,
        1,
        $count,
        PREG_OFFSET_CAPTURE
    );
    
    if ($count === 0) {
        // Nếu không có phương thức public khác ngoài constructor
        $newContent = preg_replace(
            '/class\s+' . $className . '\s+extends\s+BaseController\s*\{.*?}$/s',
            $newClassDefinition . "}",
            $content
        );
    }
    
    // Lưu nội dung mới
    file_put_contents($controller, $newContent);
    echo "Đã cập nhật controller: $controller\n";
}

echo "Hoàn thành cập nhật controllers!\n";
