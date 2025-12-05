<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-gray-900 to-gray-800 dark:from-gray-950 dark:to-gray-900 shadow-2xl transform transition-transform duration-300 flex flex-col -translate-x-full md:translate-x-0">

    {{-- Logo & Brand --}}
    <div class="p-6 border-b border-gray-700/50">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg">
                <i data-feather="zap" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">NeoUX</h2>
                <p class="text-xs text-gray-400">Platform by HUNTR</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <a href="/dashboard"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('dashboard') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <i data-feather="home" class="w-5 h-5"></i>
            </div>
            <span class="ml-3">Dashboard</span>
        </a>

        <a href="{{ route('companies.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('companies.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('companies.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <i data-feather="briefcase" class="w-5 h-5"></i>
            </div>
            <span class="ml-3">Companies</span>
        </a>

        {{-- Catalogue Menu - Only show if logged in as approved/active company --}}
        @php
            $selectedCompanyId = session('selected_company_id');
            $showCatalogue = false;
            $selectedCompany = null;
            if ($selectedCompanyId) {
                $selectedCompany = \App\Modules\Company\Domain\Models\Company::find($selectedCompanyId);
                $showCatalogue = $selectedCompany && in_array($selectedCompany->status, ['approved', 'active']);
            }
        @endphp

        @if($showCatalogue)
            <a href="{{ route('catalogue.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('catalogue.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('catalogue.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <i data-feather="package" class="w-5 h-5"></i>
                </div>
                <span class="ml-3">Catalogue</span>
            </a>
        @endif

        @if(session('selected_company_id'))
            {{-- Procurement Menu - Only show when company is selected --}}
            <a href="{{ route('procurement.pr.public-feed') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.pr.public-feed') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.pr.public-feed') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <i data-feather="globe" class="w-5 h-5"></i>
                </div>
                <span class="ml-3">All Requests</span>
            </a>

            <a href="{{ route('procurement.pr.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.pr.index') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.pr.index') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <i data-feather="shopping-cart" class="w-5 h-5"></i>
                </div>
                <span class="ml-3">Purchase Requisitions</span>
            </a>

            <a href="{{ route('procurement.po.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.po.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.po.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <i data-feather="file-text" class="w-5 h-5"></i>
                </div>
                <span class="ml-3">Purchase Orders</span>
            </a>

            <a href="{{ route('procurement.invoices.index') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.invoices.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.invoices.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <i data-feather="credit-card" class="w-5 h-5"></i>
                </div>
                <span class="ml-3">Invoices</span>
            </a>

            <a href="{{ route('procurement.offers.my') }}"
                class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('procurement.offers.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
                <div
                    class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('procurement.offers.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                    <i data-feather="briefcase" class="w-5 h-5"></i>
                </div>
                <span class="ml-3">My Offers</span>
            </a>
        @endif

        <a href="/settings"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group text-gray-300 hover:bg-gray-700/50 hover:text-white">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center bg-gray-700/50 group-hover:bg-gray-600/50 transition-colors">
                <i data-feather="settings" class="w-5 h-5"></i>
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
                    <i data-feather="log-out" class="w-4 h-4"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>

</aside>