<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vue Inertia Book Management</title>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="antialiased bg-gray-500 text-white">
    @inertia
</body>
</html>