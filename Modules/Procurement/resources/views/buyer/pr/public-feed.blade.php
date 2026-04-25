@extends('layouts.app', [
    'title' => 'Market Feed',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Market Feed', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-8">
    {{-- Header: Hero Section --}}
    <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white relative overflow-hidden shadow-2xl">
        <div class="absolute top-0 right-0 p-12 opacity-10 pointer-events-none">
            <i data-feather="globe" style="width:200px;height:200px;"></i>
        </div>
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 bg-primary-500 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">Global Network</span>
                    <div class="h-px w-8 bg-gray-700"></div>
                    <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Market Feed v2.0</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight leading-none">
                    PUBLIC <span class="text-primary-500 text-outline">TENDERS</span>
                </h1>
                <p class="text-gray-400 text-sm font-medium max-w-xl">
                    Explore live purchase requests from across the neoux network. Authenticate your company to participate in bidding and negotiations.
                </p>
            </div>
            
            @if(session('procurement_mode') === 'buyer')
            <div class="shrink-0">
                <a href="{{ route('procurement.pr.create') }}"
                   class="h-16 px-10 flex items-center bg-white text-gray-900 text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl hover:bg-primary-500 hover:text-white transition-all active:scale-[0.98]">
                    <i data-feather="plus" class="w-4 h-4 mr-3"></i>
                    Initialize Request
                </a>
            </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- LEFT PANEL: Filters & Stats --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- Navigation --}}
            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 p-2 shadow-sm">
                <div class="space-y-1">
                    @foreach([
                        ['id' => 'open', 'label' => 'Open Tenders', 'icon' => 'inbox', 'count' => $openCount],
                        ['id' => 'closed', 'label' => 'Historical', 'icon' => 'archive', 'count' => $closedCount],
                        ['id' => 'all', 'label' => 'All Activity', 'icon' => 'activity', 'count' => $openCount + $closedCount]
                    ] as $tab)
                    <a href="{{ route('procurement.pr.public-feed', ['filter' => $tab['id']]) }}" 
                       class="flex items-center justify-between px-5 py-4 rounded-2xl transition-all group {{ $filter === $tab['id'] ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/20' : 'text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <div class="flex items-center gap-3">
                            <i data-feather="{{ $tab['icon'] }}" class="w-4 h-4 {{ $filter === $tab['id'] ? 'text-white' : 'text-gray-400 group-hover:text-primary-500' }}"></i>
                            <span class="text-[11px] font-black uppercase tracking-widest">{{ $tab['label'] }}</span>
                        </div>
                        <span class="text-[10px] font-black {{ $filter === $tab['id'] ? 'bg-white/20' : 'bg-gray-100 dark:bg-gray-800' }} px-2 py-0.5 rounded-lg">{{ $tab['count'] }}</span>
                    </a>
                    @endforeach
                </div>
            </div>

            {{-- Market Insights --}}
            <div class="bg-emerald-50 dark:bg-emerald-900/10 rounded-3xl border border-emerald-100 dark:border-emerald-900/30 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-800 rounded-xl flex items-center justify-center text-emerald-600">
                        <i data-feather="trending-up" class="w-4 h-4"></i>
                    </div>
                    <p class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-widest">Market Pulse</p>
                </div>
                <p class="text-[10px] font-medium text-emerald-700/80 dark:text-emerald-400/80 leading-relaxed">
                    Active tenders are up <span class="font-black">12%</span> this week. Electronics and logistics services are in high demand.
                </p>
            </div>
        </div>

        {{-- CENTER PANEL: The Feed --}}
        <div class="lg:col-span-6 space-y-6">
            {{-- Search Bar --}}
            <div class="relative group">
                <form action="{{ route('procurement.pr.public-feed') }}" method="GET">
                    @if(request('filter'))
                        <input type="hidden" name="filter" value="{{ request('filter') }}">
                    @endif
                    <div class="absolute inset-y-0 left-0 pl-6 flex items-center pointer-events-none">
                        <i data-feather="search" class="h-4 w-4 text-gray-400 group-focus-within:text-primary-500 transition-colors"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                           class="block w-full pl-14 pr-6 py-5 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-3xl text-xs font-bold text-gray-900 dark:text-white placeholder-gray-400 focus:ring-4 focus:ring-primary-500/5 focus:border-primary-500 transition-all outline-none shadow-sm"
                           placeholder="SEARCH BY SKU, TITLE, OR COMPANY...">
                </form>
            </div>

            {{-- Feed Items --}}
            <div class="space-y-6">
                @forelse($requisitions as $pr)
                <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 overflow-hidden hover:shadow-2xl hover:shadow-gray-200/50 dark:hover:shadow-none transition-all group">
                    <div class="p-8">
                        {{-- Meta --}}
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-400 group-hover:bg-primary-50 group-hover:text-primary-600 transition-colors">
                                    <i data-feather="briefcase" class="w-5 h-5"></i>
                                </div>
                                <div>
                                    <h3 class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest">{{ $pr->company->name ?? 'System Request' }}</h3>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $pr->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            
                            @if($pr->status === 'open' || $pr->status === 'pending')
                                <span class="px-3 py-1 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 rounded-lg text-[9px] font-black uppercase tracking-widest border border-emerald-100 dark:border-emerald-800">LIVE TENDER</span>
                            @else
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 text-gray-500 rounded-lg text-[9px] font-black uppercase tracking-widest">CLOSED</span>
                            @endif
                        </div>

                        {{-- Content --}}
                        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-3 group-hover:text-primary-600 transition-colors">
                            {{ $pr->title }}
                        </h2>
                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 line-clamp-2 leading-relaxed mb-6">
                            {{ $pr->description ?: 'No detailed technical specifications provided for this requisition.' }}
                        </p>

                        {{-- Stats --}}
                        <div class="flex items-center gap-6 mb-8 py-4 border-y border-gray-50 dark:border-gray-800/50">
                            <div class="flex items-center gap-2">
                                <i data-feather="package" class="w-3.5 h-3.5 text-gray-400"></i>
                                <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ $pr->items->count() }} line items</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-feather="message-square" class="w-3.5 h-3.5 text-gray-400"></i>
                                <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ $pr->comments->count() }} discussions</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-feather="hash" class="w-3.5 h-3.5 text-gray-400"></i>
                                <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase">{{ $pr->pr_number }}</span>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex items-center gap-4">
                            @if($pr->status === 'open' || $pr->status === 'pending')
                                <a href="{{ route('procurement.pr.show-public', $pr) }}" 
                                   class="flex-1 h-14 flex items-center justify-center bg-gray-900 dark:bg-primary-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-black transition-all shadow-xl shadow-gray-900/10">
                                    PARTICIPATE IN TENDER
                                </a>
                            @else
                                <a href="{{ route('procurement.pr.show-public', $pr) }}" 
                                   class="flex-1 h-14 flex items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-gray-200 dark:hover:bg-gray-700 transition-all border border-gray-100 dark:border-gray-700">
                                    VIEW TENDER DETAILS
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="bg-gray-50 dark:bg-gray-900/10 rounded-[2.5rem] border-2 border-dashed border-gray-100 dark:border-gray-800 py-20 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 bg-white dark:bg-gray-900 rounded-3xl flex items-center justify-center text-gray-200 mb-6 shadow-sm">
                        <i data-feather="inbox" class="w-10 h-10"></i>
                    </div>
                    <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest mb-2">No Active Tenders Found</h3>
                    <p class="text-xs text-gray-400 max-w-xs">Try adjusting your search filters or check back later for new procurement requests.</p>
                </div>
                @endforelse

                {{-- Pagination --}}
                @if($requisitions->hasPages())
                    <div class="pt-6">
                        {{ $requisitions->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT PANEL: Trending & Info --}}
        <div class="lg:col-span-3 space-y-6">
            {{-- User Context --}}
            @auth
            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-primary-500 rounded-2xl flex items-center justify-center text-white font-black text-lg">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">{{ Auth::user()->name }}</p>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Active Participant</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-2xl">
                        <p class="text-[18px] font-black text-gray-900 dark:text-white">0</p>
                        <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Active Bids</p>
                    </div>
                    <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-2xl">
                        <p class="text-[18px] font-black text-emerald-500">0</p>
                        <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest">Awarded</p>
                    </div>
                </div>
            </div>
            @endauth

            {{-- Checklist / Help --}}
            <div class="bg-gray-900 dark:bg-black rounded-3xl p-6 text-white border border-gray-800">
                <p class="text-[10px] font-black text-primary-500 uppercase tracking-widest mb-4">Vendor Checklist</p>
                <ul class="space-y-4">
                    @foreach([
                        'Review technical specs',
                        'Check delivery timeline',
                        'Verify shipping terms',
                        'Submit competitive offer'
                    ] as $check)
                    <li class="flex items-center gap-3">
                        <div class="w-5 h-5 rounded-lg bg-white/5 flex items-center justify-center text-primary-500">
                            <i data-feather="check" class="w-3 h-3"></i>
                        </div>
                        <span class="text-[10px] font-medium text-gray-400">{{ $check }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush
@endsection
