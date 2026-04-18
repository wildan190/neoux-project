{{-- Status Feed --}}
<div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Status History</h3>
    <div class="space-y-6">
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-2 h-2 rounded-full bg-primary-600 shadow-[0_0_8px_rgba(37,99,235,0.6)]"></div>
                <div class="w-0.5 flex-1 bg-gray-100 dark:bg-gray-700 mt-2"></div>
            </div>
            <div>
                <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tighter">Current Stage</p>
                <p class="text-xs text-gray-500 mt-1 capitalize">{{ str_replace('_', ' ', $purchaseOrder->status) }}</p>
            </div>
        </div>
        <div class="flex gap-4">
            <div class="flex flex-col items-center">
                <div class="w-2 h-2 rounded-full bg-gray-300 dark:bg-gray-600"></div>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">Issued On</p>
                <p class="text-[10px] text-gray-500 mt-1">{{ $purchaseOrder->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
    </div>
</div>
