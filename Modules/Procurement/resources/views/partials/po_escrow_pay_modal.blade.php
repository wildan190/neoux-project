{{-- Escrow Payment Modal --}}
<div id="escrowPayModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-2xl w-full max-w-md relative overflow-hidden transform transition-all">
        {{-- Header Decor --}}
        <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-primary-400 to-primary-600"></div>
        
        <button onclick="document.getElementById('escrowPayModal').classList.add('hidden')" class="absolute top-6 right-6 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
            <i data-feather="x" class="w-6 h-6"></i>
        </button>

        <div class="p-8">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary-50 dark:bg-primary-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-primary-100 dark:border-primary-800/50">
                    <i data-feather="shield" class="w-8 h-8 text-primary-600 dark:text-primary-400"></i>
                </div>
                <h3 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">Pembayaran Escrow</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Amankan transaksi Anda dengan deposit dana ke sistem escrow kami.</p>
            </div>

            <div class="p-6 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700 mb-8">
                <div class="flex justify-between items-center mb-1">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Tagihan</p>
                    <span class="px-2 py-0.5 bg-primary-100 dark:bg-primary-900/40 text-primary-700 dark:text-primary-300 text-[10px] font-bold rounded-full">MUST PAY</span>
                </div>
                <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums">
                    {{ $purchaseOrder->has_deductions ? $purchaseOrder->formatted_adjusted_total_amount : $purchaseOrder->formatted_total_amount }}
                </p>
            </div>

            <form action="{{ route('procurement.po.escrow-pay', $purchaseOrder) }}" method="POST" onsubmit="return handlePrFormSubmit(this)">
                @csrf
                <div class="space-y-6">


                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="document.getElementById('escrowPayModal').classList.add('hidden')" 
                                class="flex-1 py-4 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 font-bold transition-all">
                            Batal
                        </button>
                        <button type="submit" class="flex-2 py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold flex items-center justify-center gap-2 shadow-lg shadow-primary-500/20 transition-all hover:-translate-y-0.5 active:translate-y-0">
                            <i data-feather="check-circle" class="w-5 h-5"></i>
                            Konfirmasi Bayar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
