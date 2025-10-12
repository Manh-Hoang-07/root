<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-container {
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .content h2 {
            color: #007bff;
            margin-top: 0;
        }
        .content p {
            margin-bottom: 15px;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            border-top: 1px solid #e9ecef;
        }
        .data-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .data-section h3 {
            margin-top: 0;
            color: #007bff;
        }
        .data-item {
            margin: 5px 0;
        }
        .data-label {
            font-weight: bold;
            color: #495057;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="header">
            <h1>{{ $app_name }}</h1>
        </div>
        
        <div class="content">
            <h2>{{ $subject }}</h2>
            
            <div class="content-body">
                {!! nl2br(e($content)) !!}
            </div>

            @if(!empty($data))
            <div class="data-section">
                <h3>Thông tin bổ sung:</h3>
                @foreach($data as $key => $value)
                <div class="data-item">
                    <span class="data-label">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                    <span>{{ is_array($value) ? json_encode($value) : $value }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="footer">
            <p>Email này được gửi từ hệ thống {{ $app_name }}</p>
            <p>Thời gian: {{ $timestamp }}</p>
        </div>
    </div>
</body>
</html>
