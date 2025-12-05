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
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($purchaseOrder->items as $item)
                                <tr>
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
                                <td colspan="4" class="px-6 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Total Amount</td>
                                <td class="px-6 py-4 text-right text-lg font-bold text-primary-600 dark:text-primary-400">
                                    {{ $purchaseOrder->formatted_total_amount }}
                                </td>
                            </tr>
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
                    @endphp
                    @if($isBuyer && $purchaseOrder->status !== 'completed' && $purchaseOrder->status !== 'cancelled' && !$isFullyReceived)
                        <a href="{{ route('procurement.gr.create', $purchaseOrder) }}" class="text-sm font-bold text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                            + Receive Goods
                        </a>
                    @elseif($isFullyReceived)
                        <span class="text-sm font-semibold text-green-600 dark:text-green-400">
                            <i data-feather="check-circle" class="w-4 h-4 inline"></i>
                            Fully Received
                        </span>
                    @endif
                </div>
                <div class="p-6">
                    @if($purchaseOrder->goodsReceipts->isEmpty())
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic text-center py-4">No goods received yet.</p>
                    @else
                        <div class="space-y-4">
                            @foreach($purchaseOrder->goodsReceipts as $gr)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $gr->gr_number }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $gr->received_at->format('d M Y, H:i') }} by {{ $gr->receivedBy->name }}</p>
                                        </div>
                                        <div class="flex items-center gap-2">
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
                                </div>
                            @endforeach
                        </div>
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
        feather.replace();
    </script>
@endpush
