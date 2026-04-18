@extends('layouts.app', [
    'title' => 'Logistics Intelligence',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'WMS', 'url' => route('warehouse.index')],
        ['name' => 'Analytics', 'url' => '#']
    ]
])

@section('content')
<div class="space-y-8 pb-10">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">LOGISTICS <span class="text-primary-600">INTELLIGENCE</span></h1>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Real-time inventory movement & analysis</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[10px] font-black tracking-widest uppercase text-gray-500 hover:text-primary-600 transition-all shadow-sm">
                <i data-feather="download" class="w-3.5 h-3.5 mr-2"></i> EXPORT REPORT
            </button>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-6 opacity-5">
                <i data-feather="arrow-down-left" class="w-16 h-16"></i>
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Inbound Today</p>
            <div class="flex items-end gap-3">
                <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ number_format($todayIn) }}</h3>
                <span class="text-[10px] font-bold text-green-500 mb-1.5 uppercase tracking-widest">Units</span>
            </div>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-gray-500">
                <div class="w-1 h-1 rounded-full bg-green-500"></div> System Verified
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden">
            <div class="absolute top-0 right-0 p-6 opacity-5">
                <i data-feather="arrow-up-right" class="w-16 h-16"></i>
            </div>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Outbound Today</p>
            <div class="flex items-end gap-3">
                <h3 class="text-3xl font-black text-gray-900 dark:text-white tracking-tighter">{{ number_format($todayOut) }}</h3>
                <span class="text-[10px] font-bold text-primary-500 mb-1.5 uppercase tracking-widest">Units</span>
            </div>
            <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-gray-500">
                <div class="w-1 h-1 rounded-full bg-primary-500"></div> Dispatched
            </div>
        </div>

        <div class="bg-primary-600 rounded-3xl p-6 shadow-xl shadow-primary-600/20 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 p-6 opacity-10">
                <i data-feather="activity" class="w-16 h-16"></i>
            </div>
            <p class="text-[10px] font-black text-primary-100 uppercase tracking-[0.2em] mb-4">Top Performer</p>
            @if($topItems->first())
                <div class="space-y-1">
                    <h3 class="text-lg font-black tracking-tight leading-tight truncate">{{ $topItems->first()->item->product->name ?? 'N/A' }}</h3>
                    <p class="text-[10px] text-primary-100 font-bold uppercase tracking-widest">{{ number_format($topItems->first()->total_qty) }} Units Moved</p>
                </div>
            @else
                <h3 class="text-lg font-black tracking-tight">No Data Yet</h3>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Recent Movements --}}
        <div class="lg:col-span-2 space-y-4">
            <div class="flex items-center justify-between px-2">
                <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Transaction Log</h2>
                <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-800 rounded-md text-[9px] font-black text-gray-500">{{ $movements->total() }} LOGS</span>
            </div>
            
            <div class="bg-white dark:bg-gray-900 rounded-3xl border border-gray-100 dark:border-gray-800 overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Date & Source</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Asset</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Type</th>
                                <th class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Qty</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800/50">
                            @forelse($movements as $movement)
                                <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="space-y-0.5">
                                            <p class="text-xs font-black text-gray-900 dark:text-white">{{ $movement->created_at->format('d M Y') }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">{{ $movement->created_at->format('H:i') }} • {{ $movement->user->name ?? 'System' }}</p>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-gray-900 dark:text-white">{{ $movement->item->product->name ?? 'Unknown' }}</span>
                                            <span class="text-[9px] font-bold text-primary-600 uppercase tracking-widest">SKU: {{ $movement->item->sku }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($movement->type === 'in')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-green-50 dark:bg-green-900/10 text-[9px] font-black text-green-600 uppercase tracking-widest border border-green-100 dark:border-green-900/20">
                                                INBOUND
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-primary-50 dark:bg-primary-900/10 text-[9px] font-black text-primary-600 uppercase tracking-widest border border-primary-100 dark:border-primary-900/20">
                                                OUTBOUND
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-xs font-black {{ $movement->type === 'in' ? 'text-green-600' : 'text-primary-600' }}">
                                            {{ $movement->type === 'in' ? '+' : '-' }}{{ number_format($movement->quantity) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-20 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-12 h-12 bg-gray-50 dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-300 dark:text-gray-600 mb-4">
                                                <i data-feather="activity" class="w-6 h-6"></i>
                                            </div>
                                            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">No Activity Logged</h3>
                                            <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest mt-1">Movement data will appear here.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($movements->hasPages())
                    <div class="px-6 py-4 bg-gray-50/50 dark:bg-gray-800/50 border-t border-gray-100 dark:border-gray-800">
                        {{ $movements->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Hot Assets --}}
        <div class="space-y-4">
            <h2 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] px-2">Hot Assets</h2>
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-6 shadow-sm space-y-6">
                @forelse($topItems as $top)
                    <div class="flex items-center gap-4 group">
                        <div class="w-10 h-10 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-primary-600 border border-gray-100 dark:border-gray-800 group-hover:bg-primary-600 group-hover:text-white transition-all">
                            <i data-feather="package" class="w-5 h-5"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-black text-gray-900 dark:text-white truncate tracking-tight uppercase">{{ $top->item->product->name ?? 'Unknown' }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $top->count }} Movements</p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs font-black text-gray-900 dark:text-white">{{ number_format($top->total_qty) }}</p>
                            <p class="text-[9px] font-bold text-primary-500 uppercase tracking-widest">Units</p>
                        </div>
                    </div>
                @empty
                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest text-center py-4">Insights pending...</p>
                @endforelse
            </div>

            <div class="bg-gray-900 rounded-3xl p-6 text-white relative overflow-hidden shadow-xl shadow-gray-900/20">
                <div class="absolute top-0 right-0 p-4 opacity-10">
                    <i data-feather="pie-chart" class="w-20 h-20 -mr-4 -mt-4"></i>
                </div>
                <div class="relative z-10 space-y-4">
                    <h3 class="text-xs font-black tracking-widest uppercase">System Health</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between text-[9px] font-black text-gray-400">
                            <span>DATA INTEGRITY</span>
                            <span class="text-green-500">OPTIMAL</span>
                        </div>
                        <div class="h-1 bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-green-500 w-[94%]"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endsection
