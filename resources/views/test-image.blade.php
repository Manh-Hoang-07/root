<!DOCTYPE html>
<html>
<head>
    <title>Test Image</title>
</head>
<body>
    <h1>Test Image</h1>
    <p>Testing image: {{ $testImage }}</p>
    <img src="{{ $testImage }}" alt="Test Image" style="max-width: 300px;">
    
    <h2>Debug Info:</h2>
    <p>Storage URL: {{ Storage::url('public/images/4Nt5QqOmEY3OQJamU4PBTVVgENrlf8FZwqppTf2L.png') }}</p>
    <p>Full URL: {{ url(Storage::url('public/images/4Nt5QqOmEY3OQJamU4PBTVVgENrlf8FZwqppTf2L.png')) }}</p>
    <p>App URL: {{ config('app.url') }}</p>
</body>
</html> 