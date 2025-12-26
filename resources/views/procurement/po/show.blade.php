@extends('layouts.app', [
    'title' => 'Purchase Order: ' . $purchaseOrder->po_number,
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Purchase Orders', 'url' => route('procurement.po.index')],
        ['name' => $purchaseOrder->po_number, 'url' => null],
    ]
])

@section('content')
    {{-- Header Actions --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $purchaseOrder->po_number }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Created on {{ $purchaseOrder->created_at->format('d F Y, H:i') }}</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('procurement.po.print', $purchaseOrder) }}" target="_blank" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition flex items-center gap-2">
                <i data-feather="printer" class="w-4 h-4"></i>
                Print PO
            </a>
            @if($isVendor && $purchaseOrder->status === 'issued')
                <form action="{{ route('procurement.po.confirm', $purchaseOrder) }}" method="POST" onsubmit="return handlePrFormSubmit(this)">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition flex items-center gap-2 font-bold shadow-sm">
                        <i data-feather="check-square" class="w-4 h-4"></i>
                        Confirm Purchase Order
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Items Table --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Ordered Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Item</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Received</th>
                                <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($purchaseOrder->items as $item)
                                @php
                                    // Check if any GR items have issues
                                    $grItems = $item->goodsReceiptItems ?? collect();
                                    $damagedCount = $grItems->where('item_status', 'damaged')->sum('quantity_received');
                                    $rejectedCount = $grItems->where('item_status', 'rejected')->sum('quantity_received');
                                    $goodCount = $grItems->where('item_status', 'good')->sum('quantity_received');
                                    $hasIssue = $damagedCount > 0 || $rejectedCount > 0;
                                    $grrCount = $grItems->filter(fn($gi) => $gi->goodsReturnRequest)->count();
                                @endphp
                                <tr class="{{ $hasIssue ? 'bg-red-50 dark:bg-red-900/10' : '' }}">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->purchaseRequisitionItem->catalogueItem->name }}
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            SKU: {{ $item->purchaseRequisitionItem->catalogueItem->sku }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                        {{ $item->quantity_ordered }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm">
                                        <span class="font-bold @if($item->quantity_received >= $item->quantity_ordered) text-green-600 dark:text-green-400 @else text-yellow-600 dark:text-yellow-400 @endif">
                                            {{ $item->quantity_received }}
                                        </span>
                                        @if($hasIssue)
                                            <div class="text-xs mt-1 space-y-0.5">
                                                @if($goodCount > 0)
                                                    <span class="text-green-600">✓ {{ $goodCount }} OK</span>
                                                @endif
                                                @if($damagedCount > 0)
                                                    <span class="text-yellow-600 block">⚠ {{ $damagedCount }} rusak</span>
                                                @endif
                                                @if($rejectedCount > 0)
                                                    <span class="text-red-600 block">✗ {{ $rejectedCount }} ditolak</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($grrCount > 0)
                                            <a href="{{ route('procurement.grr.index') }}" 
                                               class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/50 transition">
                                                <i data-feather="alert-triangle" class="w-3 h-3 mr-1"></i>
                                                {{ $grrCount }} GRR
                                            </a>
                                        @elseif($hasIssue)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">
                                                Issue
                                            </span>
                                        @elseif($item->quantity_received >= $item->quantity_ordered)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                ✓ Complete
                                            </span>
                                        @elseif($item->quantity_received > 0)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                Partial
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-400">Pending</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                        {{ $item->formatted_unit_price }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">
                                        {{ $item->formatted_subtotal }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Subtotal</td>
                                <td class="px-6 py-4 text-right text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $purchaseOrder->formatted_total_amount }}
                                </td>
                            </tr>
                            @if($purchaseOrder->has_deductions)
                                <tr class="border-t border-red-200 dark:border-red-700 bg-red-50 dark:bg-red-900/20">
                                    <td colspan="5" class="px-6 py-3 text-right text-sm font-medium text-red-700 dark:text-red-400">
                                        <span class="flex items-center justify-end gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Potongan Harga (Debit Note)
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right text-lg font-bold text-red-600 dark:text-red-400">
                                        - {{ $purchaseOrder->formatted_total_deduction }}
                                    </td>
                                </tr>
                                <tr class="border-t-2 border-primary-200 dark:border-primary-700 bg-primary-50 dark:bg-primary-900/20">
                                    <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-primary-700 dark:text-primary-300 uppercase">Total Akhir</td>
                                    <td class="px-6 py-4 text-right text-xl font-bold text-primary-600 dark:text-primary-400">
                                        {{ $purchaseOrder->formatted_adjusted_total_amount }}
                                    </td>
                                </tr>
                            @else
                                <tr class="border-t border-gray-200 dark:border-gray-600">
                                    <td colspan="5" class="px-6 py-4 text-right text-sm font-bold text-primary-700 dark:text-primary-300 uppercase">Total Amount</td>
                                    <td class="px-6 py-4 text-right text-xl font-bold text-primary-600 dark:text-primary-400">
                                        {{ $purchaseOrder->formatted_total_amount }}
                                    </td>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Goods Receipts Section --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/30">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Goods Receipts</h3>
                    @php
                        $totalOrdered = $purchaseOrder->items->sum('quantity_ordered');
                        $totalReceived = $purchaseOrder->items->sum('quantity_received');
                        $isFullyReceived = $totalReceived >= $totalOrdered;
                        
                        // Check for any GRR that needs replacement
                        $hasReplacementPending = false;
                        foreach($purchaseOrder->goodsReceipts as $gr) {
                            foreach($gr->items as $grItem) {
                                if ($grItem->goodsReturnRequest && 
                                    $grItem->goodsReturnRequest->resolution_type === 'replacement' &&
                                    $grItem->goodsReturnRequest->resolution_status === 'resolved') {
                                    $hasReplacementPending = true;
                                    break 2;
                                }
                            }
                        }
                    @endphp
                    <div class="flex items-center gap-2">
                        @if($hasReplacementPending)
                            <a href="{{ route('procurement.gr.create', $purchaseOrder) }}" 
                               class="text-sm font-bold text-green-600 hover:text-green-700 dark:text-green-400 bg-green-100 dark:bg-green-900/30 px-3 py-1.5 rounded-lg flex items-center gap-1">
                                <i data-feather="refresh-cw" class="w-3 h-3"></i>
                                Receive Replacement
                            </a>
                        @endif
                        @if($isBuyer && $purchaseOrder->status !== 'completed' && $purchaseOrder->status !== 'cancelled' && $purchaseOrder->status !== 'issued' && !$isFullyReceived)
                            <a href="{{ route('procurement.gr.create', $purchaseOrder) }}" class="text-sm font-bold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                + Receive Goods
                            </a>
                        @elseif($isBuyer && $purchaseOrder->status === 'issued')
                            <span class="text-xs font-semibold text-gray-500 italic">
                                Waiting for vendor confirmation...
                            </span>
                        @elseif($isFullyReceived && !$hasReplacementPending)
                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                                <i data-feather="check-circle" class="w-4 h-4 inline"></i>
                                Fully Received
                            </span>
                        @endif
                    </div>
                </div>
                <div class="p-6">
                    @if($purchaseOrder->goodsReceipts->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic text-center py-4">No goods received yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($purchaseOrder->goodsReceipts as $gr)
                                @php
                                    $grGoodCount = $gr->items->where('item_status', 'good')->count();
                                    $grDamagedCount = $gr->items->where('item_status', 'damaged')->count();
                                    $grRejectedCount = $gr->items->where('item_status', 'rejected')->count();
                                    $grHasIssue = $grDamagedCount > 0 || $grRejectedCount > 0;
                                    $grGrrCount = $gr->items->filter(fn($i) => $i->goodsReturnRequest)->count();
                                @endphp
                                <div class="border {{ $grHasIssue ? 'border-red-300 dark:border-red-700 bg-red-50 dark:bg-red-900/10' : 'border-gray-200 dark:border-gray-700' }} rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 dark:text-white flex items-center gap-2">
                                                {{ $gr->gr_number }}
                                                @if($grHasIssue)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                        <i data-feather="alert-triangle" class="w-3 h-3 mr-1"></i>
                                                        Has Issues
                                                    </span>
                                                @endif
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $gr->received_at->format('d M Y, H:i') }} by {{ $gr->receivedBy->name }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            @if($grGrrCount > 0)
                                                <a href="{{ route('procurement.grr.index') }}" 
                                                   class="text-xs font-semibold text-red-600 hover:text-red-700 dark:text-red-400">
                                                    {{ $grGrrCount }} GRR
                                                </a>
                                            @endif
                                            <span class="text-xs font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">
                                                {{ $gr->items->sum('quantity_received') }} items
                                            </span>
                                            <a href="{{ route('procurement.gr.print', $gr->id) }}" target="_blank" class="text-xs font-semibold text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300">
                                                <i data-feather="printer" class="w-3 h-3 inline"></i> Print DO
                                            </a>
                                        </div>
                                    </div>
                                    @if($gr->delivery_note_number)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-2">Ref: {{ $gr->delivery_note_number }}</p>
                                    @endif
                                    
                                    {{-- Item status breakdown --}}
                                    @if($grHasIssue)
                                        <div class="flex gap-3 mt-3 pt-3 border-t border-red-200 dark:border-red-800/50">
                                            @if($grGoodCount > 0)
                                                <span class="text-xs text-green-600 dark:text-green-400">
                                                    ✓ {{ $grGoodCount }} OK
                                                </span>
                                            @endif
                                            @if($grDamagedCount > 0)
                                                <span class="text-xs text-yellow-600 dark:text-yellow-400">
                                                    ⚠ {{ $grDamagedCount }} rusak
                                                </span>
                                            @endif
                                            @if($grRejectedCount > 0)
                                                <span class="text-xs text-red-600 dark:text-red-400">
                                                    ✗ {{ $grRejectedCount }} ditolak
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        
                        {{-- Info banner for replacement --}}
                        @if($hasReplacementPending)
                            <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                <p class="text-sm text-blue-700 dark:text-blue-300 flex items-start gap-2">
                                    <i data-feather="info" class="w-4 h-4 flex-shrink-0 mt-0.5"></i>
                                    <span>
                                        <strong>Replacement Pending:</strong> Ada unit pengganti yang harus diterima. 
                                        Klik tombol "Receive Replacement" di atas untuk mencatat penerimaan unit baru.
                                    </span>
                                </p>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>

        {{-- Sidebar Info --}}
        <div class="space-y-6">
            {{-- Status Card --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-4">Status</h3>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-3 h-3 rounded-full 
                        @if($purchaseOrder->status === 'completed') bg-green-500
                        @elseif($purchaseOrder->status === 'cancelled') bg-red-500
                        @elseif($purchaseOrder->status === 'issued') bg-blue-500
                        @else bg-yellow-500 @endif"></div>
                    <span class="text-lg font-bold text-gray-900 dark:text-white capitalize">
                        {{ str_replace('_', ' ', $purchaseOrder->status) }}
                    </span>
                </div>
                
                <div class="space-y-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Vendor</p>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $purchaseOrder->vendorCompany->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Buyer</p>
                        <p class="font-bold text-gray-900 dark:text-white">{{ $purchaseOrder->purchaseRequisition->company->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase mb-1">Created By</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $purchaseOrder->createdBy->name }}</p>
                    </div>
                </div>
            </div>

            {{-- Invoices Card --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase">Invoices</h3>
                    @if($isVendor)
                        <a href="{{ route('procurement.invoices.create', $purchaseOrder) }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 dark:text-primary-400">+ Create</a>
                    @endif
                </div>
                
                @if($purchaseOrder->invoices->isEmpty())
                    <p class="text-sm text-gray-500 dark:text-gray-400 italic">No invoices submitted.</p>
                @else
                    <div class="space-y-3">
                        @foreach($purchaseOrder->invoices as $invoice)
                            <a href="{{ route('procurement.invoices.show', $invoice) }}" class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                <div>
                                    <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $invoice->formatted_total_amount }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-bold rounded bg-white dark:bg-gray-600 shadow-sm 
                                    @if($invoice->status === 'mismatch') text-red-600 dark:text-red-400 @elseif($invoice->status === 'matched') text-blue-600 dark:text-blue-400 @endif">
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof feather !== 'undefined') {
                feather.replace();
            }
        });

        function handlePrFormSubmit(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                
                submitBtn.innerHTML = '<span class="flex items-center gap-2">' +
                    '<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                    '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                    '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
                    '</svg>' +
                    'Processing...' +
                    '</span>';
            }
            return true;
        }
    </script>
@endpush
