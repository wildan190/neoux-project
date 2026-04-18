@extends('layouts.app', [
    'title' => 'My Submitted Offers',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'My Offers', 'url' => null],
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">SALES PIPELINE</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $offers->total() }} Active Bids</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Technical & Financial Proposals</h1>
        </div>
        <a href="{{ route('procurement.pr.public-feed') }}" 
           class="px-5 py-2.5 bg-gray-900 text-white dark:bg-white dark:text-gray-900 rounded-xl text-[11px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-xl shadow-gray-900/10">
            Browse Opportunities
        </a>
    </div>

    @if($offers->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-20 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i data-feather="send" class="w-10 h-10 text-gray-200"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">No Active Offers</h3>
            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase tracking-widest px-12 leading-relaxed">
                Start submitting technical proposals for open tenders to build your sales portfolio.
            </p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 mb-8">
            @foreach($offers as $offer)
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 p-8 shadow-sm transition-all hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-none relative group
                    @if($offer->status === 'accepted') ring-2 ring-emerald-500 @endif">
                    
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-3">
                                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $offer->purchaseRequisition->title }}</h3>
                                @if($offer->status === 'accepted')
                                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-black uppercase tracking-widest rounded-md">WINNER</span>
                                @elseif($offer->status === 'rejected')
                                    <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[8px] font-black uppercase tracking-widest rounded-md">REJECTED</span>
                                @else
                                    <span class="px-2 py-0.5 bg-primary-100 text-primary-700 text-[8px] font-black uppercase tracking-widest rounded-md">IN REVIEW</span>
                                @endif
                            </div>
                            
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 leading-relaxed">
                                CLIENT: {{ $offer->purchaseRequisition->company->name }} <br>
                                SUBMITTED ON: {{ $offer->created_at->format('d M Y, H:i') }}
                            </p>

                            <div class="flex items-center gap-6">
                                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-2 rounded-xl text-center min-w-[100px]">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Rank</p>
                                    <p class="text-lg font-black text-gray-900 dark:text-white">#{{ $offer->rank_position }}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-2 rounded-xl text-center min-w-[100px]">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Points</p>
                                    <p class="text-lg font-black text-gray-900 dark:text-white">{{ $offer->rank_score }}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-900 px-4 py-2 rounded-xl text-center min-w-[100px]">
                                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Items</p>
                                    <p class="text-lg font-black text-gray-900 dark:text-white">{{ $offer->items->count() }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col lg:items-end gap-3">
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Bid Amount</p>
                                <p class="text-3xl font-black text-primary-600 dark:text-primary-400">{{ $offer->formatted_total_price }}</p>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <a href="{{ route('procurement.offers.show', $offer) }}"
                                   class="px-5 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-100 transition-all border border-gray-100 dark:border-gray-700">
                                    Manage Proposal
                                </a>
                                @if($offer->purchaseOrder)
                                    <a href="{{ route('procurement.po.show', $offer->purchaseOrder) }}" 
                                       class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-emerald-600/20 hover:scale-105 transition-all">
                                        View Purchase Order
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($offers->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $offers->links() }}
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
@endpush
