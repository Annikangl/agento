<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selection not found</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: white;
        }
        .message {
            text-align: center;
        }
        .icon {
            display: block;
            margin: 0 auto;
            width: 100px;
        }
    </style>
</head>
<body>
<div class="message">
    <img src="{{ asset('assets/images/no-results.png') }}" alt="Not Found" class="icon">
    <h1>{{ $message ?? 'Page not found' }}</h1>
</div>
</body>
</html>
