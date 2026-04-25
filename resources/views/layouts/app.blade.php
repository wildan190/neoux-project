<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap"
        rel="stylesheet">

    @php
        $procurementMode = session('procurement_mode', 'buyer');
    @endphp

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .animate-shimmer {
            animation: shimmer 2s infinite linear;
            background: linear-gradient(to right, #f6f7f8 8%, #edeef1 18%, #f6f7f8 33%);
            background-size: 1000px 100%;
        }
        .dark .animate-shimmer {
            background: linear-gradient(to right, #1f2937 8%, #374151 18%, #1f2937 33%);
            background-size: 1000px 100%;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300" 
    data-layout="app"
    data-hide-sidebar="{{ empty($hide_sidebar) ? 'false' : 'true' }}" 
    data-hide-header="{{ empty($hide_header) ? 'false' : 'true' }}">
    @php
        $isAdminContext = request()->is('admin') || request()->is('admin/*');
        $isAdminView = auth('admin')->check() && $isAdminContext;
        $isPlatformAuth = auth()->check();
        $isAuth = $isPlatformAuth || $isAdminView;
    @endphp

    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        @if(empty($hide_sidebar))
            @if($isAdminView)
                @include('admin::layouts.partials.sidebar')
            @else
                @include('layouts.partials.sidebar')
            @endif
        @endif

        {{-- Mobile Overlay --}}
        <div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col min-w-0 {{ empty($hide_sidebar) && $isAuth ? 'md:pl-64' : '' }}">

            @if(empty($hide_header))
            {{-- Header --}}
            <header class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-md border-b border-gray-100 dark:border-gray-800 sticky top-0 z-40">
                <div class="px-4 md:px-8 py-3.5">
                    <div class="flex items-center justify-between gap-4">

                        {{-- Left: Page Info --}}
                        <div class="flex items-center space-x-4 min-w-0 flex-1">
                            {{-- Mobile Menu Button --}}
                            <button id="toggleSidebar"
                                class="md:hidden flex-shrink-0 p-2 rounded-xl bg-gray-50 dark:bg-gray-800 text-gray-500 hover:text-primary-600 transition-all">
                                <i data-feather="menu" class="w-5 h-5"></i>
                            </button>
                            
                            <div id="header-content-area" class="min-w-0 flex-1">
                                @if(isset($title))
                                    <div class="flex flex-col">
                                        <h1 class="text-lg font-black text-gray-900 dark:text-white truncate tracking-tight uppercase" id="page-title">{{ $title }}</h1>
                                        <div id="breadcrumb-area" class="hidden sm:block">
                                            @include('layouts.partials.breadcrumbs')
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Right: Actions --}}
                        <div class="flex items-center gap-3 md:gap-6">

                            {{-- Company Switcher --}}
                            @php
                                $selectedCompanyId = session('selected_company_id');
                                $userCompanies = $isPlatformAuth ? auth()->user()->allCompanies() : collect();
                                $selectedCompany = ($userCompanies->firstWhere('id', $selectedCompanyId) ?? $userCompanies->first());
                            @endphp

                            @if(!$isAdminView && $selectedCompany)
                                <div class="relative group" id="companySwitcher">
                                    <button
                                        class="hidden md:flex items-center gap-3 px-3 py-1.5 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition-all border border-transparent hover:border-gray-100 dark:hover:border-gray-700">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold text-xs shadow-md">
                                            {{ substr($selectedCompany->name, 0, 1) }}
                                        </div>
                                        <div class="text-left">
                                        <i data-feather="chevron-down"
                                            class="w-3.5 h-3.5 text-gray-400 transition-transform group-hover:rotate-180"></i>
                                    </button>

                                    {{-- Improved Dropdown --}}
                                    <div class="absolute right-0 top-full mt-2 w-72 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all transform origin-top-right z-50 py-2 scale-95 group-hover:scale-100">
                                        <div class="px-4 py-2 border-b border-gray-50 dark:border-gray-700/50 mb-2">
                                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Select Workspace</p>
                                        </div>

                                        <div class="max-h-[300px] overflow-y-auto px-2 space-y-1">
                                            @foreach($userCompanies as $company)
                                                <form action="{{ route('dashboard.select-company', $company->id) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="w-full flex items-center gap-3 p-2.5 rounded-xl transition-all
                                                        {{ $company->id == $selectedCompany->id
                                                            ? 'bg-primary-50 dark:bg-primary-900/20'
                                                            : 'hover:bg-gray-50 dark:hover:bg-gray-700'
                                                        }}">
                                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-black
                                                            {{ $company->id == $selectedCompany->id
                                                                ? 'bg-primary-600 text-white'
                                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-500'
                                                            }}">
                                                            {{ strtoupper(substr($company->name, 0, 1)) }}
                                                        </div>

                                                        <div class="text-left flex-1 min-w-0">
                                                            <p class="text-[12px] font-black truncate {{ $company->id == $selectedCompany->id ? 'text-primary-700 dark:text-primary-300' : 'text-gray-900 dark:text-white' }}">
                                                                {{ $company->name }}
                                                            </p>
                                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $company->status }}</span>
                                                        </div>
                                                        @if($company->id == $selectedCompany->id)
                                                            <i data-feather="check" class="w-3.5 h-3.5 text-primary-600"></i>
                                                        @endif
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>

                                        <div class="px-2 pt-2 mt-2 border-t border-gray-50 dark:border-gray-700/50">
                                            <a href="{{ route('companies.create') }}"
                                                class="flex items-center justify-center gap-2 p-2.5 text-[11px] font-black text-primary-600 uppercase tracking-widest hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl transition-all">
                                                <i data-feather="plus" class="w-3.5 h-3.5"></i>
                                                Register Company
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif


                            {{-- Cart --}}
                            @if(!$isAdminView)
                            <a href="{{ route('procurement.marketplace.cart') }}" class="relative p-2.5 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition-all text-gray-400 hover:text-primary-600">
                                <i data-feather="shopping-cart" class="w-5 h-5"></i>
                                @php
                                    $cartCount = count(session()->get('marketplace_cart', []));
                                @endphp
                                @if($cartCount > 0)
                                    <span class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white dark:border-gray-900 flex items-center justify-center"></span>
                                @endif
                            </a>
                            @endif

                            {{-- Notifications --}}
                            <div class="relative" id="notificationDropdown">
                                <button id="notificationButton"
                                    class="p-2.5 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition-all relative text-gray-400 hover:text-primary-600">
                                    <i data-feather="bell" class="w-5 h-5"></i>
                                    <span id="notificationBadge"
                                        class="absolute top-2 right-2 w-2.5 h-2.5 bg-red-500 rounded-full hidden border-2 border-white dark:border-gray-900 flex items-center justify-center"></span>
                                </button>

                                <div id="notificationMenu"
                                    class="absolute right-0 mt-3 w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 hidden overflow-hidden z-50">
                                    <div
                                        class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Notifications</h3>
                                        <button onclick="markAllNotificationsRead()"
                                            class="text-[10px] text-primary-600 hover:underline font-black uppercase tracking-widest">Mark All Read</button>
                                    </div>
                                    <div id="notificationList" class="max-h-80 overflow-y-auto">
                                        <div class="p-4 text-center text-gray-400 text-xs">Loading...</div>
                                    </div>
                                    <div class="p-3 border-t border-gray-100 dark:border-gray-700 text-center">
                                        <a href="{{ route('notifications.index') }}"
                                            class="text-[10px] font-black text-gray-500 dark:text-gray-400 hover:text-primary-600 uppercase tracking-widest">View All</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Switch Buyer/Vendor mode --}}
                            @if(auth()->check() && !$isAdminView)
                            <form action="{{ route('procurement.mode.switch') }}" method="POST" class="flex items-center">
                                @csrf
                                <input type="hidden" name="mode" value="{{ $procurementMode === 'buyer' ? 'vendor' : 'buyer' }}">
                                <button type="submit" 
                                    class="px-3 md:px-5 py-2 rounded-xl border font-bold text-[10px] transition-all uppercase tracking-widest
                                    {{ $procurementMode === 'buyer' 
                                        ? 'border-primary-600 text-primary-600 hover:bg-primary-600 hover:text-white' 
                                        : 'border-green-600 text-green-600 hover:bg-green-600 hover:text-white' }}">
                                    <span class="hidden md:inline">GO TO {{ $procurementMode === 'buyer' ? 'SELLING' : 'BUYING' }}</span>
                                    <span class="md:hidden">{{ $procurementMode === 'buyer' ? 'SELL' : 'BUY' }}</span>
                                </button>
                            </form>
                            @endif

                            {{-- Admin Logout --}}
                            @if($isAdminView)
                            <form action="{{ route('admin.logout') }}" method="POST" class="flex items-center">
                                @csrf
                                <button type="submit" 
                                    class="flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/10 text-red-600 rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all border border-red-100 dark:border-red-900/20">
                                    <i data-feather="log-out" class="w-3.5 h-3.5"></i>
                                    <span>Logout</span>
                                </button>
                            </form>
                            @endif

                            {{-- Dark Mode --}}
                            <button id="darkModeToggle"
                                class="p-2.5 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition-all text-gray-400 hover:text-primary-600 flex-shrink-0">
                                <i id="darkIcon" data-feather="moon" class="w-5 h-5"></i>
                            </button>

                        </div>
                    </div>
                </div>
            </header>
            @endif

            {{-- Skeleton Template (Hidden) --}}
            <template id="skeleton-template">
                <div class="animate-pulse space-y-8">
                    {{-- Header Skeleton --}}
                    <div class="flex items-center justify-between">
                        <div class="h-8 w-64 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                        <div class="h-10 w-32 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    </div>
                    
                    {{-- Banner/Card Skeleton --}}
                    <div class="h-32 w-full bg-gray-100 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700"></div>
                    
                    {{-- Table Skeleton --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="h-6 w-48 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        </div>
                        <div class="p-6 space-y-4">
                            @for($i = 0; $i < 5; $i++)
                                <div class="flex items-center justify-between py-2 border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                                        <div class="space-y-2">
                                            <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                            <div class="h-3 w-20 bg-gray-100 dark:bg-gray-800 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </template>

            @if(!$isAdminView)
                @include('layouts.partials.bottom-nav')
            @endif

            {{-- Progress Bar (at top of main) --}}
            <div id="global-progress" class="fixed top-0 left-0 md:left-64 right-0 h-1 bg-primary-600 z-[60] transition-all duration-300 opacity-0" style="width: 0%"></div>

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900 {{ auth()->check() && $procurementMode === 'buyer' ? 'pb-40' : '' }}" data-layout="app">
                <div class="w-full px-6 py-8" id="main-content-area">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Modular Scripts --}}
    @include('layouts.partials.scripts')

    @stack('scripts')

    {{-- SweetAlert2 is loaded via Vite bundle (window.Swal) --}}
</body>

</html>