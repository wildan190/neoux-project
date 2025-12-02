<header class="bg-white dark:bg-gray-800 shadow-sm">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white">{{ $title ?? 'Admin Panel' }}</h1>
            <span class="text-sm text-gray-600 dark:text-gray-400">{{ now()->format('l, F j, Y') }}</span>
        </div>
        @include('admin.layouts.partials.breadcrumb')
    </div>
</header>