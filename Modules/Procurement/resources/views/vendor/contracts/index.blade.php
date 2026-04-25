@extends('layouts.app', [
    'title' => 'Annual Sales Contracts',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => url('/')],
        ['name' => 'Sales Contracts', 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">MASTER SALES AGREEMENTS</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Fixed Pricing Commitments</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                Sales <span class="text-emerald-600">Contracts</span>
            </h1>
        </div>
    </div>

    {{-- Contract Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($contracts as $contract)
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl hover:shadow-emerald-200/20 transition-all p-8 flex flex-col justify-between group">
                <div>
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-14 h-14 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center text-emerald-600 shadow-inner group-hover:bg-emerald-50 transition-colors">
                            <i data-feather="file-text" class="w-6 h-6"></i>
                        </div>
                        <span class="px-3 py-1 bg-{{ $contract->status_color }}-500 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $contract->status }}</span>
                    </div>
                    
                    <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2 group-hover:text-emerald-600 transition-colors line-clamp-1">{{ $contract->title }}</h3>
                    <div class="flex items-center gap-2 mb-6">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $contract->buyer->name ?? 'Unknown Buyer' }}</p>
                        <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                        <p class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest">{{ $contract->contract_number }}</p>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-8">
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">EFFECTIVE</p>
                            <p class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ $contract->start_date->format('M d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">EXPIRATION</p>
                            <p class="text-xs font-bold text-gray-900 dark:text-white uppercase">{{ $contract->end_date->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-6 border-t border-gray-50 dark:border-gray-800/50">
                    <a href="{{ route('procurement.contracts.show', $contract) }}" class="flex-1 h-12 flex items-center justify-center bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-black transition-all">
                        @if($contract->status === 'proposed') Sign Contract @else View Matrix @endif
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-20 bg-gray-50 dark:bg-gray-900/10 rounded-[3rem] border-2 border-dashed border-gray-100 dark:border-gray-800 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-white dark:bg-gray-800 rounded-3xl flex items-center justify-center text-gray-200 mb-6 shadow-sm">
                    <i data-feather="briefcase" class="w-10 h-10"></i>
                </div>
                <h3 class="text-sm font-black text-gray-400 uppercase tracking-[0.2em] mb-2">No Sales Contracts</h3>
                <p class="text-xs text-gray-400 max-w-xs px-10">Annual contracts from buyers will appear here once they propose a master agreement for negotiated items.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-8">
        {{ $contracts->links() }}
    </div>
</div>
@endsection
