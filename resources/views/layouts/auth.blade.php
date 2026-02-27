<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.2.0/fonts/remixicon.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f6f9ff;
            color: #444444;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        
        .card {
            border: none;
            border-radius: 5px;
            box-shadow: 0px 0 30px rgba(1, 41, 112, 0.1);
            background-color: #fff;
            width: 100%;
            max-width: 500px;
        }
        
        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #ebeef4;
            padding: 20px;
            font-size: 18px;
            font-weight: 600;
            color: #012970;
            text-align: center;
        }

        .btn-primary {
            background-color: #4154f1;
            border-color: #4154f1;
        }

        .btn-primary:hover {
            background-color: #2c3cd1;
            border-color: #2c3cd1;
        }
    </style>
</head>
<body>

    @yield('content')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>