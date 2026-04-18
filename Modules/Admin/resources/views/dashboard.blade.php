@extends('layouts.app', [
    'title' => 'Admin Dashboard',
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Global Dashboard', 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">SYSTEM CONTROL</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Platform Overview</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                Network <span class="text-primary-600">Performance</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4 bg-white dark:bg-gray-800 p-3 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="w-12 h-12 rounded-2xl bg-primary-100 dark:bg-primary-900/20 flex items-center justify-center text-primary-600">
                <i data-feather="activity" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">System status</p>
                <p class="text-xs font-black text-emerald-500 uppercase tracking-widest leading-none">All nodes healthy</p>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary-50 dark:bg-primary-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Total Companies</p>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-gray-900 dark:text-white leading-none">{{ $totalCompanies }}</span>
                    <span class="text-xs font-bold text-gray-400 mb-1">entities</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-yellow-50 dark:bg-yellow-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-yellow-600 uppercase tracking-widest mb-6">Pending Review</p>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-yellow-600 leading-none">{{ $pendingCompanies }}</span>
                    <span class="text-xs font-bold text-gray-400 mb-1">requests</span>
                </div>
            </div>
            @if($pendingCompanies > 0)
                <div class="absolute top-8 right-8 w-2 h-2 rounded-full bg-yellow-500 animate-ping"></div>
            @endif
        </div>

        <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-6">Active verified</p>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-emerald-600 leading-none">{{ $activeCompanies }}</span>
                    <span class="text-xs font-bold text-gray-400 mb-1">active</span>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-8 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-red-50 dark:bg-red-900/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
            <div class="relative z-10">
                <p class="text-[10px] font-black text-red-600 uppercase tracking-widest mb-6">Declined entities</p>
                <div class="flex items-end gap-3">
                    <span class="text-4xl font-black text-red-600 leading-none">{{ $declinedCompanies }}</span>
                    <span class="text-xs font-bold text-gray-400 mb-1">inactive</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Recent Tenders --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="flex items-center justify-between">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Latest Tender activity</h3>
                <a href="{{ route('admin.companies.index') }}" class="text-[9px] font-black text-primary-600 uppercase tracking-widest hover:translate-x-1 transition-transform inline-flex items-center gap-2">View Audit Log <i data-feather="arrow-right" class="w-3 h-3"></i></a>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Company</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Tender #</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Winner</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100 dark:border-gray-800">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse($recentTenders as $tender)
                                <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-900 flex items-center justify-center text-[10px] font-black text-gray-400 uppercase shadow-inner">
                                                {{ substr($tender->company->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-gray-900 dark:text-white leading-none mb-1">{{ $tender->company->name }}</p>
                                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none">{{ $tender->company->category }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="text-xs font-black text-gray-500 uppercase tracking-tight">{{ $tender->pr_number }}</p>
                                    </td>
                                    <td class="px-8 py-6">
                                        @if($tender->winningOffer)
                                            <div class="flex items-center gap-2">
                                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                                                <span class="text-xs font-bold text-gray-600 dark:text-gray-400">{{ $tender->winningOffer->company->name }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs font-bold text-gray-300 uppercase tracking-widest">In Progress</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 text-[9px] font-black rounded-lg uppercase tracking-widest">
                                            {{ str_replace('_', ' ', $tender->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <p class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">No recent tender activity recorded</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Products --}}
        <div class="space-y-8">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Network High Performers</h3>
            
            <div class="space-y-4">
                @forelse($topProducts as $product)
                    <div class="bg-white dark:bg-gray-800 rounded-[2rem] p-6 border border-gray-100 dark:border-gray-800 shadow-sm group hover:border-primary-300 transition-all duration-300">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-gray-50 dark:bg-gray-900 relative overflow-hidden shadow-inner shrink-0 leading-[0]">
                                @if($product->image_url)
                                    <img src="{{ asset('storage/' . $product->image_url) }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-200">
                                        <i data-feather="package" class="w-8 h-8"></i>
                                    </div>
                                @endif
                                <div class="absolute top-1 right-1 px-1.5 py-0.5 bg-gray-900/80 text-white text-[8px] font-black rounded-md z-10">
                                    TOP
                                </div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[8px] font-black text-primary-600 uppercase tracking-[0.2em] mb-1 leading-none">{{ $product->category->name ?? 'UNCATEGORIZED' }}</p>
                                <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight truncate mb-2">{{ $product->name }}</h4>
                                <div class="flex items-center justify-between">
                                    <span class="text-[10px] font-black text-gray-400 tabular-nums uppercase tracking-widest">{{ $product->transaction_count }} Deals</span>
                                    <span class="text-[10px] font-black text-emerald-500 tabular-nums uppercase tracking-widest">{{ $product->total_sold }} Sold</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-gray-50 dark:bg-gray-900/20 rounded-[2rem] p-12 text-center border border-dashed border-gray-100 dark:border-gray-800">
                        <p class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Awaiting sales data</p>
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
