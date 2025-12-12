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
                                        <th class="px-4 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase w-24">Receive Now</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase w-32">Status</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Catatan</th>
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
                                                        $grItem->goodsReturnRequest->resolution_status === 'resolved') {
                                                        $replacementQty += $grItem->goodsReturnRequest->quantity_affected;
                                                        $needsReplacement = true;
                                                    }
                                                }
                                            }
                                            
                                            // Use replacement quantity if in replacement mode
                                            $maxQty = ($isReplacement ?? false) ? $replacementQty : $remaining;
                                            $defaultQty = ($isReplacement ?? false) ? $replacementQty : $remaining;
                                        @endphp
                                        <tr class="{{ $needsReplacement ? 'bg-green-50 dark:bg-green-900/10' : '' }}">
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $item->purchaseRequisitionItem->catalogueItem->name }}
                                                </div>
                                                @if($needsReplacement)
                                                    <span class="inline-flex items-center text-xs text-green-600 dark:text-green-400 mt-1">
                                                        <i data-feather="refresh-cw" class="w-3 h-3 mr-1"></i>
                                                        Perlu replacement: {{ $replacementQty }} unit
                                                    </span>
                                                @endif
                                                <input type="hidden" name="items[{{ $index }}][po_item_id]" value="{{ $item->id }}">
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                                {{ $item->quantity_ordered }}
                                            </td>
                                            <td class="px-4 py-3 text-right text-sm text-gray-700 dark:text-gray-300">
                                                {{ $item->quantity_received }}
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="number" name="items[{{ $index }}][quantity_received]" 
                                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-right focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                                       min="0" max="{{ $maxQty }}" value="{{ $defaultQty }}">
                                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 text-right">
                                                    Max: <span class="font-semibold {{ $needsReplacement ? 'text-green-600 dark:text-green-400' : 'text-primary-600 dark:text-primary-400' }}">{{ $maxQty }}</span>
                                                    @if($needsReplacement)
                                                        <span class="text-green-600">(replacement)</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <select name="items[{{ $index }}][item_status]" 
                                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                                        onchange="toggleConditionField(this, {{ $index }})">
                                                    <option value="good">✅ Baik</option>
                                                    <option value="damaged">⚠️ Rusak</option>
                                                    <option value="rejected">❌ Ditolak</option>
                                                </select>
                                            </td>
                                            <td class="px-4 py-3">
                                                <input type="text" name="items[{{ $index }}][condition]" 
                                                       id="condition_{{ $index }}"
                                                       class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500 sm:text-sm"
                                                       placeholder="Catatan kondisi...">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                            <i data-feather="info" class="w-3 h-3 inline"></i>
                            Jika ada item rusak/ditolak, sistem akan otomatis membuat Goods Return Request (GRR).
                        </p>
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
