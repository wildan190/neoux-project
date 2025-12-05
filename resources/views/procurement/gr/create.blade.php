@extends('layouts.app', [
    'title' => 'Receive Goods: ' . $purchaseOrder->po_number,
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Purchase Orders', 'url' => route('procurement.po.index')],
        ['name' => $purchaseOrder->po_number, 'url' => route('procurement.po.show', $purchaseOrder)],
        ['name' => 'Receive Goods', 'url' => null],
    ]
])

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('procurement.gr.store', $purchaseOrder) }}" method="POST">
            @csrf
            
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">Receive Goods</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Record items received against PO {{ $purchaseOrder->po_number }}</p>
                </div>
                
                <div class="p-6 space-y-6">
                    {{-- General Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="received_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Received <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="received_at" id="received_at" required
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                   value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div>
                            <label for="delivery_note" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Delivery Note / Ref Number</label>
                            <input type="text" name="delivery_note" id="delivery_note"
                                   class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                   placeholder="e.g. DO-123456">
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Any additional comments about this delivery..."></textarea>
                    </div>

                    {{-- Items Table --}}
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Items to Receive</h3>
                        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Item</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Ordered</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Prev. Received</th>
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase w-32">Receive Now</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Condition</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @foreach($purchaseOrder->items as $index => $item)
                                        @php
                                            $remaining = $item->quantity_ordered - $item->quantity_received;
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $item->purchaseRequisitionItem->catalogueItem->name }}
                                                </div>
                                                <input type="hidden" name="items[{{ $index }}][po_item_id]" value="{{ $item->id }}">
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                                {{ $item->quantity_ordered }}
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                                {{ $item->quantity_received }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="relative">
                                                    <input type="number" name="items[{{ $index }}][quantity_received]" 
                                                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-right focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                                           min="0" max="{{ $remaining }}" value="{{ $remaining }}">
                                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-right">
                                                        Max: <span class="font-semibold text-primary-600 dark:text-primary-400">{{ $remaining }}</span> remaining
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" name="items[{{ $index }}][condition]" 
                                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                                       placeholder="Good">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                    <a href="{{ route('procurement.po.show', $purchaseOrder) }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition font-bold shadow-sm">
                        Confirm Receipt
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
