@extends('layouts.app', [
    'title' => 'Storage Unit Detail: ' . $warehouse->code,
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => url('/')],
        ['name' => 'Storage Points', 'url' => route('procurement.warehouse.index')],
        ['name' => $warehouse->code, 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">LOGISTICS NODE</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-3">
                {{ $warehouse->name }}
            </h1>
            <p class="text-gray-500 font-medium">Detailed tracking and configuration for this storage point.</p>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('procurement.warehouse.edit', $warehouse) }}" class="h-16 px-10 flex items-center bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-gray-900/20 hover:bg-black transition-all active:scale-[0.98]">
                Edit Unit Data
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Unit Metadata --}}
        <div class="space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm p-10 space-y-8">
                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Node Identity</p>
                    <div class="space-y-1">
                        <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $warehouse->name }}</p>
                        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-[0.2em]">{{ $warehouse->code }}</p>
                    </div>
                </div>

                <div>
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Location Signature</p>
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800">
                        <p class="text-xs font-bold text-gray-600 dark:text-gray-400 leading-relaxed italic">
                            {{ $warehouse->address ?? 'No physical address defined.' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stock Summary (If integrated) --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Current Inventory Content</h3>
                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-800 rounded-lg text-[9px] font-black text-gray-400 uppercase tracking-widest">
                    {{ $warehouse->stocks->count() ?? 0 }} items tracked
                </span>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Item Description</th>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Physical Stock</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @forelse($warehouse->stocks ?? [] as $stock)
                            <tr>
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-300">
                                            <i data-feather="package" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $stock->catalogueItem->name ?? 'Unknown Item' }}</p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $stock->catalogueItem->sku ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6 text-right">
                                    <span class="text-sm font-black text-gray-900 dark:text-white tabular-nums">{{ $stock->quantity }}</span>
                                    <span class="ml-1 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Units</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-10 py-20 text-center">
                                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Storage point is currently hollow</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
