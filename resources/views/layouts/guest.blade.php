<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ !empty($title) ? $title . ' - FG IT Asset Management' : 'Sign In - FG IT Asset Management' }}</title>
    <link rel="icon" href="{{ asset('images/fg_logo.png') }}" type="image/png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    <!-- WireUI & Vite Scripts -->
    @wireUiScripts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-[#f9fafb] text-[#3b4048] antialiased font-sans selection:bg-[#a92d31] selection:text-white flex flex-col justify-between">
    <main class="flex-1 flex items-center justify-center px-4 py-10">
        {{ $slot }}
    </main>

    <footer class="py-4 text-center text-xs text-gray-500">
        &copy; {{ date('Y') }} First Grade Resources &mdash; FG IT Asset Management System
    </footer>

    @livewireScripts
</body>
</html>
