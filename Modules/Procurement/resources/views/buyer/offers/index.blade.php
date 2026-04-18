@extends('layouts.app', [
    'title' => 'Offers for: ' . $purchaseRequisition->title,
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'My Requests', 'url' => route('procurement.pr.my-requests')],
        ['name' => 'Offers', 'url' => null],
    ]
])

@section('content')
    @php
        $canApprove = Auth::user()->hasCompanyPermission($purchaseRequisition->company_id, 'approve pr');
    @endphp

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">TENDER ANALYSIS</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $offers->count() }} Offers Received</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $purchaseRequisition->title }}</h1>
        </div>
        <a href="{{ route('procurement.pr.show', $purchaseRequisition) }}"
            class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 transition-all">
            Review PR Details
        </a>
    </div>

    {{-- Winner Notice --}}
    @if($purchaseRequisition->winningOffer)
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-3xl p-8 mb-8 flex items-center gap-6 shadow-sm">
            <div class="w-16 h-16 bg-emerald-600 text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-600/20">
                <i data-feather="award" class="w-8 h-8"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-emerald-900 dark:text-emerald-200 uppercase tracking-tight">Winner Selected</h3>
                <p class="text-sm text-emerald-700 dark:text-emerald-300 mt-1">
                    <strong>{{ $purchaseRequisition->winningOffer->company->name }}</strong> has been awarded this tender for <strong>{{ $purchaseRequisition->winningOffer->formatted_total_price }}</strong>
                </p>
            </div>
        </div>
    @endif

    {{-- System Recommendation & Comparison --}}
    @if($offers->count() > 1 && !$purchaseRequisition->winningOffer)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            {{-- AI Recommendation Card --}}
            @php
                $recommendedOffer = $offers->where('is_recommended', true)->first();
            @endphp
            
            <div class="lg:col-span-1">
                @if($recommendedOffer)
                    <div class="bg-gray-900 rounded-[2.5rem] p-10 text-white h-full relative overflow-hidden shadow-2xl">
                        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary-600/20 rounded-full blur-[80px]"></div>
                        
                        <div class="relative z-10 flex flex-col h-full">
                            <div class="bg-primary-600 self-start px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest mb-6">AI RECOMMENDATION</div>
                            
                            <h3 class="text-2xl font-black mb-2 uppercase tracking-tight leading-tight">{{ $recommendedOffer->company->name }}</h3>
                            <div class="text-3xl font-black text-primary-400 mb-8">{{ $recommendedOffer->formatted_total_price }}</div>
                            
                            <div class="space-y-4 mb-10 flex-1">
                                <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Rank Score</span>
                                    <span class="text-lg font-black">{{ $recommendedOffer->rank_score }}</span>
                                </div>
                                <div class="flex items-center justify-between p-4 bg-white/5 rounded-2xl border border-white/10">
                                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Qty Match</span>
                                    <span class="text-lg font-black">{{ number_format(($recommendedOffer->items->sum('quantity_offered') / max(1, $purchaseRequisition->items->sum('quantity'))) * 100) }}%</span>
                                </div>
                            </div>
                            
                            <a href="#offer-{{ $recommendedOffer->id }}" class="w-full py-4 bg-white text-gray-900 hover:bg-gray-100 font-black rounded-2xl text-[11px] uppercase tracking-widest text-center transition-all shadow-xl">
                                Review Offer
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-10 h-full border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center shadow-sm">
                        <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-3xl flex items-center justify-center mb-6">
                            <i data-feather="cpu" class="w-10 h-10 text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">In-Depth Analysis</h3>
                        <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase tracking-widest leading-relaxed px-6">
                            Our AI is processing offers across 7 reliability and price dimensions.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Comparison Matrix --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm flex flex-col">
                <div class="p-8 border-b border-gray-50 dark:border-gray-700/50 flex justify-between items-center">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] flex items-center gap-2">
                        COMPARISON MATRIX (TOP 3)
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="border-b border-gray-50 dark:border-gray-700">
                                <th class="px-8 py-4 text-[9px] font-black text-gray-400 uppercase tracking-widest">Criteria</th>
                                @foreach($offers->take(3) as $offer)
                                    <th class="px-8 py-6">
                                        <div class="font-black text-gray-900 dark:text-white text-[11px] uppercase tracking-tight">{{ $offer->company->name }}</div>
                                        <div class="text-[10px] text-primary-500 font-black uppercase tracking-widest mt-1">RANK #{{ $loop->iteration }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            <tr>
                                <td class="px-8 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">Total Price</td>
                                @php $minPrice = $offers->min('total_price'); @endphp
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-8 py-4">
                                        <span class="{{ $offer->total_price == $minPrice ? 'text-emerald-600 font-black' : 'text-gray-900 dark:text-gray-300 font-bold' }} text-[11px]">
                                            {{ $offer->formatted_total_price }}
                                        </span>
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="px-8 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">Match Score</td>
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-8 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-1 bg-gray-100 dark:bg-gray-700 rounded-full h-1.5 w-24 overflow-hidden">
                                                <div class="bg-primary-600 h-full" style="width: {{ $offer->rank_score }}%"></div>
                                            </div>
                                            <span class="font-black text-[11px] text-gray-900 dark:text-white">{{ $offer->rank_score }}</span>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                            <tr>
                                <td class="px-8 py-4 text-[10px] font-black text-gray-500 uppercase tracking-widest">Delivery</td>
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-8 py-4">
                                        <div class="text-[11px] font-bold text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $offer->delivery_time ?? 'N/A' }}</div>
                                    </td>
                                @endforeach
                            </tr>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/10">
                                <td class="px-8 py-6"></td>
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-8 py-6">
                                        @if($offer->status === 'pending' && $canApprove)
                                            <form action="{{ route('procurement.offers.accept', $offer) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                    onclick="return confirm('Accept this offer?')"
                                                    class="px-4 py-2 bg-gray-900 text-white dark:bg-white dark:text-gray-900 rounded-xl text-[9px] font-black uppercase tracking-widest hover:scale-105 transition-all">
                                                    Award Tender
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">{{ $offer->status }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Offers List --}}
    @if($offers->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-20 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
            <i data-feather="inbox" class="w-16 h-16 text-gray-200 dark:text-gray-700 mx-auto mb-6"></i>
            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">No Bids Yet</h3>
            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase tracking-widest">Waiting for vendors to submit technical & financial proposals.</p>
        </div>
    @else
        <div class="space-y-6">
            @foreach($offers as $offer)
                <div id="offer-{{ $offer->id }}" class="scroll-mt-24 bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 p-8 shadow-sm transition-all hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-none relative group
                                @if($offer->status === 'accepted') ring-2 ring-emerald-500 @endif">
                    
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div class="flex items-start gap-6">
                            <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-xl font-black
                                            @if($offer->rank_position == 1) bg-primary-600 text-white
                                            @elseif($offer->rank_position == 2) bg-gray-900 text-white
                                            @else bg-gray-50 text-gray-400 @endif shadow-lg shadow-gray-200/20 dark:shadow-none">
                                #{{ $offer->rank_position }}
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $offer->company->name }}</h3>
                                    @if($offer->status === 'accepted')
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[8px] font-black uppercase tracking-widest rounded-md">WINNER</span>
                                    @endif
                                    @if($offer->is_recommended)
                                        <span class="px-2 py-0.5 bg-primary-100 text-primary-700 text-[8px] font-black uppercase tracking-widest rounded-md">BEST VALUE</span>
                                    @endif
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">
                                    BY {{ $offer->user->name }} • {{ $offer->created_at->diffForHumans() }}
                                </p>
                                
                                <div class="flex flex-wrap gap-2">
                                    @foreach($offer->items->take(3) as $item)
                                        <span class="px-3 py-1 bg-gray-50 dark:bg-gray-900 rounded-lg text-[9px] font-black text-gray-500 uppercase tracking-tight">
                                            {{ $item->quantity_offered }}x {{ $item->purchaseRequisitionItem->catalogueItem->name }}
                                        </span>
                                    @endforeach
                                    @if($offer->items->count() > 3)
                                        <span class="px-3 py-1 bg-gray-50 dark:bg-gray-900 rounded-lg text-[9px] font-black text-gray-400 uppercase tracking-tight">+{{ $offer->items->count() - 3 }} MORE</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col lg:items-end gap-3">
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Financial Proposal</p>
                                <p class="text-2xl font-black text-primary-600 dark:text-primary-400">{{ $offer->formatted_total_price }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <a href="{{ route('procurement.offers.show', $offer) }}"
                                   class="px-5 py-2.5 bg-gray-50 dark:bg-gray-900 text-gray-900 dark:text-white rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-gray-100 transition-all border border-gray-100 dark:border-gray-700">
                                    View Details
                                </a>
                                
                                @if($offer->status === 'pending' || $offer->status === 'negotiating')
                                    <form action="{{ route('procurement.offers.accept', $offer) }}" method="POST">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Award this tender to {{ $offer->company->name }}?')"
                                            class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:scale-105 transition-all">
                                            Select Winner
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
@endpush
