<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>

    @vite('resources/css/app.css')
    <script src="https://unpkg.com/feather-icons"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Mobile Overlay --}}
        <div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0 md:pl-64">

            {{-- Header --}}
            <header
                class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">

                        {{-- Left --}}
                        <div class="flex items-center space-x-4">
                            {{-- Mobile Menu Button --}}
                            <button id="toggleSidebar"
                                class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i data-feather="menu" class="w-6 h-6 text-gray-600 dark:text-gray-300"></i>
                            </button>

                            <div>
                                @if(isset($title))
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $title }}</h1>
                                    @include('layouts.partials.breadcrumbs')
                                @endif
                            </div>
                        </div>

                        {{-- Right --}}
                        <div class="flex items-center gap-4">

                            {{-- Company Switcher --}}
                            @php
                                $selectedCompanyId = session('selected_company_id');
                                $userCompanies = auth()->user()->companies()->get();
                                $selectedCompany = $userCompanies->firstWhere('id', $selectedCompanyId)
                                    ?? $userCompanies->first();
                            @endphp

                            @if($selectedCompany)
                                <div class="relative group" id="companySwitcher">
                                    <button
                                        class="hidden md:flex items-center gap-2 px-2.5 py-1.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700/50 dark:hover:bg-gray-700 rounded-lg transition-all border border-gray-200 dark:border-gray-600">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400">
                                            <i data-feather="briefcase" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <div class="text-left mr-1">
                                            <p class="text-xs font-bold text-gray-900 dark:text-white leading-none">
                                                {{ $selectedCompany->name }}
                                            </p>
                                            <p class="text-[9px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ $selectedCompany->category }}
                                            </p>
                                        </div>
                                        <i data-feather="chevron-down"
                                            class="w-3.5 h-3.5 text-gray-400 transition group-hover:rotate-180"></i>
                                    </button>

                                    {{-- Dropdown --}}
                                    <div
                                        class="absolute right-0 top-full mt-2 w-72 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all origin-top-right z-50">
                                        <div
                                            class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">
                                                Switch Workspace
                                            </p>
                                        </div>

                                        <div class="max-h-[300px] overflow-y-auto p-2 space-y-1">
                                            @foreach($userCompanies as $company)
                                                                            <form action="{{ route('dashboard.select-company', $company->id) }}"
                                                                                method="POST">
                                                                                @csrf
                                                                                <button type="submit" class="w-full flex items-center gap-3 p-2 rounded-xl transition
                                                                                                {{ $company->id == $selectedCompany->id
                                                ? 'bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-900/30'
                                                : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 border border-transparent'
                                                                                                }}">
                                                                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                                                                                {{ $company->id == $selectedCompany->id
                                                ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                                                                                                }}">
                                                                                        @if($company->id == $selectedCompany->id)
                                                                                            <i data-feather="check" class="w-4 h-4"></i>
                                                                                        @else
                                                                                            <span
                                                                                                class="text-xs font-bold">{{ substr($company->name, 0, 1) }}</span>
                                                                                        @endif
                                                                                    </div>

                                                                                    <div class="text-left flex-1 min-w-0">
                                                                                        <p
                                                                                            class="text-sm font-semibold truncate
                                                                                                {{ $company->id == $selectedCompany->id ? 'text-primary-700 dark:text-primary-300' : 'text-gray-900 dark:text-white' }}">
                                                                                            {{ $company->name }}
                                                                                        </p>
                                                                                        <div class="flex items-center gap-2">
                                                                                            <span
                                                                                                class="w-1.5 h-1.5 rounded-full
                                                                                                    {{ in_array($company->status, ['approved', 'active'])
                                                ? 'bg-green-500'
                                                : ($company->status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                                                                            </span>
                                                                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                                                {{ $company->status }}
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </button>
                                                                            </form>
                                            @endforeach
                                        </div>

                                        <div
                                            class="p-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                            <a href="{{ route('companies.create') }}"
                                                class="flex items-center justify-center gap-2 p-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl">
                                                <i data-feather="plus-circle" class="w-4 h-4"></i>
                                                Register New Company
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif


                            {{-- Dark Mode --}}
                            <button id="darkModeToggle"
                                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                <i id="darkIcon" data-feather="moon"
                                    class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
                            </button>

                        </div>
                    </div>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900">
                <div class="container mx-auto px-4 md:px-6 py-8">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- CLEAN FIXED JAVASCRIPT --}}
    {{-- ===================================================== --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {

            /* ---------------------------
             * FEATHER ICONS
             * --------------------------- */
            feather.replace();

            /* ---------------------------
             * DARK MODE SYSTEM
             * --------------------------- */
            const htmlEl = document.documentElement;
            const darkIcon = document.getElementById('darkIcon');
            const darkToggle = document.getElementById('darkModeToggle');

            const savedTheme = localStorage.getItem('theme');

            if (savedTheme === 'dark') {
                htmlEl.classList.add('dark');
                darkIcon.dataset.feather = 'sun';
            }

            feather.replace();

            darkToggle.addEventListener('click', () => {
                htmlEl.classList.toggle('dark');
                const isDark = htmlEl.classList.contains('dark');

                localStorage.setItem('theme', isDark ? 'dark' : 'light');

                darkIcon.dataset.feather = isDark ? 'sun' : 'moon';
                feather.replace();
            });

            /* ---------------------------
             * MOBILE SIDEBAR
             * --------------------------- */
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');
            const toggleBtn = document.getElementById('toggleSidebar');

            // Mobile default: hide sidebar
            if (window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
            }

            // Open Sidebar
            toggleBtn?.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            });

            // Close when overlay clicked
            overlay?.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });

            // Adjust on resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('hidden');
                } else {
                    sidebar.classList.add('-translate-x-full');
                }
            });

        });
    </script>

</body>

</html>