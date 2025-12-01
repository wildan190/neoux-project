<header class="bg-white dark:bg-gray-800 shadow-sm">
    <div class="flex items-center justify-between px-6 py-4">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $title ?? 'Admin Panel' }}</h1>
        </div>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ now()->format('l, F j, Y') }}</span>
        </div>
    </div>
</header>