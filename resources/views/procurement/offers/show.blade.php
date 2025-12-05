@extends('layouts.app', [
    'title' => 'Offer Details',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => $purchaseRequisition->title, 'url' => route('procurement.offers.index', $purchaseRequisition)],
        ['name' => 'Offer Details', 'url' => null],
    ]
])

@section('content')
    {{-- Offer Header --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border-2 
            @if($offer->is_recommended) border-primary-300 dark:border-primary-700
            @elseif($offer->status === 'accepted') border-green-300 dark:border-green-700
            @else border-gray-100 dark:border-gray-700 @endif mb-6">

        <div class="p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-3">
                        <div
                            class="w-14 h-14 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-xl font-bold text-primary-700 dark:text-primary-400">
                            #{{ $offer->rank_position }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $offer->company->name }}</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Submitted by {{ $offer->user->name }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 flex-wrap">
                        @if($offer->is_recommended)
                            <span
                                class="px-3 py-1.5 bg-primary-100 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400 text-sm font-bold rounded-full inline-flex items-center gap-1">
                                <i data-feather="star" class="w-4 h-4 fill-current"></i>
                                Recommended
                            </span>
                        @endif

                        @if($offer->status === 'accepted')
                            <span
                                class="px-3 py-1.5 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 text-sm font-bold rounded-full inline-flex items-center gap-1">
                                <i data-feather="award" class="w-4 h-4"></i>
                                Winner
                            </span>
                        @elseif($offer->status === 'rejected')
                            <span
                                class="px-3 py-1.5 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-sm font-bold rounded-full">
                                Rejected
                            </span>
                        @else
                            <span
                                class="px-3 py-1.5 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-sm font-bold rounded-full">
                                Pending
                            </span>
                        @endif

                        <span class="text-sm text-gray-600 dark:text-gray-400">
                            Score: <strong>{{ $offer->rank_score }}/100</strong>
                        </span>

                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $offer->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <div class="text-right">
                    <p class="text-sm text-gray-500 dark:text-gray-400 uppercase">Total Offer</p>
                    <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ $offer->formatted_total_price }}
                    </p>
                </div>
            </div>

            @if($offer->notes)
                <div class="mt-4 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase mb-2">Proposal Notes</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ $offer->notes }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Items Table --}}
    <div
        class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-6">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Offered Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Product</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Requested</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Offered</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Unit Price</th>
                        <th scope="col"
                            class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Subtotal</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($offer->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $item->purchaseRequisitionItem->catalogueItem->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">SKU:
                                    {{ $item->purchaseRequisitionItem->catalogueItem->sku }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $item->purchaseRequisitionItem->quantity }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold 
                                            @if($item->quantity_offered >= $item->purchaseRequisitionItem->quantity) text-green-600 dark:text-green-400
                                            @else text-yellow-600 dark:text-yellow-400 @endif">
                                    {{ $item->quantity_offered }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                {{ $item->formatted_unit_price }}
                            </td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                {{ $item->formatted_subtotal }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <td colspan="4"
                            class="px-6 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Total
                        </td>
                        <td class="px-6 py-4 text-sm font-bold text-primary-600 dark:text-primary-400">
                            {{ $offer->formatted_total_price }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Supporting Documents --}}
    @if($offer->documents->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-6">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">
                    <i data-feather="paperclip" class="w-5 h-5 inline mr-2"></i>
                    Supporting Documents
                </h3>
            </div>
            <div class="px-6 py-4 space-y-3">
                @foreach($offer->documents as $doc)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-primary-300 dark:hover:border-primary-600 transition group">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-600">
                                <i data-feather="file-text" class="w-6 h-6 text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $doc->file_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $doc->formatted_file_size }} • {{ strtoupper(pathinfo($doc->file_name, PATHINFO_EXTENSION)) }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ asset('storage/' . $doc->file_path) }}" 
                           target="_blank"
                           download
                           class="flex-shrink-0 px-4 py-2 bg-primary-100 hover:bg-primary-200 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 text-primary-700 dark:text-primary-400 text-sm font-semibold rounded-lg transition inline-flex items-center gap-2">
                            <i data-feather="download" class="w-4 h-4"></i>
                            Download
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Actions --}}
    @if($purchaseRequisition->user_id === Auth::id() && $offer->status === 'pending')
        <div
            class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Review This Offer</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Accept this offer to award the tender, or reject it.</p>
                </div>

                <div class="flex gap-3">
                    <form action="{{ route('procurement.offers.reject', $offer) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('Reject this offer?')"
                            class="px-6 py-3 bg-red-100 hover:bg-red-200 dark:bg-red-900/30 dark:hover:bg-red-900/50 text-red-700 dark:text-red-400 text-sm font-bold rounded-lg transition">
                            <i data-feather="x" class="w-4 h-4 inline mr-1"></i>
                            Reject Offer
                        </button>
                    </form>

                    <form action="{{ route('procurement.offers.accept', $offer) }}" method="POST">
                        @csrf
                        <button type="submit"
                            onclick="return confirm('Accept this offer as the winner? This will auto-reject all other pending offers.')"
                            class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition shadow-sm">
                            <i data-feather="check-circle" class="w-4 h-4 inline mr-1"></i>
                            Accept as Winner
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Generate PO Action --}}
    @if($purchaseRequisition->user_id === Auth::id() && $offer->status === 'accepted')
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Next Steps</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        @if($purchaseRequisition->po_generated_at)
                            Purchase Order has been generated for this offer.
                        @else
                            Generate a Purchase Order to officially order these items from the vendor.
                        @endif
                    </p>
                </div>

                @if($purchaseRequisition->po_generated_at)
                    <a href="{{ route('procurement.po.show', $purchaseRequisition->purchaseOrder) }}" 
                       class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-lg transition shadow-sm inline-flex items-center gap-2">
                        <i data-feather="file-text" class="w-4 h-4"></i>
                        View Purchase Order
                    </a>
                @else
                    <form action="{{ route('procurement.po.generate', $purchaseRequisition) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                onclick="return confirm('Generate Purchase Order? This will create a formal PO document.')"
                                class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-lg transition shadow-sm inline-flex items-center gap-2">
                            <i data-feather="file-plus" class="w-4 h-4"></i>
                            Generate Purchase Order
                        </button>
                    </form>
                @endif
            </div>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ $isOwner ? route('procurement.pr.show', $purchaseRequisition) : route('procurement.offers.my') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-gray-600 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
            <i data-feather="arrow-left" class="w-4 h-4"></i>
            {{ $isOwner ? 'Back to Request' : 'Back to My Offers' }}
        </a>
    </div>
@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush