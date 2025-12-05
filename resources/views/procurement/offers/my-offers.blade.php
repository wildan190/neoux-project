@extends('layouts.app', [
    'title' => 'My Offers',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'My Offers', 'url' => null],
    ]
])

@section('content')
    @if($offers->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center border border-gray-100 dark:border-gray-700">
            <i data-feather="inbox" class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4"></i>
            <p class="text-lg font-bold text-gray-900 dark:text-white">No Offers Yet</p>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">You haven't submitted any offers</p>
            <a href="{{ route('procurement.pr.public-feed') }}" 
               class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition">
                Browse Requests
                <i data-feather="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6">
            @foreach($offers as $offer)
                <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border 
                    @if($offer->status === 'accepted') border-green-300 dark:border-green-700
                    @elseif($offer->status === 'rejected') border-red-200 dark:border-red-800
                    @else border-gray-100 dark:border-gray-700 @endif">
                    
                    <div class="p-6">
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                {{-- PR Info --}}
                                <div class="mb-3">
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ $offer->purchaseRequisition->title }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Requested by <strong>{{ $offer->purchaseRequisition->company->name }}</strong>
                                    </p>
                                </div>

                                {{-- Offer Status --}}
                                <div class="flex items-center gap-3 flex-wrap">
                                    @if($offer->status === 'accepted')
                                        <span class="px-3 py-1.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-sm font-bold rounded-full inline-flex items-center gap-1">
                                            <i data-feather="award" class="w-4 h-4"></i>
                                            Winner - Offer Accepted
                                        </span>
                                    @elseif($offer->status === 'rejected')
                                        <span class="px-3 py-1.5 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-sm font-bold rounded-full">
                                            Offer Rejected
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-sm font-bold rounded-full">
                                            Pending Review
                                        </span>
                                    @endif

                                    @if($offer->is_recommended && $offer->status === 'pending')
                                        <span class="px-3 py-1.5 bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 text-sm font-bold rounded-full inline-flex items-center gap-1">
                                            <i data-feather="star" class="w-4 h-4 fill-current"></i>
                                            Recommended by System
                                        </span>
                                    @endif

                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 text-sm font-bold rounded-full">
                                        Rank #{{ $offer->rank_position }}
                                    </span>

                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Score: {{ $offer->rank_score }}/100
                                    </span>
                                </div>

                                @if($offer->notes)
                                    <div class="mt-3 bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                                        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Your Notes</p>
                                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $offer->notes }}</p>
                                    </div>
                                @endif
                            </div>

                            {{-- Price & Date --}}
                            <div class="text-right ml-6">
                                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $offer->formatted_total_price }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $offer->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        {{-- Items Preview --}}
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-4">
                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Items Offered ({{ $offer->items->count() }})</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                @foreach($offer->items->take(4) as $item)
                                    <div class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 px-3 py-2 rounded flex justify-between">
                                        <span>
                                            <strong>{{ $item->quantity_offered }}x</strong> {{ $item->purchaseRequisitionItem->catalogueItem->name }}
                                        </span>
                                        <span class="text-gray-500 dark:text-gray-400">
                                            Rp {{ number_format($item->unit_price, 2) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            @if($offer->items->count() > 4)
                                <p class="text-xs text-gray-500 dark:text-gray-400 italic mt-2">+{{ $offer->items->count() - 4 }} more items</p>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mt-4 flex items-center justify-between">
                            <a href="{{ route('procurement.pr.show-public', $offer->purchaseRequisition) }}" 
                               class="text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 inline-flex items-center gap-1">
                                <i data-feather="file-text" class="w-3 h-3"></i>
                                View Original Request
                            </a>

                            <a href="{{ route('procurement.offers.show', $offer) }}" 
                               class="text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 inline-flex items-center gap-1">
                                View Full Offer Details
                                <i data-feather="arrow-right" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($offers->hasPages())
            <div class="mt-6">
                {{ $offers->links() }}
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush
