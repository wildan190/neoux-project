<header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            {{-- Left: Mobile Menu + Title --}}
            <div class="flex items-center space-x-4">
                <button id="toggleAdminSidebar"
                    class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <i data-feather="menu" class="w-6 h-6 text-gray-600 dark:text-gray-300"></i>
                </button>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title ?? 'Admin Panel' }}</h1>
                    @include('admin.layouts.partials.breadcrumb')
                </div>
            </div>

            {{-- Right: Date + Dark Mode Toggle --}}
            <div class="flex items-center gap-4">
                <span class="hidden md:block text-sm font-medium text-gray-500 dark:text-gray-400">
                    {{ now()->format('l, F j, Y') }}
                </span>
                <button id="adminDarkModeToggle"
                    class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors relative">
                    <i id="adminDarkIcon" data-feather="moon" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
                </button>
            </div>
        </div>
    </div>
</header>