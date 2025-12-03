<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Not Found</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 flex items-center justify-center min-h-screen p-6 relative">

<div class="text-center max-w-xl relative z-10">
    {{-- Icon Feather --}}
    <div class="mb-8 text-primary-500 dark:text-primary-400">
        <i data-feather="alert-triangle" class="w-24 h-24 mx-auto animate-bounce"></i>
    </div>

    {{-- Headline --}}
    <h1 class="text-6xl font-extrabold mb-4">404</h1>
    <h2 class="text-2xl font-semibold mb-6">Oops! Page Not Found</h2>

    {{-- Description --}}
    <p class="text-gray-600 dark:text-gray-400 mb-6">
        The page you are looking for does not exist or has been moved. Check the URL or go back to the homepage.
    </p>

    {{-- Buttons --}}
    <div class="flex justify-center gap-4 flex-wrap">
        <a href="/"
           class="px-6 py-3 bg-primary-500 text-white rounded-lg hover:bg-primary-600 dark:bg-primary-400 dark:hover:bg-primary-500 transition font-semibold">
            Go Home
        </a>
        <a href="javascript:history.back()"
           class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition font-semibold">
            Go Back
        </a>
    </div>
</div>

{{-- Background shapes --}}
<div class="absolute top-10 left-10 w-16 h-16 bg-primary-200 dark:bg-primary-700 rounded-full opacity-20 animate-pulse"></div>
<div class="absolute bottom-20 right-20 w-24 h-24 bg-primary-300 dark:bg-primary-600 rounded-full opacity-10 animate-pulse"></div>

<script>
    feather.replace();
</script>
</body>
</html>
