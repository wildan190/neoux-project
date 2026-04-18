@extends('layouts.app', [
    'title' => 'Asset Identifier',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'WMS', 'url' => route('warehouse.index')],
        ['name' => 'QR Label', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-2xl mx-auto space-y-8 pb-20">
    {{-- Identifier Unit --}}
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 shadow-xl overflow-hidden">
        <div class="bg-gray-900 px-8 py-6 flex items-center justify-between text-white border-b-4 border-primary-600">
            <div class="space-y-1">
                <h2 class="text-xl font-black tracking-tighter uppercase">ASSET <span class="text-primary-500">LABEL</span></h2>
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.2em]">Validated System Identity</p>
            </div>
            <button onclick="window.print()" class="w-12 h-12 bg-white/5 hover:bg-white/10 rounded-2xl flex items-center justify-center transition-all group">
                <i data-feather="printer" class="w-5 h-5 text-gray-400 group-hover:text-white transition-colors"></i>
            </button>
        </div>

        <div class="p-12">
            {{-- Printable Area --}}
            <div id="printable-label" class="bg-white border-2 border-gray-100 rounded-3xl p-10 flex flex-col items-center text-center space-y-8 print:border-none print:p-0 print:shadow-none mx-auto max-w-sm">
                <div class="w-full flex justify-between items-start mb-4">
                    <div class="text-left space-y-0.5">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">WMS IDENTITY</p>
                        <p class="text-xs font-black text-gray-900 uppercase tracking-tight">{{ config('app.name', 'NEOUX') }}</p>
                    </div>
                    <div class="text-right space-y-0.5">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest">UNIT</p>
                        <p class="text-xs font-black text-gray-900 uppercase tracking-tight">{{ $item->unit }}</p>
                    </div>
                </div>

                <div class="bg-gray-50 p-6 rounded-[2rem] border-2 border-dashed border-gray-100">
                    <div class="w-56 h-56 flex items-center justify-center bg-white shadow-sm p-4 rounded-2xl">
                        {!! $qrCode !!}
                    </div>
                </div>

                <div class="space-y-2">
                    <h3 class="text-2xl font-black text-gray-900 tracking-tighter uppercase leading-tight">{{ $item->product->name }}</h3>
                    <div class="flex items-center justify-center gap-3">
                        <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black tracking-widest uppercase">{{ $item->sku }}</span>
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-50 w-full flex justify-center opacity-50">
                    <div class="flex flex-col items-center gap-1">
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-[0.3em]">SCAN TO AUTHENTICATE</p>
                        <div class="flex gap-1">
                            <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                            <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                            <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Action Hints --}}
            <div class="mt-12 grid grid-cols-2 gap-4">
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 space-y-2">
                    <i data-feather="shield" class="w-5 h-5 text-primary-500"></i>
                    <h4 class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Tamper Proof</h4>
                    <p class="text-[9px] font-bold text-gray-400 leading-relaxed">Unique SKU-encrypted identifier ensures zero duplication during physical inventory cycles.</p>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-800 space-y-2">
                    <i data-feather="layers" class="w-5 h-5 text-gray-400"></i>
                    <h4 class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Bulk Printing</h4>
                    <p class="text-[9px] font-bold text-gray-400 leading-relaxed">Standardized for thermal label printers (4x4 inch). Supports mass asset tagging protocols.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #printable-label, #printable-label * { visibility: visible; }
    #printable-label {
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        max-width: none;
        border: none !important;
    }
    .no-print { display: none !important; }
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
