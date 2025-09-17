<?php

// Đường dẫn tới thư mục services
$servicePath = 'app/Services';

// Lấy tất cả các file service
$services = glob($servicePath . '/**/*Service.php');

// Loại bỏ BaseService
$services = array_filter($services, function($file) {
    return !str_contains($file, 'BaseService.php');
});

echo "Tìm thấy " . count($services) . " services cần kiểm tra\n";

foreach ($services as $service) {
    // Đọc nội dung file
    $content = file_get_contents($service);
    
    // Kiểm tra xem service đã được cập nhật chưa
    if (strpos($content, 'protected static $repositoryClass') !== false) {
        echo "Service đã được cập nhật: $service\n";
        continue;
    }
    
    // Kiểm tra xem service có kế thừa từ BaseService không
    if (strpos($content, 'extends BaseService') === false) {
        echo "Service không kế thừa từ BaseService: $service\n";
        continue;
    }
    
    // Tìm namespace của service
    preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
    if (empty($namespaceMatches)) {
        echo "Không tìm thấy namespace: $service\n";
        continue;
    }
    $namespace = $namespaceMatches[1];
    
    // Tìm tên class của service
    preg_match('/class\s+(\w+)\s+extends\s+BaseService/', $content, $classMatches);
    if (empty($classMatches)) {
        echo "Không tìm thấy class kế thừa từ BaseService: $service\n";
        continue;
    }
    $className = $classMatches[1];
    
    // Tìm constructor
    preg_match('/public\s+function\s+__construct\(([^)]+)\)[\s\n]*{([^}]+)}/', $content, $constructorMatches);
    
    if (empty($constructorMatches)) {
        echo "Không tìm thấy constructor: $service\n";
        continue;
    }
    
    $constructorParams = $constructorMatches[1];
    
    // Tìm repository class từ constructor
    preg_match('/([^\s,]+)Repository\s+\$repo/', $constructorParams, $repoMatches);
    
    if (empty($repoMatches)) {
        echo "Không tìm thấy repository class: $service\n";
        continue;
    }
    
    $repoClass = $repoMatches[1] . 'Repository';
    
    // Tạo mẫu mới cho class
    $newClassDefinition = "class $className extends BaseService\n{\n";
    $newClassDefinition .= "    protected static \$repositoryClass = $repoClass::class;";
    
    // Tạo nội dung file mới
    $pattern = '/class\s+' . $className . '\s+extends\s+BaseService\s*\{[\s\S]*?public\s+function\s+__construct\([^)]+\)[\s\S]*?\}/';
    $replacement = $newClassDefinition;
    $newContent = preg_replace($pattern, $replacement, $content);
    
    if ($newContent === null) {
        echo "Lỗi khi thay thế nội dung: $service\n";
        continue;
    }
    
    // Lưu nội dung mới
    file_put_contents($service, $newContent);
    echo "Đã cập nhật service: $service\n";
}

echo "Hoàn thành cập nhật services!\n";
