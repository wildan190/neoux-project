<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin Panel' }} - NeoUX Admin</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        @include('admin.layouts.partials.sidebar')

        {{-- Mobile Overlay --}}
        <div id="admin-sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0 md:pl-64">
            {{-- Header --}}
            @include('admin.layouts.partials.header')

            {{-- Content Area --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900">
                <div class="container mx-auto px-4 md:px-6 py-8">
                    {{-- Success Alert --}}
                    @if(session('success'))
                        <div class="mb-6 flex items-start gap-3 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 rounded-lg shadow-sm"
                            role="alert">
                            <div class="flex-shrink-0">
                                <i data-feather="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Error Alert --}}
                    @if(session('error'))
                        <div class="mb-6 flex items-start gap-3 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 rounded-lg shadow-sm"
                            role="alert">
                            <div class="flex-shrink-0">
                                <i data-feather="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <script>
        feather.replace();

        // Dark Mode Toggle
        const adminDarkToggle = document.getElementById('adminDarkModeToggle');
        const htmlEl = document.documentElement;
        const adminDarkIcon = document.getElementById('adminDarkIcon');

        const savedTheme = localStorage.getItem('admin-theme');
        if (savedTheme === 'dark') {
            htmlEl.classList.add('dark');
            adminDarkIcon.dataset.feather = 'sun';
        } else if (savedTheme === 'light') {
            htmlEl.classList.remove('dark');
            adminDarkIcon.dataset.feather = 'moon';
        }
        feather.replace();

        adminDarkToggle?.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('admin-theme', htmlEl.classList.contains('dark') ? 'dark' : 'light');
            adminDarkIcon.dataset.feather = htmlEl.classList.contains('dark') ? 'sun' : 'moon';
            feather.replace();
        });

        // Mobile Sidebar Toggle
        const adminSidebar = document.getElementById('admin-sidebar');
        const adminOverlay = document.getElementById('admin-sidebar-overlay');
        const toggleAdminSidebarBtn = document.getElementById('toggleAdminSidebar');

        toggleAdminSidebarBtn?.addEventListener('click', () => {
            adminSidebar.classList.toggle('-translate-x-full');
            adminOverlay.classList.toggle('hidden');
        });

        adminOverlay?.addEventListener('click', () => {
            adminSidebar.classList.add('-translate-x-full');
            adminOverlay.classList.add('hidden');
        });
    </script>
</body>

</html>