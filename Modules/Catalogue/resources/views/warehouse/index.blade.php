@extends('layouts.app', [
    'title' => 'WMS Control Center',
    'breadcrumbs' => [
        ['name' => 'Logistics', 'url' => url('/')],
        ['name' => 'WMS Dashboard', 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">LOGISTICS CORE</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Inventory Nodes Active</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                Warehouse <span class="text-primary-600">Management System</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('warehouse.scan') }}" class="h-16 px-10 flex items-center bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-gray-900/20 hover:bg-black transition-all active:scale-[0.98]">
                Initiate QR Scan
            </a>
            <a href="{{ route('warehouse.report') }}" class="h-16 px-8 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 text-[11px] font-black text-gray-400 uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                Audit Logs
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary-50 dark:bg-primary-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Total SKU Nodes</p>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-gray-900 dark:text-white leading-none">{{ $totalItems }}</span>
                    <span class="text-xs font-bold text-gray-400 mb-1">SKUs</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 dark:bg-indigo-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-6">Physical Inventory</p>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-indigo-600 leading-none">{{ $totalStock }}</span>
                    <span class="text-xs font-bold text-gray-400 mb-1">Units</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-50 dark:bg-red-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-6">Low Stock Alerts</p>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-red-600 leading-none">{{ count($lowStockItems) }}</span>
                    <span class="text-xs font-bold text-gray-400 mb-1">Critical</span>
                </div>
            </div>
            @if(count($lowStockItems) > 0)
                <div class="absolute top-8 right-8 w-2 h-2 rounded-full bg-red-500 animate-ping"></div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-6">Network Health</p>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-emerald-600 leading-none">100%</span>
                    <span class="text-xs font-bold text-gray-400 mb-1">Operational</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Inventory List --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Recent Inventory Ingress</h3>
                <a href="{{ route('catalogue.index') }}" class="text-[9px] font-black text-primary-600 uppercase tracking-widest">Global Catalogue</a>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">SKU Artifact</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Category</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Availability</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800 text-right">Label</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse($recentItems as $item)
                                <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center relative overflow-hidden shadow-inner group-hover:scale-105 transition-transform duration-500">
                                                @if($item->primaryImage)
                                                    <img src="{{ asset('storage/' . $item->primaryImage->image_path) }}" class="w-full h-full object-cover">
                                                @else
                                                    <i data-feather="package" class="w-5 h-5 text-gray-300"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-1">{{ $item->name }}</p>
                                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none">{{ $item->sku }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $item->category->name ?? 'GENERAL' }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs font-black {{ $item->stock <= ($item->min_stock ?? 5) ? 'text-red-600' : 'text-gray-900 dark:text-white' }} tabular-nums">{{ $item->stock }}</span>
                                            <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">ON HAND</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <a href="{{ route('warehouse.qr', $item->id) }}" class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-900 text-gray-400 hover:bg-primary-600 hover:text-white transition-all shadow-sm">
                                            <i data-feather="printer" class="w-4 h-4"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Zero SKUs detected in local node</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Low Stock Terminal --}}
        <div class="space-y-8">
            <h3 class="text-[10px] font-black text-red-600 uppercase tracking-[0.3em]">Critical Stock Low Threshold</h3>
            
            <div class="space-y-4">
                @forelse($lowStockItems as $item)
                    <div class="bg-red-50 dark:bg-red-900/10 rounded-[2rem] p-6 border border-red-100 dark:border-red-900/30 flex items-center justify-between group">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center text-red-600 shadow-sm shrink-0">
                                <i data-feather="alert-triangle" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1 truncate max-w-[120px]">{{ $item->name }}</h4>
                                <p class="text-[9px] font-bold text-red-600 uppercase tracking-widest leading-none">{{ $item->stock }} Units Left</p>
                            </div>
                        </div>
                        <a href="{{ route('catalogue.edit', $item->id) }}" class="h-10 px-4 flex items-center bg-white dark:bg-gray-800 text-[9px] font-black text-primary-600 uppercase tracking-widest rounded-xl shadow-sm border border-transparent hover:border-primary-300 transition-all">
                            Replenish
                        </a>
                    </div>
                @empty
                    <div class="bg-emerald-50 dark:bg-emerald-900/10 rounded-[2rem] p-8 text-center border border-dashed border-emerald-100 dark:border-emerald-900/30">
                        <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Global inventory levels stable</p>
                    </div>
                @endforelse
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
