<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Auth' }}</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 flex items-center justify-center min-h-screen p-4">

<div class="flex flex-col md:flex-row bg-white dark:bg-gray-800 rounded-3xl shadow-xl overflow-hidden max-w-4xl w-full">
    {{-- Left side: Illustration --}}
    <div class="hidden md:flex md:w-1/2 bg-indigo-500 dark:bg-indigo-600 items-center justify-center relative">
        <div class="text-white text-center p-8">
            <h2 class="text-4xl font-bold mb-4">Welcome Back!</h2>
            <p class="text-lg">Enter your credentials to access your account and enjoy the amazing features!</p>
        </div>
        <div class="absolute bottom-0 right-0 w-32 h-32 bg-white/20 rounded-full animate-pulse"></div>
    </div>

    {{-- Right side: Form --}}
    <div class="w-full md:w-1/2 p-8">
        <div class="mb-6 text-center">
            <h1 class="text-3xl font-bold mb-2">{{ $title ?? 'Login' }}</h1>
            <p class="text-gray-500 dark:text-gray-400">Enter your details below</p>
        </div>

        {{-- Form Content --}}
        <div>
            @yield('form')
        </div>

        {{-- Footer / Links --}}
        <div class="mt-6 text-center text-gray-500 dark:text-gray-400 text-sm">
            &copy; {{ date('Y') }} MyApp. All rights reserved.
        </div>
    </div>
</div>

<script>
    feather.replace();
</script>
</body>
</html>
