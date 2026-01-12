@extends('layouts.app', [
    'title' => 'Offers for: ' . $purchaseRequisition->title,
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'My Requests', 'url' => route('procurement.pr.my-requests')],
        ['name' => 'Offers', 'url' => null],
    ]
])

@section('content')
    {{-- PR Summary Card --}}
    <div
        class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-6">
        <div class="p-6">
            <div class="flex items-start justify-between">
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $purchaseRequisition->title }}</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ Str::limit($purchaseRequisition->description, 200) }}</p>

                    <div class="flex items-center gap-4 text-sm">
                        <span class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-400">
                            <i data-feather="shopping-cart" class="w-4 h-4"></i>
                            {{ $purchaseRequisition->items->count() }} items
                        </span>
                        <span class="inline-flex items-center gap-1 text-gray-600 dark:text-gray-400">
                            <i data-feather="file-text" class="w-4 h-4"></i>
                            {{ $offers->count() }} offers received
                        </span>
                        <span class="px-3 py-1 text-xs font-bold rounded-full 
                                @if($purchaseRequisition->tender_status === 'awarded') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                                @elseif($purchaseRequisition->tender_status === 'closed') bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400
                                @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                            Tender: {{ ucfirst($purchaseRequisition->tender_status) }}
                        </span>
                    </div>
                </div>

                <a href="{{ route('procurement.pr.show', $purchaseRequisition) }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-sm font-semibold rounded-lg transition">
                    View PR Details
                </a>
            </div>
        </div>
    </div>

    {{-- Winner Notice --}}
    @if($purchaseRequisition->winningOffer)
        <div class="bg-green-50 dark:bg-green-900/20 border-2 border-green-200 dark:border-green-800 rounded-2xl p-6 mb-6">
            <div class="flex items-start gap-3">
                <i data-feather="award" class="w-6 h-6 text-green-600 dark:text-green-400 flex-shrink-0"></i>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-green-900 dark:text-green-200">Winner Selected</h3>
                    <p class="text-sm text-green-700 dark:text-green-300 mt-1">
                        <strong>{{ $purchaseRequisition->winningOffer->company->name }}</strong> has been awarded this tender.
                        Total: <strong>{{ $purchaseRequisition->winningOffer->formatted_total_price }}</strong>
                    </p>
                </div>
            </div>
        </div>
    @endif

    {{-- System Recommendation & Comparison --}}
    @if($offers->count() > 1 && !$purchaseRequisition->winningOffer)
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            {{-- Recommendation Card --}}
            @php
                $recommendedOffer = $offers->where('is_recommended', true)->first();
            @endphp
            
            <div class="lg:col-span-1">
                @if($recommendedOffer)
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-2xl p-6 text-white h-full relative overflow-hidden shadow-lg">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-white opacity-10 rounded-full blur-xl"></div>
                        <div class="absolute bottom-0 left-0 -ml-8 -mb-8 w-24 h-24 bg-yellow-400 opacity-20 rounded-full blur-xl"></div>
                        
                        <div class="relative z-10 flex flex-col h-full">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                                    <i data-feather="thumbs-up" class="w-5 h-5 text-yellow-300"></i>
                                </div>
                                <span class="font-bold text-sm tracking-wider uppercase text-indigo-100">Top Choice</span>
                            </div>
                            
                            <h3 class="text-2xl font-bold mb-1">{{ $recommendedOffer->company->name }}</h3>
                            <div class="text-3xl font-extrabold text-white mb-4">{{ $recommendedOffer->formatted_total_price }}</div>
                            
                            <div class="space-y-3 mb-6 flex-1">
                                <div class="flex items-center gap-2 text-sm text-indigo-100">
                                    <i data-feather="target" class="w-4 h-4"></i>
                                    <span>Rank Score: <strong>{{ $recommendedOffer->rank_score }}/100</strong></span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-indigo-100">
                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                    <span>Quantity Match: <strong>{{ number_format(($recommendedOffer->items->sum('quantity_offered') / max(1, $purchaseRequisition->items->sum('quantity'))) * 100) }}%</strong></span>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-indigo-100">
                                    <i data-feather="clock" class="w-4 h-4"></i>
                                    <span>Response: <strong>{{ $recommendedOffer->created_at->diffForHumans($purchaseRequisition->created_at, true) }}</strong></span>
                                </div>
                            </div>
                            
                            <div class="bg-white/10 rounded-xl p-3 backdrop-blur-sm text-xs text-indigo-100 leading-relaxed mb-4">
                                "This offer provides the best balance of price and reliability based on our scoring system."
                            </div>

                            <a href="#offer-{{ $recommendedOffer->id }}" class="w-full py-3 bg-white text-indigo-600 hover:bg-indigo-50 font-bold rounded-lg text-center transition shadow-sm">
                                Review Offer
                            </a>
                        </div>
                    </div>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 h-full border border-gray-100 dark:border-gray-700 flex flex-col items-center justify-center text-center">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <i data-feather="bar-chart-2" class="w-8 h-8 text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Analysis In Progress</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                            Comparing offers based on price, reliability, and history. No clear winner yet.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Comparison Matrix --}}
            <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col">
                <div class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <i data-feather="columns" class="w-4 h-4"></i>
                        Side-by-Side Comparison (Top 3)
                    </h3>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead>
                            <tr class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
                                <th class="px-4 py-3 text-gray-500 font-medium w-1/4">Criteria</th>
                                @foreach($offers->take(3) as $offer)
                                    <th class="px-6 py-4 relative {{ $loop->first ? 'bg-yellow-50/50 dark:bg-yellow-900/10' : '' }}">
                                        @if($loop->first)
                                            <div class="absolute top-0 left-0 w-full h-1 bg-yellow-400"></div>
                                        @endif
                                        <div class="font-bold text-gray-900 dark:text-white text-base">{{ $offer->company->name }}</div>
                                        <div class="text-xs text-gray-500 font-normal mt-1">Rank #{{ $loop->iteration }}</div>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            {{-- Price Row --}}
                            <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-medium">Total Price</td>
                                @php $minPrice = $offers->min('total_price'); @endphp
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-6 py-3 {{ $loop->first ? 'bg-yellow-50/20 dark:bg-yellow-900/5' : '' }}">
                                        @if($offer->total_price == $minPrice)
                                            <span class="text-green-600 dark:text-green-400 font-bold flex items-center gap-1">
                                                {{ $offer->formatted_total_price }}
                                                <i data-feather="check" class="w-3 h-3"></i>
                                            </span>
                                        @else
                                            <span class="text-gray-900 dark:text-gray-300">{{ $offer->formatted_total_price }}</span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                            
                            {{-- Score Row --}}
                            <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-medium">System Score</td>
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-6 py-3 {{ $loop->first ? 'bg-yellow-50/20 dark:bg-yellow-900/5' : '' }}">
                                        <div class="flex items-center gap-2">
                                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 w-20">
                                                <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $offer->rank_score }}%"></div>
                                            </div>
                                            <span class="font-bold text-xs">{{ $offer->rank_score }}</span>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Quantity Match Row --}}
                            <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-medium">Quantity Match</td>
                                @php 
                                    $totalRequested = max(1, $purchaseRequisition->items->sum('quantity'));
                                @endphp
                                @foreach($offers->take(3) as $offer)
                                    @php
                                        $offeredQty = $offer->items->sum('quantity_offered');
                                        $pct = min(100, round(($offeredQty / $totalRequested) * 100));
                                    @endphp
                                    <td class="px-6 py-3 {{ $loop->first ? 'bg-yellow-50/20 dark:bg-yellow-900/5' : '' }}">
                                        @if($pct >= 100)
                                            <span class="text-green-600 dark:text-green-400 font-bold text-xs flex items-center gap-1">
                                                100% (Full)
                                                <i data-feather="check-circle" class="w-3 h-3"></i>
                                            </span>
                                        @elseif($pct >= 80)
                                            <span class="text-yellow-600 dark:text-yellow-400 font-semibold text-xs lowercase">
                                                {{ $pct }}% (Partial)
                                            </span>
                                        @else
                                            <span class="text-red-500 font-semibold text-xs lowercase">
                                                {{ $pct }}% (Partial)
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Reliability Row --}}
                            <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-medium">Vendor Reliability</td>
                                @foreach($offers->take(3) as $offer)
                                    @php
                                        // Count past wins (excluding current if won) for this company
                                        $wins = \App\Modules\Procurement\Domain\Models\PurchaseRequisitionOffer::where('company_id', $offer->company_id)
                                            ->where('status', 'accepted')
                                            ->count();
                                    @endphp
                                    <td class="px-6 py-3 {{ $loop->first ? 'bg-yellow-50/20 dark:bg-yellow-900/5' : '' }}">
                                        @if($wins > 5)
                                            <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 text-xs font-bold">
                                                <i data-feather="shield" class="w-3 h-3 fill-current"></i>
                                                High Trust ({{ $wins }} Wins)
                                            </div>
                                        @elseif($wins > 0)
                                            <span class="text-gray-700 dark:text-gray-300 text-xs">
                                                {{ $wins }} Past Wins
                                            </span>
                                        @else
                                            <span class="text-gray-500 dark:text-gray-400 text-xs italic">
                                                New Vendor
                                            </span>
                                        @endif
                                    </td>
                                @endforeach
                            </tr>

                             {{-- Delivery/Time Row --}}
                             <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-medium">Delivery Time</td>
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-6 py-3 {{ $loop->first ? 'bg-yellow-50/20 dark:bg-yellow-900/5' : '' }}">
                                        <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                            <i data-feather="truck" class="w-3 h-3 text-primary-500"></i>
                                            {{ $offer->delivery_time ?? 'N/A' }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Warranty Row --}}
                            <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-medium">Warranty</td>
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-6 py-3 {{ $loop->first ? 'bg-yellow-50/20 dark:bg-yellow-900/5' : '' }}">
                                        <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                            <i data-feather="shield" class="w-3 h-3 text-primary-500"></i>
                                            {{ $offer->warranty ?? 'N/A' }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Payment Scheme Row --}}
                            <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-medium">Payment Scheme</td>
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-6 py-3 {{ $loop->first ? 'bg-yellow-50/20 dark:bg-yellow-900/5' : '' }}">
                                        <div class="flex items-center gap-2 text-sm text-gray-900 dark:text-white">
                                            <i data-feather="credit-card" class="w-3 h-3 text-primary-500"></i>
                                            {{ $offer->payment_scheme ?? 'N/A' }}
                                        </div>
                                    </td>
                                @endforeach
                            </tr>

                             {{-- Response Time Row --}}
                             <tr>
                                <td class="px-4 py-3 text-gray-600 dark:text-gray-400 font-medium">Response Time</td>
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-6 py-3 {{ $loop->first ? 'bg-yellow-50/20 dark:bg-yellow-900/5' : '' }}">
                                        <span class="text-gray-700 dark:text-gray-300">{{ $offer->created_at->diffForHumans($purchaseRequisition->created_at, true) }}</span>
                                    </td>
                                @endforeach
                            </tr>

                            {{-- Action Row --}}
                            <tr class="bg-gray-50/50 dark:bg-gray-900/30">
                                <td class="px-4 py-3"></td>
                                @foreach($offers->take(3) as $offer)
                                    <td class="px-6 py-4 {{ $loop->first ? 'bg-yellow-50/30 dark:bg-yellow-900/10' : '' }}">
                                        @if($offer->status === 'pending')
                                            <form action="{{ route('procurement.offers.accept', $offer) }}" method="POST">
                                                @csrf
                                                <button type="submit" 
                                                    onclick="return confirm('Accept this offer from {{ $offer->company->name }}?')"
                                                    class="w-full py-2 px-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 hover:border-green-500 hover:text-green-600 dark:hover:text-green-400 rounded-lg text-sm font-semibold transition shadow-sm text-gray-600 dark:text-gray-300">
                                                    Select Winner
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-bold uppercase text-gray-400">{{ $offer->status }}</span>
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
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-gray-700">
            <i data-feather="inbox" class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4"></i>
            <p class="text-lg font-bold text-gray-900 dark:text-white">No Offers Yet</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Wait for companies to submit their offers</p>
        </div>
    @else
        <div class="space-y-4">
            @foreach($offers as $offer)
                <div id="offer-{{ $offer->id }}" class="scroll-mt-24 bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border-2 
                                @if($offer->is_recommended) border-primary-300 dark:border-primary-700
                                @elseif($offer->status === 'accepted') border-green-300 dark:border-green-700
                                @elseif($offer->status === 'rejected') border-gray-200 dark:border-gray-700 opacity-60
                                @else border-gray-100 dark:border-gray-700 @endif">

                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-start gap-4">
                                {{-- Rank Badge --}}
                                <div class="flex-shrink-0">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center text-lg font-bold
                                                    @if($offer->rank_position == 1) bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                                    @elseif($offer->rank_position == 2) bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400
                                                    @elseif($offer->rank_position == 3) bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400
                                                    @else bg-gray-50 text-gray-600 dark:bg-gray-700 dark:text-gray-400 @endif">
                                        #{{ $offer->rank_position }}
                                    </div>
                                </div>

                                {{-- Company Info --}}
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $offer->company->name }}</h3>

                                        @if($offer->is_recommended)
                                            <span
                                                class="px-2 py-1 bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 text-xs font-bold rounded-full inline-flex items-center gap-1">
                                                <i data-feather="star" class="w-3 h-3 fill-current"></i>
                                                Recommended
                                            </span>
                                        @endif

                                        @if($offer->status === 'accepted')
                                            <span
                                                class="px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-xs font-bold rounded-full inline-flex items-center gap-1">
                                                <i data-feather="check-circle" class="w-3 h-3"></i>
                                                Accepted
                                            </span>
                                        @elseif($offer->status === 'winning')
                                            <span
                                                class="px-2 py-1 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 text-xs font-bold rounded-full inline-flex items-center gap-1">
                                                <i data-feather="clock" class="w-3 h-3"></i>
                                                Winning (Pending Approval)
                                            </span>
                                        @elseif($offer->status === 'negotiating')
                                            <span
                                                class="px-2 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 text-xs font-bold rounded-full inline-flex items-center gap-1">
                                                <i data-feather="message-circle" class="w-3 h-3"></i>
                                                Negotiating
                                            </span>
                                        @elseif($offer->status === 'rejected')
                                            <span
                                                class="px-2 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-xs font-bold rounded-full">
                                                Rejected
                                            </span>
                                        @endif
                                    </div>

                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Submitted by <strong>{{ $offer->user->name }}</strong> •
                                        {{ $offer->created_at->diffForHumans() }}
                                    </p>

                                    @if($offer->notes)
                                        <p
                                            class="text-sm text-gray-700 dark:text-gray-300 mt-2 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                                            <i data-feather="message-square" class="w-3 h-3 inline mr-1"></i>
                                            {{ Str::limit($offer->notes, 150) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            {{-- Price --}}
                            <div class="text-right">
                                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                    {{ $offer->formatted_total_price }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Score: {{ $offer->rank_score }}/100</p>
                            </div>
                        </div>

                        {{-- Items Summary --}}
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-4">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Offer Details</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($offer->items->take(3) as $item)
                                    <div
                                        class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 px-3 py-2 rounded">
                                        <strong>{{ $item->quantity_offered }}x</strong>
                                        {{ $item->purchaseRequisitionItem->catalogueItem->name }}
                                        <span class="text-xs text-gray-500">@ {{ number_format($item->unit_price, 2) }}</span>
                                    </div>
                                @endforeach
                                @if($offer->items->count() > 3)
                                    <div class="text-sm text-gray-500 dark:text-gray-400 italic px-3 py-2">
                                        +{{ $offer->items->count() - 3 }} more items
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Documents Indicator --}}
                        @if($offer->documents->count() > 0)
                            <div class="border-t border-gray-100 dark:border-gray-700 pt-3 mt-3">
                                <p class="text-xs text-gray-600 dark:text-gray-400 inline-flex items-center gap-1">
                                    <i data-feather="paperclip" class="w-3 h-3"></i>
                                    {{ $offer->documents->count() }} supporting {{ Str::plural('document', $offer->documents->count()) }} attached
                                </p>
                            </div>
                        @endif

                        {{-- Actions --}}
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-4 flex items-center justify-between">
                            <div class="flex flex-col items-end gap-2">
                                <a href="{{ route('procurement.offers.show', $offer) }}"
                                    class="text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 inline-flex items-center gap-1">
                                    View Full Details
                                    <i data-feather="arrow-right" class="w-3 h-3"></i>
                                </a>

                                @if($offer->status === 'winning')
                                    <span class="text-xs text-indigo-600 dark:text-indigo-400 font-medium">
                                        Waiting for approval from <strong>{{ $purchaseRequisition->headApprover->name ?? 'Head Approver' }}</strong>
                                    </span>
                                @elseif($offer->status === 'negotiating')
                                    <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">
                                        Waiting for <strong>{{ $offer->company->name }}</strong> to update their bid
                                    </span>
                                @endif
                            </div>

                            @php
                                $isCompanyManager = Auth::user()->companies()->where('companies.id', $purchaseRequisition->company_id)->wherePivotIn('role', ['owner', 'admin'])->exists();
                                $isApprover = (Auth::id() === $purchaseRequisition->head_approver_id || Auth::user()->is_admin || $isCompanyManager);
                            @endphp

                            @if($offer->status === 'pending' || $offer->status === 'negotiating')
                                <div class="flex gap-2">
                                    @if($offer->status === 'pending')
                                        <form action="{{ route('procurement.offers.submit-negotiation', $offer) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Invite this vendor to stage 2 negotiation?')"
                                                class="px-4 py-2 bg-indigo-100 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:hover:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 text-sm font-semibold rounded-lg transition">
                                                <i data-feather="message-circle" class="w-4 h-4 inline mr-1"></i>
                                                Negotiate
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('procurement.offers.reject', $offer) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Reject this offer?')"
                                            class="px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm font-semibold rounded-lg transition">
                                            <i data-feather="x" class="w-4 h-4 inline mr-1"></i>
                                            Reject
                                        </button>
                                    </form>

                                    <form action="{{ route('procurement.offers.accept', $offer) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Select this offer as the potential winner? This will require final approval from the Purchasing Manager/Head.')"
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-sm">
                                            <i data-feather="check-circle" class="w-4 h-4 inline mr-1"></i>
                                            Select Winner
                                        </button>
                                    </form>
                                </div>
                            @elseif($offer->status === 'winning' && $isApprover)
                                <div class="flex gap-2">
                                    <form action="{{ route('procurement.offers.approve-winner', $offer) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Give final approval to award this tender to {{ $offer->company->name }}?')"
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-sm">
                                            <i data-feather="check-square" class="w-4 h-4 inline mr-1"></i>
                                            Approve Winner
                                        </button>
                                    </form>
                                    
                                    <form action="{{ route('procurement.offers.reject', $offer) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" onclick="return confirm('Reject this winning selection?')"
                                            class="px-4 py-2 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm font-semibold rounded-lg transition">
                                            <i data-feather="x" class="w-4 h-4 inline mr-1"></i>
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush