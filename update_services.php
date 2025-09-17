<?php

// Đường dẫn tới thư mục services
$servicePath = 'app/Services';

// Lấy tất cả các file service
$services = glob($servicePath . '/**/*Service.php');

// Loại bỏ BaseService
$services = array_filter($services, function($file) {
    return !str_contains($file, 'BaseService.php');
});

foreach ($services as $service) {
    // Đọc nội dung file
    $content = file_get_contents($service);
    
    // Kiểm tra xem service đã được cập nhật chưa
    if (strpos($content, 'protected static $repositoryClass') !== false) {
        echo "Service đã được cập nhật: $service\n";
        continue;
    }
    
    // Tìm namespace của service
    preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches);
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
    $constructorBody = $constructorMatches[2];
    
    // Tìm repository class từ constructor
    preg_match('/([^\s]+)Repository\s+\$repo/', $constructorParams, $repoMatches);
    
    if (empty($repoMatches)) {
        echo "Không tìm thấy repository class: $service\n";
        continue;
    }
    
    $repoClass = $repoMatches[1] . 'Repository';
    
    // Tạo mẫu mới cho class
    $newClassDefinition = "class $className extends BaseService\n{\n";
    $newClassDefinition .= "    protected static \$repositoryClass = $repoClass::class;";
    
    // Tạo nội dung file mới
    $newContent = preg_replace(
        '/class\s+' . $className . '\s+extends\s+BaseService\s*\{[\s\n]*public\s+function\s+__construct\([^)]+\)[\s\n]*\{[^}]+\}/',
        $newClassDefinition,
        $content
    );
    
    // Lưu nội dung mới
    file_put_contents($service, $newContent);
    echo "Đã cập nhật service: $service\n";
}

echo "Hoàn thành cập nhật services!\n";
