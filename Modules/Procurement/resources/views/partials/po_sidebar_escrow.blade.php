{{-- Escrow Payment Card --}}
<div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Escrow Payment</h3>
    
    @if($purchaseOrder->escrow_status === 'pending')
        <div class="text-center py-4">
            <div class="w-14 h-14 bg-amber-50 dark:bg-amber-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="clock" class="w-6 h-6 text-amber-500"></i>
            </div>
            <p class="text-sm font-bold text-gray-900 dark:text-white">Menunggu Pembayaran</p>
            <p class="text-xs text-gray-500 mt-1">Buyer perlu membayar ke rekening escrow.</p>
            <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-900/20 rounded-xl border border-gray-100 dark:border-gray-700">
                <p class="text-[10px] font-black text-gray-400 uppercase">Amount</p>
                <p class="text-lg font-black text-primary-600 tabular-nums mt-1">
                    {{ $purchaseOrder->has_deductions ? $purchaseOrder->formatted_adjusted_total_amount : $purchaseOrder->formatted_total_amount }}
                </p>
            </div>
        </div>
    @elseif($purchaseOrder->escrow_status === 'paid')
        <div class="text-center py-4">
            <div class="w-14 h-14 bg-blue-50 dark:bg-blue-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="shield" class="w-6 h-6 text-blue-600"></i>
            </div>
            <p class="text-sm font-bold text-blue-700 dark:text-blue-400">Dana di Escrow</p>
            <p class="text-xs text-gray-500 mt-1">Dana aman. Menunggu barang diterima.</p>
            <div class="mt-4 space-y-2">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/10 rounded-xl border border-blue-100 dark:border-blue-800/50">
                    <p class="text-[10px] font-black text-blue-500 uppercase">Amount Secured</p>
                    <p class="text-lg font-black text-blue-700 dark:text-blue-400 tabular-nums mt-1">
                        {{ $purchaseOrder->has_deductions ? $purchaseOrder->formatted_adjusted_total_amount : $purchaseOrder->formatted_total_amount }}
                    </p>
                </div>
                @if($purchaseOrder->escrow_reference)
                    <div class="text-left p-3 bg-gray-50 dark:bg-gray-900/20 rounded-xl border border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-black text-gray-400 uppercase">Reference</p>
                        <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mt-1">{{ $purchaseOrder->escrow_reference }}</p>
                        <p class="text-[10px] text-gray-400 mt-1">{{ $purchaseOrder->escrow_paid_at->format('d M Y, H:i') }}</p>
                    </div>
                @endif
            </div>
        </div>
    @elseif($purchaseOrder->escrow_status === 'released')
        <div class="text-center py-4">
            <div class="w-14 h-14 bg-green-50 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="check-circle" class="w-6 h-6 text-green-600"></i>
            </div>
            <p class="text-sm font-bold text-green-700 dark:text-green-400">Dana Dicairkan</p>
            <p class="text-xs text-gray-500 mt-1">Dana telah berhasil ditransfer ke vendor.</p>
            <div class="mt-4 p-3 bg-green-50 dark:bg-green-900/10 rounded-xl border border-green-100 dark:border-green-800/50">
                <p class="text-[10px] font-black text-green-500 uppercase">Released Amount</p>
                <p class="text-lg font-black text-green-700 dark:text-green-400 tabular-nums mt-1">
                    {{ $purchaseOrder->has_deductions ? $purchaseOrder->formatted_adjusted_total_amount : $purchaseOrder->formatted_total_amount }}
                </p>
                @if($purchaseOrder->escrow_released_at)
                    <p class="text-[10px] text-green-600 mt-1">{{ $purchaseOrder->escrow_released_at->format('d M Y, H:i') }}</p>
                @endif
            </div>
        </div>
    @endif
</div>
