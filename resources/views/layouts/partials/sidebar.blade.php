<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-gray-900 to-gray-800 dark:from-gray-950 dark:to-gray-900 shadow-2xl transform transition-transform duration-300 flex flex-col -translate-x-full md:translate-x-0">

    {{-- Company Brand --}}
    <div class="px-6 py-6 border-b border-gray-700/50">
        @php
            $selectedCompanyId = session('selected_company_id');
            $userCompanies = auth()->user()->allCompanies();
            $selectedCompany = $userCompanies->firstWhere('id', $selectedCompanyId);
        @endphp

        @if($selectedCompany)
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg text-white font-bold text-lg">
                    {{ substr($selectedCompany->name, 0, 1) }}
                </div>
                <div class="text-left overflow-hidden">
                    <h2 class="text-sm font-bold text-white truncate w-32">{{ $selectedCompany->name }}</h2>
                    <p class="text-xs text-gray-400 truncate w-32">{{ $selectedCompany->category }}</p>
                </div>
            </div>
        @else
            {{-- Default Brand if No Company Selected --}}
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-6 h-6 text-white">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">NeoUX</h2>
                    <p class="text-xs text-gray-400">Platform by HUNTR</p>
                </div>
            </div>
        @endif
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        @php
            $dashboardRoute = session('selected_company_id') ? route('company.dashboard') : route('dashboard');
            $isDashboardActive = request()->routeIs('dashboard') || request()->routeIs('company.dashboard');
        @endphp

        <a href="{{ $dashboardRoute }}"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ $isDashboardActive ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ $isDashboardActive ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-5 h-5">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                    <polyline points="9 22 9 12 15 12 15 22"></polyline>
                </svg>
            </div>
            <span class="ml-3">Dashboard</span>
            @if(isset($sidebarCounts['notifications']) && $sidebarCounts['notifications'] > 0)
                <span
                    class="ml-auto inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-red-500 rounded-lg shadow-lg">
                    {{ $sidebarCounts['notifications'] }}
                </span>
            @endif
        </a>

        <a href="{{ route('companies.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('companies.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('companies.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-5 h-5">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                </svg>
            </div>
            <span class="ml-3">Companies</span>
        </a>

        {{-- Catalogue Menu - Only show if logged in as approved/active company --}}
        @php
            $selectedCompanyId = session('selected_company_id');
            $showCatalogue = false;
            $selectedCompany = null;
            if ($selectedCompanyId) {
                $selectedCompany = \Modules\Company\Models\Company::find($selectedCompanyId);
                $showCatalogue = $selectedCompany && in_array($selectedCompany->status, ['approved', 'active']);
            }
        @endphp

        @if($showCatalogue)
            <a href="{{ route('catalogue.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('catalogue.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('catalogue.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <line x1="16.5" y1="9.4" x2="7.5" y2="4.21"></line>
                        <path
                            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z">
                        </path>
                        <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
                        <line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                </div>
                <span class="ml-3">Catalogue</span>
            </a>
        @endif

        {{-- Warehouse Menu (Same rule as Catalogue) --}}
        @if($showCatalogue)
            <a href="{{ route('warehouse.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('warehouse.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('warehouse.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <path d="M3 3v18h18"></path>
                        <path d="M18.7 8l-5.1 5.2-2.8-2.7L7 14.3"></path>
                    </svg>
                </div>
                <span class="ml-3">Warehouse</span>
            </a>
        @endif

        {{-- Marketplace Menu --}}
        @if($showCatalogue)
            <a href="{{ route('procurement.marketplace.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.marketplace.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.marketplace.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                </div>
                <span class="ml-3">Marketplace</span>
            </a>
        @endif

        @if(session('selected_company_id'))
            {{-- Procurement Section (Buying) --}}
            <div class="px-4 pt-6 pb-2">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Procurement (Buying)</p>
            </div>

            <a href="{{ route('procurement.pr.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.pr.index') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.pr.index') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                <span class="ml-3">My Requisitions</span>
                @if(isset($sidebarCounts['my_requisitions']) && $sidebarCounts['my_requisitions'] > 0)
                    <span
                        class="ml-auto inline-flex items-center justify-center px-1.5 h-4 text-[9px] font-bold text-white bg-primary-500 rounded-md">
                        {{ $sidebarCounts['my_requisitions'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('procurement.po.index', ['view' => 'buyer']) }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->fullUrlIs(route('procurement.po.index', ['view' => 'buyer']) . '*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->fullUrlIs(route('procurement.po.index', ['view' => 'buyer']) . '*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                </div>
                <span class="ml-3">Purchase Orders</span>
                @if(isset($sidebarCounts['purchase_orders_buyer']) && $sidebarCounts['purchase_orders_buyer'] > 0)
                    <span
                        class="ml-auto inline-flex items-center justify-center px-1.5 h-4 text-[9px] font-bold text-white bg-primary-500 rounded-md">
                        {{ $sidebarCounts['purchase_orders_buyer'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('procurement.invoices.index', ['view' => 'buyer']) }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->fullUrlIs(route('procurement.invoices.index', ['view' => 'buyer']) . '*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->fullUrlIs(route('procurement.invoices.index', ['view' => 'buyer']) . '*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                </div>
                <span class="ml-3">Invoices (Pay)</span>
                @if(isset($sidebarCounts['invoices_buyer']) && $sidebarCounts['invoices_buyer'] > 0)
                    <span
                        class="ml-auto inline-flex items-center justify-center px-1.5 h-4 text-[9px] font-bold text-white bg-primary-500 rounded-md">
                        {{ $sidebarCounts['invoices_buyer'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('procurement.grr.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.grr.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.grr.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <polyline points="23 4 23 10 17 10"></polyline>
                        <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path>
                    </svg>
                </div>
                <span class="ml-3">Goods Return</span>
                @if(isset($sidebarCounts['grr']) && $sidebarCounts['grr'] > 0)
                    <span
                        class="ml-auto inline-flex items-center justify-center px-1.5 h-4 text-[9px] font-bold text-white bg-primary-500 rounded-md">
                        {{ $sidebarCounts['grr'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('procurement.debit-notes.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.debit-notes.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.debit-notes.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <span class="ml-3">Debit Notes</span>
                @if(isset($sidebarCounts['debit_notes']) && $sidebarCounts['debit_notes'] > 0)
                    <span
                        class="ml-auto inline-flex items-center justify-center px-1.5 h-4 text-[9px] font-bold text-white bg-primary-500 rounded-md">
                        {{ $sidebarCounts['debit_notes'] }}
                    </span>
                @endif
            </a>

            {{-- Sales Section (Selling) --}}
            <div class="px-4 pt-6 pb-2 border-t border-gray-700/50 mt-4">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Sales (Selling)</p>
            </div>

            <a href="{{ route('procurement.pr.public-feed') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.pr.public-feed') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.pr.public-feed') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="2" y1="12" x2="22" y2="12"></line>
                        <path
                            d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z">
                        </path>
                    </svg>
                </div>
                <span class="ml-3">Opportunity Feed</span>
                @if(isset($sidebarCounts['all_requests']) && $sidebarCounts['all_requests'] > 0)
                    <span
                        class="ml-auto inline-flex items-center justify-center px-1.5 h-4 text-[9px] font-bold text-white bg-primary-500 rounded-md">
                        {{ $sidebarCounts['all_requests'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('procurement.offers.my') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.offers.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.offers.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                        <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
                    </svg>
                </div>
                <span class="ml-3">My Offers</span>
                @if(isset($sidebarCounts['my_offers']) && $sidebarCounts['my_offers'] > 0)
                    <span
                        class="ml-auto inline-flex items-center justify-center px-1.5 h-4 text-[9px] font-bold text-white bg-primary-500 rounded-md">
                        {{ $sidebarCounts['my_offers'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('procurement.po.index', ['view' => 'vendor']) }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->fullUrlIs(route('procurement.po.index', ['view' => 'vendor']) . '*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->fullUrlIs(route('procurement.po.index', ['view' => 'vendor']) . '*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                </div>
                <span class="ml-3">Incoming Orders</span>
                @if(isset($sidebarCounts['purchase_orders_vendor']) && $sidebarCounts['purchase_orders_vendor'] > 0)
                    <span
                        class="ml-auto inline-flex items-center justify-center px-1.5 h-4 text-[9px] font-bold text-white bg-primary-500 rounded-md">
                        {{ $sidebarCounts['purchase_orders_vendor'] }}
                    </span>
                @endif
            </a>

            <a href="{{ route('procurement.invoices.index', ['view' => 'vendor']) }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->fullUrlIs(route('procurement.invoices.index', ['view' => 'vendor']) . '*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->fullUrlIs(route('procurement.invoices.index', ['view' => 'vendor']) . '*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                </div>
                <span class="ml-3">Sent Invoices</span>
                @if(isset($sidebarCounts['invoices_vendor']) && $sidebarCounts['invoices_vendor'] > 0)
                    <span
                        class="ml-auto inline-flex items-center justify-center px-1.5 h-4 text-[9px] font-bold text-white bg-primary-500 rounded-md">
                        {{ $sidebarCounts['invoices_vendor'] }}
                    </span>
                @endif
            </a>
        @endif

        @if(session('selected_company_id'))
            <a href="{{ route('team.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('team.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('team.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-5 h-5">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <span class="ml-3">Team</span>
            </a>
        @endif

        <a href="{{ route('settings.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('settings.*') ? 'bg-primary-600/10 text-primary-500' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('settings.*') ? 'bg-primary-600/20 text-primary-500' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                    class="w-5 h-5">
                    <circle cx="12" cy="12" r="3"></circle>
                    <path
                        d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z">
                    </path>
                </svg>
            </div>
            <span class="ml-3">Settings</span>
        </a>
    </nav>

    {{-- User Profile --}}
    <div class="p-4 border-t border-gray-700/50">
        <div class="bg-gray-700/30 rounded-xl p-3 backdrop-blur-sm">
            <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 hover:opacity-80 transition">
                @if(Auth::user()->userDetail && Auth::user()->userDetail->profile_photo_url)
                    <img src="{{ Auth::user()->userDetail->profile_photo_url }}" alt="{{ Auth::user()->name }}"
                        class="w-10 h-10 rounded-lg object-cover shadow-lg">
                @else
                    <div
                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold shadow-lg">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ Auth::user()->email }}</p>
                </div>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mt-3">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center space-x-2 px-3 py-2 bg-gray-600/50 hover:bg-gray-600 rounded-lg text-xs font-medium text-gray-200 hover:text-white transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                        class="w-4 h-4">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                        <polyline points="16 17 21 12 16 7"></polyline>
                        <line x1="21" y1="12" x2="9" y2="12"></line>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

</aside>