@extends('layouts.app', [
    'title' => 'Invoice: ' . $invoice->invoice_number,
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Purchase Orders', 'url' => route('procurement.po.index')],
        ['name' => $invoice->purchaseOrder->po_number, 'url' => route('procurement.po.show', $invoice->purchaseOrder)],
        ['name' => $invoice->invoice_number, 'url' => null],
    ]
])

@section('content')
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Submitted on {{ $invoice->created_at->format('d F Y') }} | 
                Due: {{ $invoice->due_date->format('d F Y') }}
            </p>
        </div>
        <div>
            <span class="px-3 py-1 text-sm font-bold rounded-full 
                @if($invoice->status === 'approved' || $invoice->status === 'paid') bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400
                @elseif($invoice->status === 'mismatch' || $invoice->status === 'rejected') bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400
                @elseif($invoice->status === 'matched') bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400
                @else bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                {{ ucfirst($invoice->status) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Invoice Items --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Invoice Items</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Item</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Price</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($invoice->items as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $item->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                        {{ $item->quantity_invoiced }}
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
                                <td colspan="3" class="px-6 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Total Amount</td>
                                <td class="px-6 py-4 text-right text-lg font-bold text-primary-600 dark:text-primary-400">
                                    {{ $invoice->formatted_total_amount }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Matching Results (Only for Buyer) --}}
            @if($isBuyer && $invoice->match_status)
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Three-Way Matching Results</h3>
                        @if($invoice->status === 'matched')
                            <span class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/30 px-2 py-1 rounded">Passed</span>
                        @elseif($invoice->status === 'mismatch')
                            <span class="text-xs font-bold text-red-600 dark:text-red-400 bg-red-100 dark:bg-red-900/30 px-2 py-1 rounded">Mismatch Found</span>
                        @endif
                    </div>
                    <div class="p-6">
                        @if(isset($invoice->match_status['variances']) && count($invoice->match_status['variances']) > 0)
                            <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-100 dark:border-red-800">
                                <h4 class="text-sm font-bold text-red-800 dark:text-red-300 mb-2">Variances Detected:</h4>
                                <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-400">
                                    @foreach($invoice->match_status['variances'] as $variance)
                                        <li>{{ $variance }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="text-xs text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                        <th class="py-2 text-left">Item</th>
                                        <th class="py-2 text-right">PO Price</th>
                                        <th class="py-2 text-right">Inv Price</th>
                                        <th class="py-2 text-center">Price Match</th>
                                        <th class="py-2 text-right">Qty Recv</th>
                                        <th class="py-2 text-right">Qty Inv</th>
                                        <th class="py-2 text-center">Qty Match</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach($invoice->match_status['details'] as $detail)
                                        @php
                                            $item = $invoice->items->first(function($i) use ($detail) {
                                                return $i->purchase_order_item_id == $detail['item_id'];
                                            });
                                            $itemName = $item ? $item->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name : 'Unknown Item';
                                        @endphp
                                        <tr>
                                            <td class="py-3 text-gray-900 dark:text-white">{{ $itemName }}</td>
                                            <td class="py-3 text-right text-gray-600 dark:text-gray-400">{{ number_format($detail['po_price'], 2) }}</td>
                                            <td class="py-3 text-right text-gray-600 dark:text-gray-400">{{ number_format($detail['invoice_price'], 2) }}</td>
                                            <td class="py-3 text-center">
                                                @if($detail['price_match'])
                                                    <i data-feather="check-circle" class="w-4 h-4 text-green-500 inline"></i>
                                                @else
                                                    <i data-feather="x-circle" class="w-4 h-4 text-red-500 inline"></i>
                                                @endif
                                            </td>
                                            <td class="py-3 text-right text-gray-600 dark:text-gray-400">{{ $detail['total_received'] }}</td>
                                            <td class="py-3 text-right text-gray-600 dark:text-gray-400">{{ $detail['invoice_qty'] }}</td>
                                            <td class="py-3 text-center">
                                                @if($detail['qty_match'])
                                                    <i data-feather="check-circle" class="w-4 h-4 text-green-500 inline"></i>
                                                @else
                                                    <i data-feather="x-circle" class="w-4 h-4 text-red-500 inline"></i>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-sm font-bold text-gray-500 dark:text-gray-400 uppercase mb-4">Related PO</h3>
                <a href="{{ route('procurement.po.show', $invoice->purchaseOrder) }}" class="block p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <p class="font-bold text-primary-600 dark:text-primary-400">{{ $invoice->purchaseOrder->po_number }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">View Purchase Order</p>
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush
