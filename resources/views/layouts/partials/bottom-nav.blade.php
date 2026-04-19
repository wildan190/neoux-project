@php
    $currentRoute = request()->route() ? request()->route()->getName() : '';
    $procurementMode = session('procurement_mode', 'buyer');
@endphp

@auth
@if($procurementMode === 'buyer')
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[60] w-full max-w-lg px-6 md:px-0">
        <div class="bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border border-gray-100 dark:border-gray-800 rounded-[2rem] shadow-2xl shadow-gray-200/50 dark:shadow-black/50 p-2 flex items-center justify-between" id="bottomNav">
            
            {{-- Marketplace / Home --}}
            <a href="/" data-no-pjax
                class="flex-1 flex flex-col items-center gap-1 py-3 px-2 rounded-2xl transition-all relative group
                {{ request()->is('/') || $currentRoute === 'market.index' || $currentRoute === 'market.show' ? 'text-primary-600' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                <i data-feather="home" class="w-5 h-5 {{ request()->is('/') || $currentRoute === 'market.index' || $currentRoute === 'market.show' ? 'fill-primary-600/10' : '' }}"></i>
                <span class="text-[9px] font-black uppercase tracking-widest">Market</span>
                <div class="nav-dot absolute -bottom-1 w-1 h-1 bg-primary-600 rounded-full {{ request()->is('/') || $currentRoute === 'market.index' || $currentRoute === 'market.show' ? '' : 'hidden' }}"></div>
            </a>

            {{-- Requests (PR) --}}
            <a href="{{ route('procurement.pr.index') }}" 
                class="flex-1 flex flex-col items-center gap-1 py-3 px-2 rounded-2xl transition-all relative group
                {{ strpos($currentRoute, 'procurement.pr') !== false ? 'text-primary-600' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                <i data-feather="file-text" class="w-5 h-5 {{ strpos($currentRoute, 'procurement.pr') !== false ? 'fill-primary-600/10' : '' }}"></i>
                <span class="text-[9px] font-black uppercase tracking-widest">Requests</span>
                <div class="nav-dot absolute -bottom-1 w-1 h-1 bg-primary-600 rounded-full {{ strpos($currentRoute, 'procurement.pr') !== false ? '' : 'hidden' }}"></div>
            </a>

            {{-- Orders (PO) --}}
            <a href="{{ route('procurement.po.index') }}" 
                class="flex-1 flex flex-col items-center gap-1 py-3 px-2 rounded-2xl transition-all relative group
                {{ strpos($currentRoute, 'procurement.po') !== false ? 'text-primary-600' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                <i data-feather="package" class="w-5 h-5 {{ strpos($currentRoute, 'procurement.po') !== false ? 'fill-primary-600/10' : '' }}"></i>
                <span class="text-[9px] font-black uppercase tracking-widest">Orders</span>
                <div class="nav-dot absolute -bottom-1 w-1 h-1 bg-primary-600 rounded-full {{ strpos($currentRoute, 'procurement.po') !== false ? '' : 'hidden' }}"></div>
            </a>

            {{-- Negotiations --}}
            <a href="{{ route('procurement.offers.negotiations') }}" 
                class="flex-1 flex flex-col items-center gap-1 py-3 px-2 rounded-2xl transition-all relative group
                {{ strpos($currentRoute, 'procurement.offers.negotiations') !== false ? 'text-primary-600' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                <i data-feather="message-circle" class="w-5 h-5 {{ strpos($currentRoute, 'procurement.offers.negotiations') !== false ? 'fill-primary-600/10' : '' }}"></i>
                <span class="text-[9px] font-black uppercase tracking-widest">Nego</span>
                <div class="nav-dot absolute -bottom-1 w-1 h-1 bg-primary-600 rounded-full {{ strpos($currentRoute, 'procurement.offers.negotiations') !== false ? '' : 'hidden' }}"></div>
            </a>

            {{-- Invoices --}}
            <a href="{{ route('procurement.invoices.index') }}" 
                class="flex-1 flex flex-col items-center gap-1 py-3 px-2 rounded-2xl transition-all relative group
                {{ strpos($currentRoute, 'procurement.invoices') !== false ? 'text-primary-600' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                <i data-feather="credit-card" class="w-5 h-5 {{ strpos($currentRoute, 'procurement.invoices') !== false ? 'fill-primary-600/10' : '' }}"></i>
                <span class="text-[9px] font-black uppercase tracking-widest">Finance</span>
                <div class="nav-dot absolute -bottom-1 w-1 h-1 bg-primary-600 rounded-full {{ strpos($currentRoute, 'procurement.invoices') !== false ? '' : 'hidden' }}"></div>
            </a>

            {{-- Logistic (GR/DO) --}}
            <a href="{{ route('procurement.gr.index') }}" 
                class="flex-1 flex flex-col items-center gap-1 py-3 px-2 rounded-2xl transition-all relative group
                {{ strpos($currentRoute, 'procurement.gr') !== false || strpos($currentRoute, 'procurement.do') !== false ? 'text-primary-600' : 'text-gray-400 hover:text-gray-600 dark:hover:text-gray-200' }}">
                <i data-feather="truck" class="w-5 h-5 {{ strpos($currentRoute, 'procurement.gr') !== false ? 'fill-primary-600/10' : '' }}"></i>
                <span class="text-[9px] font-black uppercase tracking-widest">Logistic</span>
                <div class="nav-dot absolute -bottom-1 w-1 h-1 bg-primary-600 rounded-full {{ strpos($currentRoute, 'procurement.gr') !== false || strpos($currentRoute, 'procurement.do') !== false ? '' : 'hidden' }}"></div>
            </a>

        </div>
    </div>
@endif
@endauth
