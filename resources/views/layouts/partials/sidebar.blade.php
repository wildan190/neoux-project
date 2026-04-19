<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-900 border-r border-gray-100 dark:border-gray-800 shadow-xl shadow-gray-200/20 transform transition-transform duration-300 flex flex-col -translate-x-full md:translate-x-0">

    {{-- Company Brand --}}
    {{-- Brand Section --}}
    <div class="px-5 py-5 border-b border-gray-100 dark:border-gray-800/40">
        <div class="flex items-center gap-3 group">
            <div class="w-8 h-8 rounded-lg bg-gray-900 dark:bg-gray-800 flex items-center justify-center shadow-lg transition-transform group-hover:scale-110">
                <i data-feather="box" class="w-4 h-4 text-white"></i>
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-bold text-gray-900 dark:text-white tracking-tighter uppercase tracking-[0.1em]">Huntr<span class="text-primary-600">.id</span></h2>
                <p class="text-[8px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] truncate">Powered by HUNTR</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        @php
            $isBuyer = ($procurementMode === 'buyer');
            $dashboardRoute = session('selected_company_id') ? ($isBuyer ? url('/') : route('company.dashboard')) : route('dashboard');
            $isDashboardActive = request()->is('/') || request()->routeIs('dashboard') || request()->routeIs('company.dashboard');
        @endphp

        <a href="{{ $dashboardRoute }}" data-no-pjax
            class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group {{ $isDashboardActive ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
            <div
                class="w-8 h-8 rounded-lg flex items-center justify-center {{ $isDashboardActive ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-800 group-hover:bg-primary-50 dark:group-hover:bg-primary-900/20' }} transition-colors">
                <i data-feather="home" class="w-5 h-5"></i>
            </div>
            <span class="ml-3">Dashboard</span>
            @php
                $notifCount = $sidebarCounts['notifications'] ?? 0;
                $showNotifBadge = $notifCount > 0 && !$isDashboardActive;
            @endphp
            <span id="badge-notifications"
                class="ml-auto inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-red-500 rounded-full shadow-lg {{ $showNotifBadge ? '' : 'hidden' }}"
                {!! $showNotifBadge ? '' : 'style="display: none"' !!}>
                {{ $showNotifBadge ? $notifCount : '' }}
            </span>
        </a>

        <a href="{{ route('companies.index') }}"
            class="flex items-center px-4 py-2.5 rounded-xl text-sm font-bold transition-all duration-200 group {{ request()->routeIs('companies.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('companies.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <i data-feather="briefcase" class="w-5 h-5"></i>
            </div>
            <span class="ml-3">Companies</span>
        </a>

        {{-- Marketplace Menu --}}
        @php
            $selectedCompanyId = session('selected_company_id');
            $showCatalogue = false;
            $selectedCompany = null;
            if ($selectedCompanyId) {
                $selectedCompany = \Modules\Company\Models\Company::find($selectedCompanyId);
                $showCatalogue = $selectedCompany && in_array($selectedCompany->status, ['approved', 'active']);
            }
        @endphp


        {{-- Marketplace Filters (Visible to Buyers) --}}
        @if($isBuyer)
        <div class="px-4 pt-4 pb-2 space-y-2">
            <p class="text-[10px] font-bold text-primary-600 dark:text-primary-400 uppercase tracking-widest px-2 mb-4">Marketplace Navigation</p>
            
            {{-- Quick Search --}}
            <div class="px-2 mb-6">
                <form action="{{ url('/') }}" method="GET" class="relative">
                    <i data-feather="search" class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Quick search..." 
                        class="w-full pl-9 pr-3 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-xs focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all outline-none text-gray-900 dark:text-white">
                </form>
            </div>

            {{-- Category Filter --}}
            <div class="space-y-1">
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest px-2 mb-2">Categories</p>
                <a href="{{ url('/') }}" 
                    class="flex items-center px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group {{ !request('category') ? 'bg-primary-50 dark:bg-primary-900/10 text-primary-600' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600' }}">
                    <i data-feather="grid" class="w-4 h-4 mr-3"></i>
                    <span>All Products</span>
                </a>
                @foreach($sidebarCategories ?? [] as $cat)
                    <a href="{{ url('/?category=' . $cat->slug) }}" 
                        class="flex items-center px-4 py-2.5 rounded-xl text-xs font-bold transition-all duration-200 group {{ request('category') == $cat->slug ? 'bg-primary-50 dark:bg-primary-900/10 text-primary-600' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600' }}">
                        <i data-feather="chevron-right" class="w-3.5 h-3.5 mr-3 opacity-30 group-hover:opacity-100 transition-opacity"></i>
                        <span class="truncate">{{ $cat->name }}</span>
                    </a>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Procurement Section (Buying) --}}
        @if($isBuyer)
            <div class="px-6 pt-6 pb-2">
                <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Procurement</p>
            </div>

            <div class="px-3 space-y-1">
                <a href="{{ route('procurement.pr.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.pr.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="file-text" class="w-4 h-4 mr-3"></i>
                    <span>All Requests (RFQs)</span>
                </a>

                <a href="{{ route('procurement.offers.negotiations') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.offers.negotiations') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="message-circle" class="w-4 h-4 mr-3"></i>
                    <span>Negotiations</span>
                </a>

                <a href="{{ route('procurement.po.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.po.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="shopping-cart" class="w-4 h-4 mr-3"></i>
                    <span>Purchase Orders</span>
                </a>

                <a href="{{ route('procurement.invoices.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.invoices.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="dollar-sign" class="w-4 h-4 mr-3"></i>
                    <span>Invoices</span>
                </a>

                <a href="{{ route('procurement.grr.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.grr.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="refresh-cw" class="w-4 h-4 mr-3"></i>
                    <span>Return Requests</span>
                </a>

                <a href="{{ route('procurement.debit-notes.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.debit-notes.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="file-minus" class="w-4 h-4 mr-3"></i>
                    <span>Debit Notes</span>
                </a>

                <a href="{{ route('procurement.approvals.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.approvals.index') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="check-circle" class="w-4 h-4 mr-3"></i>
                    <span>Approvals</span>
                </a>
            </div>
        @endif

        {{-- Sales Section (Selling) --}}
        @if($showCatalogue && $procurementMode === 'vendor')
            <div class="px-6 pt-6 pb-2">
                <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Operations</p>
            </div>

            <div class="px-3 space-y-1">
                <a href="{{ route('catalogue.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('catalogue.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="package" class="w-4 h-4 mr-3"></i>
                    <span>Catalogue</span>
                </a>

                <a href="{{ route('warehouse.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('warehouse.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="database" class="w-4 h-4 mr-3"></i>
                    <span>Warehouse</span>
                </a>

                <a href="{{ route('procurement.approvals.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.approvals.index') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="check-circle" class="w-4 h-4 mr-3"></i>
                    <span>Approvals</span>
                </a>
            </div>

            <div class="px-6 pt-6 pb-2">
                <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Transactions</p>
            </div>

            <div class="px-3 space-y-1">
                <a href="{{ route('procurement.pr.public-feed') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.pr.public-feed') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="globe" class="w-4 h-4 mr-3"></i>
                    <span>Public RFQs</span>
                </a>

                <a href="{{ route('procurement.offers.my') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.offers.*') && !request()->routeIs('procurement.offers.negotiations') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="tag" class="w-4 h-4 mr-3"></i>
                    <span>My Offers</span>
                </a>

                <a href="{{ route('procurement.offers.negotiations') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.offers.negotiations') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="message-circle" class="w-4 h-4 mr-3"></i>
                    <span>Negotiations</span>
                </a>

                <a href="{{ route('procurement.po.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.po.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="shopping-cart" class="w-4 h-4 mr-3"></i>
                    <span>Purchase Orders</span>
                </a>

                <a href="{{ route('procurement.invoices.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.invoices.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="dollar-sign" class="w-4 h-4 mr-3"></i>
                    <span>Invoices</span>
                </a>

                <a href="{{ route('procurement.grr.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.grr.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="refresh-cw" class="w-4 h-4 mr-3"></i>
                    <span>Return Requests</span>
                </a>

                <a href="{{ route('procurement.debit-notes.index') }}"
                    class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('procurement.debit-notes.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                    <i data-feather="file-minus" class="w-4 h-4 mr-3"></i>
                    <span>Debit Notes</span>
                </a>
            </div>
        @endif

        <div class="px-6 pt-6 pb-2">
            <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em]">Account Management</p>
        </div>

        <div class="px-3 space-y-1">
            <a href="{{ route('team.index') }}"
                class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('team.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                <i data-feather="users" class="w-4 h-4 mr-3"></i>
                <span>Team Members</span>
            </a>

            <a href="{{ route('settings.index') }}"
                class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('settings.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                <i data-feather="settings" class="w-4 h-4 mr-3"></i>
                <span>Settings</span>
            </a>

            <a href="{{ route('support.index') }}"
                class="flex items-center px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('support.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-primary-600 dark:hover:text-primary-400' }}">
                <i data-feather="life-buoy" class="w-4 h-4 mr-3"></i>
                <span>Bantuan / Support</span>
            </a>
        </div>
    </nav>

    {{-- User Profile --}}
    @if(auth()->check())
    <div class="p-4 border-t border-gray-100 dark:border-gray-800/50">
        <div class="bg-gray-50/50 dark:bg-gray-800/30 rounded-2xl p-3 border border-gray-100 dark:border-gray-800">
            <a href="{{ route('profile.show') }}" class="flex items-center space-x-3 hover:opacity-80 transition group">
                @if(auth()->user()->userDetail && auth()->user()->userDetail->profile_photo_url)
                    <img src="{{ auth()->user()->userDetail->profile_photo_url }}" alt="{{ auth()->user()->name }}"
                        class="w-8 h-8 rounded-lg object-cover shadow-sm">
                @else
                    <div
                        class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center text-white font-bold text-[10px] shadow-lg shadow-primary-600/20">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="text-[10px] font-bold text-gray-900 dark:text-white truncate tracking-tight">{{ auth()->user()->name }}</p>
                    <p class="text-[8px] font-medium text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
                <i data-feather="chevron-right" class="w-3 h-3 text-gray-300 group-hover:text-primary-500 transition-colors"></i>
            </a>
            <div class="mt-3 grid grid-cols-2 gap-2">
                <a href="{{ route('settings.index') }}"
                    class="flex items-center justify-center space-x-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl text-[9px] font-bold text-gray-500 hover:text-primary-600 transition-all duration-200">
                    <i data-feather="settings" class="w-3 h-3"></i>
                    <span>SETTINGS</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center space-x-2 px-3 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-xl text-[9px] font-bold text-gray-500 hover:text-red-600 transition-all duration-200">
                        <i data-feather="log-out" class="w-3 h-3"></i>
                        <span>SIGN OUT</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</aside>