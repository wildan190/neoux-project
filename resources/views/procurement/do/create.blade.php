@extends('layouts.app', [
    'title' => 'Create Delivery Order',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Purchase Orders', 'url' => route('procurement.po.index')],
        ['name' => $purchaseOrder->po_number, 'url' => route('procurement.po.show', $purchaseOrder)],
        ['name' => 'Create DO', 'url' => null],
    ]
])

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create Delivery Order</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400">Specify the quantities you are shipping for {{ $purchaseOrder->po_number }}</p>
        </div>

        <form action="{{ route('procurement.do.store', $purchaseOrder) }}" method="POST">
            @csrf

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Items to Ship</h3>
                </div>
                
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Item</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Ordered</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Already Shipped</th>
                            <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase w-32">Qty to Ship</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($purchaseOrder->items as $index => $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <input type="hidden" name="items[{{ $index }}][purchase_order_item_id]" value="{{ $item->id }}">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $item->purchaseRequisitionItem->catalogueItem->name }}
                                    </div>
                                    <div class="text-xs text-gray-500">SKU: {{ $item->purchaseRequisitionItem->catalogueItem->sku }}</div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                    {{ $item->quantity_ordered }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm text-gray-700 dark:text-gray-300">
                                    {{ $item->quantity_shipped }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @php
                                        $remaining = $item->quantity_ordered - $item->quantity_shipped;
                                    @endphp
                                    <input type="number" 
                                           name="items[{{ $index }}][quantity_shipped]" 
                                           value="{{ max(0, $remaining) }}"
                                           min="0" 
                                           max="{{ max(0, $remaining) }}"
                                           {{ $remaining <= 0 ? 'disabled' : '' }}
                                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white text-right {{ $remaining <= 0 ? 'bg-gray-100 dark:bg-gray-800 cursor-not-allowed' : '' }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6 mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Delivery Notes</h3>
                <textarea name="notes" rows="3" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:text-white" placeholder="Add any instructions or notes about this shipment..."></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('procurement.po.show', $purchaseOrder) }}" class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-bold rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Cancel
                </a>
                <button type="submit" class="px-8 py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition shadow-lg shadow-primary-500/20">
                    Generate Delivery Order
                </button>
            </div>
        </form>
    </div>
@endsection
