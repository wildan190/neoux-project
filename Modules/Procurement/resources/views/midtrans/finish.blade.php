@extends('layouts.app', [
    'title' => 'Payment Status',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => '#'],
        ['name' => 'Payment Status', 'url' => null],
    ]
])

@section('content')
<div class="max-w-3xl mx-auto py-12">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden text-center p-12 relative">
        
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary-500/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-primary-500/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            @if(in_array($transactionStatus, ['settlement', 'capture']))
                <div class="w-24 h-24 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-emerald-200 dark:border-emerald-800">
                    <i data-feather="check-circle" class="w-12 h-12"></i>
                </div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">Payment Successful</h1>
                <p class="text-gray-500 dark:text-gray-400 font-bold mb-8">Your escrow payment has been processed successfully.</p>
            @elseif($transactionStatus == 'pending')
                <div class="w-24 h-24 bg-amber-100 dark:bg-amber-900/30 text-amber-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-amber-200 dark:border-amber-800">
                    <i data-feather="clock" class="w-12 h-12"></i>
                </div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">Payment Pending</h1>
                <p class="text-gray-500 dark:text-gray-400 font-bold mb-8">Please complete your payment using the instructions provided by Midtrans.</p>
            @else
                <div class="w-24 h-24 bg-red-100 dark:bg-red-900/30 text-red-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-red-200 dark:border-red-800">
                    <i data-feather="x-circle" class="w-12 h-12"></i>
                </div>
                <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2">Payment Failed / Canceled</h1>
                <p class="text-gray-500 dark:text-gray-400 font-bold mb-8">The payment process could not be completed or was canceled.</p>
            @endif

            @if($purchaseOrder)
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 max-w-sm mx-auto mb-10 text-left">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Reference Number</p>
                    <p class="text-sm font-bold text-gray-900 dark:text-white mb-4">{{ $purchaseOrder->po_number }}</p>
                    
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Amount</p>
                    <p class="text-xl font-black text-primary-600 dark:text-primary-400">Rp {{ number_format($purchaseOrder->adjusted_total_amount, 0, ',', '.') }}</p>
                </div>

                <a href="{{ route('procurement.po.show', $purchaseOrder) }}" class="inline-flex items-center justify-center px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[11px] font-black uppercase tracking-widest rounded-xl shadow-xl hover:scale-105 transition-all">
                    Return to Purchase Order
                </a>
            @else
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-8 py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[11px] font-black uppercase tracking-widest rounded-xl shadow-xl hover:scale-105 transition-all">
                    Return to Dashboard
                </a>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
