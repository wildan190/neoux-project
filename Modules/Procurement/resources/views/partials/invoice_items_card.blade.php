<div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
    <div class="p-8">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Invoice Items</h3>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-50 dark:border-gray-700">
                        <th class="py-4 text-left text-[10px] font-black text-gray-400 uppercase tracking-widest">Description</th>
                        <th class="py-4 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest px-4">Qty</th>
                        <th class="py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Rate</th>
                        <th class="py-4 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                    @foreach($invoice->items as $item)
                        <tr class="group">
                            <td class="py-6">
                                <div class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                    {{ $item->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? $item->purchaseOrderItem->item_name ?? 'N/A' }}
                                </div>
                                <div class="text-[10px] text-gray-400 font-bold mt-1 uppercase tracking-widest">
                                    SKU: {{ $item->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->sku ?? 'N/A' }}
                                </div>
                            </td>
                            <td class="py-6 text-center font-bold text-gray-700 dark:text-gray-300">{{ $item->quantity_invoiced }}</td>
                            <td class="py-6 text-right font-bold text-gray-400">{{ $item->formatted_unit_price }}</td>
                            <td class="py-6 text-right font-black text-gray-900 dark:text-white tabular-nums">{{ $item->formatted_subtotal }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="flex justify-end pt-8 border-t border-gray-100 dark:border-gray-700 mt-8">
            <div class="w-full md:w-80 space-y-4">
                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-900/50 p-4 rounded-2xl">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Grand Total</span>
                    <span class="text-2xl font-black text-primary-600 tabular-nums">
                        {{ $invoice->formatted_total_amount }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
