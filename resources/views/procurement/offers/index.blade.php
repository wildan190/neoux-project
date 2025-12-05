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
                <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border-2 
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
                            <a href="{{ route('procurement.offers.show', $offer) }}"
                                class="text-sm font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 inline-flex items-center gap-1">
                                View Full Details
                                <i data-feather="arrow-right" class="w-3 h-3"></i>
                            </a>

                            @if($offer->status === 'pending')
                                <div class="flex gap-2">
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
                                            onclick="return confirm('Accept this offer as the winner? This will reject all other offers.')"
                                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-sm">
                                            <i data-feather="check-circle" class="w-4 h-4 inline mr-1"></i>
                                            Accept Offer
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