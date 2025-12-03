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

                    {{-- Logo --}}
                    <img src="{{ asset('assets/img/logo.png') }}" alt="MyApp Logo" class="h-8 w-auto">
                </div>

                {{-- Right: Company Switcher + Dark Mode Toggle --}}
                <div class="flex items-center gap-4">
                    {{-- Company Switcher Dropdown --}}
                    @php
                        $selectedCompanyId = session('selected_company_id');
                        $userCompanies = auth()->user()->companies()->get();
                        $selectedCompany = $userCompanies->firstWhere('id', $selectedCompanyId);

                        // Fallback if session is empty but companies exist (should be handled by controller, but good for safety)
                        if (!$selectedCompany && $userCompanies->isNotEmpty()) {
                            $selectedCompany = $userCompanies->first();
                        }
                    @endphp

                    @if($selectedCompany)
                        <div class="relative group" id="companySwitcher">
                            <button
                                class="hidden md:flex items-center gap-3 px-3 py-2 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700/50 dark:hover:bg-gray-700 rounded-xl transition-all border border-gray-200 dark:border-gray-600">
                                <div
                                    class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400">
                                    <i data-feather="briefcase" class="w-4 h-4"></i>
                                </div>
                                <div class="text-left mr-2">
                                    <p class="text-sm font-bold text-gray-900 dark:text-white leading-none">
                                        {{ $selectedCompany->name }}</p>
                                    <p
                                        class="text-[10px] font-medium text-gray-500 dark:text-gray-400 mt-1 uppercase tracking-wider">
                                        {{ $selectedCompany->category }}</p>
                                </div>
                                <i data-feather="chevron-down"
                                    class="w-4 h-4 text-gray-400 transition-transform group-hover:rotate-180"></i>
                            </button>

                            {{-- Dropdown Menu --}}
                            <div
                                class="absolute right-0 top-full mt-2 w-72 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all transform origin-top-right z-50 overflow-hidden">
                                <div
                                    class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        Switch Workspace</p>
                                </div>
                                <div class="max-h-[300px] overflow-y-auto p-2 space-y-1">
                                    @foreach($userCompanies as $company)
                                        <form action="{{ route('dashboard.select-company', $company->id) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full flex items-center gap-3 p-2 rounded-xl transition-colors {{ $company->id === $selectedCompany->id ? 'bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-900/30' : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 border border-transparent' }}">
                                                <div
                                                    class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 {{ $company->id === $selectedCompany->id ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400' }}">
                                                    @if($company->id === $selectedCompany->id)
                                                        <i data-feather="check" class="w-4 h-4"></i>
                                                    @else
                                                        <span class="text-xs font-bold">{{ substr($company->name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <div class="text-left flex-1 min-w-0">
                                                    <p
                                                        class="text-sm font-semibold text-gray-900 dark:text-white truncate {{ $company->id === $selectedCompany->id ? 'text-primary-700 dark:text-primary-300' : '' }}">
                                                        {{ $company->name }}</p>
                                                    <div class="flex items-center gap-2 mt-0.5">
                                                        <span
                                                            class="w-1.5 h-1.5 rounded-full {{ in_array($company->status, ['approved', 'active']) ? 'bg-green-500' : ($company->status === 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}"></span>
                                                        <span
                                                            class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $company->status }}</span>
                                                    </div>
                                                </div>
                                            </button>
                                        </form>
                                    @endforeach
                                </div>
                                <div
                                    class="p-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                    <a href="{{ route('companies.create') }}"
                                        class="flex items-center justify-center gap-2 w-full p-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl transition-colors">
                                        <i data-feather="plus-circle" class="w-4 h-4"></i>
                                        Register New Company
                                    </a>
                                </div>
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

        // DARK MODE
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
        }
        feather.replace();

        darkToggle.addEventListener('click', () => {
            htmlEl.classList.toggle('dark');
            localStorage.setItem('theme', htmlEl.classList.contains('dark') ? 'dark' : 'light');
            darkIcon.dataset.feather = htmlEl.classList.contains('dark') ? 'sun' : 'moon';
            feather.replace();
        });

        // SIDEBAR TOGGLE
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');
        const toggleBtn = document.getElementById('toggleSidebar');
        const contentWrapper = document.querySelector('.flex-1');

        const openDesktopSidebar = () => {
            contentWrapper.classList.add('md:pl-64');
            sidebar.classList.remove('-translate-x-full');
        };

        const closeDesktopSidebar = () => {
            contentWrapper.classList.remove('md:pl-64');
            sidebar.classList.add('-translate-x-full');
        };

        toggleBtn.addEventListener('click', () => {
            const isMobile = window.innerWidth < 768;

            if (isMobile) {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');

                // ensure overlay hides when sidebar closed
                if (sidebar.classList.contains('-translate-x-full')) {
                    overlay.classList.add('hidden');
                }
            } else {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openDesktopSidebar();
                } else {
                    closeDesktopSidebar();
                }
            }
        });

        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.add('hidden');
                contentWrapper.classList.add('md:pl-64');
            } else {
                sidebar.classList.add('-translate-x-full');
            }
        });

        // initial state
        if (window.innerWidth < 768) {
            sidebar.classList.add('-translate-x-full');
        } else {
            sidebar.classList.remove('-translate-x-full');
        }
    </script>


</body>

</html>