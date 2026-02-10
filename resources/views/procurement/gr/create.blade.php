@extends('layouts.app', [
    'title' => ($isReplacement ?? false) ? 'Receive Replacement: ' . $purchaseOrder->po_number : 'Receive Goods: ' . $purchaseOrder->po_number,
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Purchase Orders', 'url' => route('procurement.po.index')],
        ['name' => $purchaseOrder->po_number, 'url' => route('procurement.po.show', $purchaseOrder)],
        ['name' => ($isReplacement ?? false) ? 'Receive Replacement' : 'Receive Goods', 'url' => null],
    ]
])

@section('content')
    <div class="max-w-4xl mx-auto">
        <form action="{{ route('procurement.gr.store', $purchaseOrder) }}" method="POST">
            @csrf
            @if($deliveryOrder)
                <input type="hidden" name="delivery_order_id" value="{{ $deliveryOrder->id }}">
                <div class="mb-6 p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl border border-indigo-200 dark:border-indigo-800">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-800 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-feather="truck" class="w-5 h-5 text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-indigo-800 dark:text-indigo-200">Processing Delivery Order: {{ $deliveryOrder->do_number }}</h3>
                            <p class="text-sm text-indigo-600 dark:text-indigo-400 mt-1">
                                You are receiving goods against Delivery Order <strong>{{ $deliveryOrder->do_number }}</strong>.
                                Quantities have been pre-filled based on the vendor's shipment.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            @if($isReplacement ?? false)
                <input type="hidden" name="is_replacement" value="1">
                <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-200 dark:border-green-800">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center flex-shrink-0">
                            <i data-feather="refresh-cw" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-green-800 dark:text-green-200">Mode Penerimaan Unit Pengganti</h3>
                            <p class="text-sm text-green-600 dark:text-green-400 mt-1">
                                Anda sedang menerima barang pengganti untuk item yang sebelumnya ditolak/rusak.
                                Masukkan jumlah unit pengganti yang diterima dalam kondisi baik.
                            </p>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden mb-6">
                <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                        {{ ($isReplacement ?? false) ? '🔄 Receive Replacement' : 'Receive Goods' }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Record items received against PO {{ $purchaseOrder->po_number }}</p>
                </div>
                
                <div class="p-6 space-y-6">
                    {{-- General Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                        <div>
                            <label for="received_at" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date Received <span class="text-red-500">*</span></label>
                            <input type="datetime-local" name="received_at" id="received_at" required
                                   class="w-full px-3 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                   value="{{ now()->format('Y-m-d\TH:i') }}">
                        </div>
                        <div>
                            <label for="warehouse_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Warehouse <span class="text-red-500">*</span></label>
                            <select name="warehouse_id" id="warehouse_id" required
                                    class="w-full px-3 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500">
                                <option value="">-- Select Warehouse --</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }} ({{ $wh->code }})</option>
                                @endforeach
                            </select>
                            @if($warehouses->isEmpty())
                                <p class="text-xs text-red-500 mt-1">No warehouses configured. <a href="{{ route('procurement.warehouse.index') }}" class="underline">Create one here</a>.</p>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        <div>
                            <label for="delivery_note" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Delivery Note / Ref Number</label>
                            <input type="text" name="delivery_note" id="delivery_note" readonly
                                   class="w-full px-3 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 bg-gray-50 dark:bg-gray-900"
                                   value="{{ $deliveryOrder ? $deliveryOrder->do_number : 'DN-' . now()->format('ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT) }}">
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full px-3 py-2 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                  placeholder="Any additional comments about this delivery..."></textarea>
                    </div>

                    {{-- Items Table --}}
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-3">Items to Receive</h3>
                        <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase w-64">Item</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Ordered</th>
                                        <th class="px-4 py-3 text-center text-xs font-bold text-gray-500 dark:text-gray-400 uppercase w-32">Total Received</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase w-48">QC Breakdown</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Notes / Refund Reason</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-800">
                                    @foreach($purchaseOrder->items as $index => $item)
                                        @php
                                            // Calculate remaining for normal mode
                                            $remaining = $item->quantity_ordered - $item->quantity_received;
                                            
                                            // For replacement mode, calculate based on damaged/rejected quantities
                                            $replacementQty = 0;
                                            $needsReplacement = false;
                                            if ($isReplacement ?? false) {
                                                // Find GRR for this item that needs replacement
                                                foreach($item->goodsReceiptItems ?? [] as $grItem) {
                                                    if ($grItem->goodsReturnRequest && 
                                                        $grItem->goodsReturnRequest->resolution_type === 'replacement' &&
                                                        $grItem->goodsReturnRequest->resolution_status === 'replacement_shipped') {
                                                        $replacementQty += $grItem->goodsReturnRequest->quantity_affected;
                                                        $needsReplacement = true;
                                                    }
                                                }
                                            }
                                            
                                            // Use replacement quantity if in replacement mode
                                            $maxQty = ($isReplacement ?? false) ? $replacementQty : $remaining;
                                            
                                            // Handle DO pre-fill
                                            $doQty = 0;
                                            if ($deliveryOrder) {
                                                $doItem = $deliveryOrder->items->where('purchase_order_item_id', $item->id)->first();
                                                $doQty = $doItem ? $doItem->quantity_shipped : 0;
                                            }
                                            
                                            $defaultQty = ($isReplacement ?? false) ? $replacementQty : ($deliveryOrder ? $doQty : $remaining);
                                            
                                            // Ensure maxQty matches DO if DO is specified
                                            if ($deliveryOrder) {
                                                $maxQty = $doQty;
                                            }
                                        @endphp
                                        <tr class="{{ $needsReplacement ? 'bg-green-50 dark:bg-green-900/10' : '' }}">
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $item->purchaseRequisitionItem->catalogueItem->name }}
                                                </div>
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    Remaining: {{ $remaining }}
                                                </div>
                                                @if($needsReplacement)
                                                    <span class="inline-flex items-center text-xs text-green-600 dark:text-green-400 mt-1">
                                                        <i data-feather="refresh-cw" class="w-3 h-3 mr-1"></i>
                                                        Expected Replacement: {{ $replacementQty }}
                                                    </span>
                                                @endif
                                                <input type="hidden" name="items[{{ $index }}][po_item_id]" value="{{ $item->id }}">
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm text-gray-700 dark:text-gray-300">
                                                {{ $item->quantity_ordered }}
                                            </td>
                                            <td class="px-4 py-3 relative">
                                                <input type="number" name="items[{{ $index }}][quantity_received]" 
                                                       id="total_qty_{{ $index }}"
                                                       class="w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-center font-bold text-lg focus:ring-primary-500 focus:border-primary-500"
                                                       min="0" max="{{ $maxQty }}" value="{{ $defaultQty }}"
                                                       oninput="updateQC({{ $index }})">
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-center">
                                                    Max: {{ $maxQty }}
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 bg-gray-50/50 dark:bg-gray-700/20">
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-xs font-bold text-green-600 dark:text-green-400 mb-0.5">GOOD</label>
                                                        <input type="number" name="items[{{ $index }}][quantity_good]" 
                                                            id="good_qty_{{ $index }}"
                                                            class="w-full px-3 py-2 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-center text-sm focus:ring-green-500 focus:border-green-500"
                                                            min="0" value="{{ $defaultQty }}" readonly>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs font-bold text-red-600 dark:text-red-400 mb-0.5">REJECTED</label>
                                                        <input type="number" name="items[{{ $index }}][quantity_rejected]" 
                                                            id="rejected_qty_{{ $index }}"
                                                            class="w-full px-3 py-2 rounded border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-center text-sm focus:ring-red-500 focus:border-red-500 bg-red-50 dark:bg-red-900/20"
                                                            min="0" value="0"
                                                            oninput="calculateGood({{ $index }})">
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="space-y-2">
                                                    <input type="text" name="items[{{ $index }}][rejected_reason]" 
                                                           id="rejected_reason_{{ $index }}"
                                                           class="w-full px-3 py-2 rounded-md border-red-300 dark:border-red-600 text-red-900 placeholder-red-300 focus:ring-red-500 focus:border-red-500 sm:text-sm hidden"
                                                           placeholder="Reason for rejection (Required)...">
                                                    
                                                    <input type="text" name="items[{{ $index }}][condition]" 
                                                           class="w-full px-3 py-2 rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                                           placeholder="General notes (optional)...">
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <i data-feather="info" class="w-3 h-3 inline"></i>
                            Items marked as <strong>Rejected</strong> will automatically create a <strong>Goods Return Request</strong>.
                        </p>
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-100 dark:border-gray-700 flex justify-end gap-3">
                    <a href="{{ route('procurement.po.show', $purchaseOrder) }}" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition font-medium">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition font-bold shadow-sm">
                        Confirm & Process Stock
                    </button>
                </div>
            </div>
        </form>
    </div>
    <script>
        function updateQC(index) {
            const totalInput = document.getElementById(`total_qty_${index}`);
            const goodInput = document.getElementById(`good_qty_${index}`);
            const rejectedInput = document.getElementById(`rejected_qty_${index}`);
            
            const total = parseInt(totalInput.value) || 0;
            const rejected = parseInt(rejectedInput.value) || 0;
            
            // If total changes, reset rejected to 0 and good to total (easiest logic)
            // Or keep rejected if possible? Let's reset for safety to avoid negative 'Good'
            rejectedInput.value = 0;
            goodInput.value = total;
            
            toggleReasonField(index, 0);
        }

        function calculateGood(index) {
            const totalInput = document.getElementById(`total_qty_${index}`);
            const goodInput = document.getElementById(`good_qty_${index}`);
            const rejectedInput = document.getElementById(`rejected_qty_${index}`);
            const reasonInput = document.getElementById(`rejected_reason_${index}`);
            
            let total = parseInt(totalInput.value) || 0;
            let rejected = parseInt(rejectedInput.value) || 0;
            
            if (rejected > total) {
                rejected = total;
                rejectedInput.value = total;
            }
            
            const good = total - rejected;
            goodInput.value = good;
            
            toggleReasonField(index, rejected);
        }
        
        function toggleReasonField(index, rejectedQty) {
            const reasonInput = document.getElementById(`rejected_reason_${index}`);
            if (rejectedQty > 0) {
                reasonInput.classList.remove('hidden');
                reasonInput.required = true;
            } else {
                reasonInput.classList.add('hidden');
                reasonInput.required = false;
                reasonInput.value = '';
            }
        }

        // Initialize icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    </script>
@endsection
