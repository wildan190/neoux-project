<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>
    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body
    class="bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200 transition-colors duration-300 overflow-x-hidden">

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col transition-all duration-300 md:pl-64 w-full max-w-full">

            {{-- Navbar fixed --}}
            <nav id="navbar"
                class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between bg-white dark:bg-gray-800 shadow px-6 py-4">
                {{-- Left: Hamburger + App name/logo --}}
                <div class="flex items-center gap-4">
                    <button id="toggleSidebar"
                        class="p-2 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition relative z-50">
                        <i data-feather="menu" class="w-6 h-6 pointer-events-none"></i>
                    </button>
                    <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400">MyApp</span>
                </div>

                {{-- Right: Active Company Info + Dark Mode Toggle --}}
                <div class="flex items-center gap-4">
                    {{-- Active Company Indicator --}}
                    @php
                        $selectedCompanyId = session('selected_company_id');
                        $selectedCompany = null;
                        if ($selectedCompanyId) {
                            $selectedCompany = \App\Modules\Company\Domain\Models\Company::find($selectedCompanyId);
                        }
                    @endphp
                    
                    @if($selectedCompany)
                        <div class="hidden md:flex items-center gap-2 px-3 py-2 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg">
                            <i data-feather="briefcase" class="w-4 h-4 text-indigo-600 dark:text-indigo-400"></i>
                            <div class="text-sm">
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $selectedCompany->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    <span class="inline-flex items-center">
                                        <span class="w-2 h-2 rounded-full mr-1 {{ in_array($selectedCompany->status, ['approved', 'active']) ? 'bg-green-500' : ($selectedCompany->status === 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                                        {{ ucfirst($selectedCompany->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    @endif

                    <button id="darkModeToggle"
                        class="p-2 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700 rounded transition relative z-50">
                        <i id="darkIcon" data-feather="moon" class="w-5 h-5 pointer-events-none"></i>
                    </button>
                </div>
            </nav>

            {{-- Content --}}
            <main class="p-6 pt-24">
                @if(isset($title))
                    <div class="mb-6">
                        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $title }}</h1>
                        @include('layouts.partials.breadcrumbs')
                    </div>
                @endif
                @yield('content')
            </main>

        </div>

    </div>

    {{-- Overlay untuk mobile --}}
    <div id="overlay" class="fixed inset-0 bg-black/40 hidden z-40 md:hidden"></div>

    <script>
        feather.replace();

        // -----------------------------
        // Dark/Light Mode Toggle
        // -----------------------------
        const darkToggle = document.getElementById('darkModeToggle');
        const htmlEl = document.documentElement;
        const darkIcon = document.getElementById('darkIcon');

        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            htmlEl.classList.add('dark');
            darkIcon.dataset.feather = 'sun';
        } else if (savedTheme === 'light') {
            htmlEl.classList.remove('dark');
            darkIcon.dataset.feather = 'moon';
        } else {
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                htmlEl.classList.add('dark');
                darkIcon.dataset.feather = 'sun';
            } else {
                htmlEl.classList.remove('dark');
                darkIcon.dataset.feather = 'moon';
            }
        }
        feather.replace();

        darkToggle.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            if (htmlEl.classList.contains('dark')) {
                localStorage.setItem('theme', 'dark');
                darkIcon.dataset.feather = 'sun';
            } else {
                localStorage.setItem('theme', 'light');
                darkIcon.dataset.feather = 'moon';
            }
            feather.replace();
        });

        // -----------------------------
        // Sidebar toggle
        // -----------------------------
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const toggleBtn = document.getElementById('toggleSidebar');
        const closeBtn = document.getElementById('closeSidebar');
        const contentWrapper = document.querySelector('.flex-1');

        const openDesktopSidebar = () => {
            contentWrapper.classList.add('md:pl-64');
            contentWrapper.classList.remove('md:pl-0');
            sidebar.classList.remove('-translate-x-full');
        };

        const closeDesktopSidebar = () => {
            contentWrapper.classList.add('md:pl-0');
            contentWrapper.classList.remove('md:pl-64');
            sidebar.classList.add('-translate-x-full');
        };

        toggleBtn.addEventListener('click', () => {
            const isMobile = window.innerWidth < 768;

            if (isMobile) {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            } else {
                if (contentWrapper.classList.contains('md:pl-64')) {
                    closeDesktopSidebar();
                } else {
                    openDesktopSidebar();
                }
            }
        });

        closeBtn.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                openDesktopSidebar();
                overlay.classList.add('hidden');
            } else {
                closeDesktopSidebar();
            }
        });

        // Initial state
        if (window.innerWidth < 768) {
            closeDesktopSidebar();
        } else {
            openDesktopSidebar();
        }
    </script>

</body>

</html>