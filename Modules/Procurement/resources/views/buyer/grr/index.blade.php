@extends('layouts.app', [
    'title' => 'Returns & Issues Audit',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Returns', 'url' => null],
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-red-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">TECHNICAL DISCREPANCY</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $grrList->total() }} Active Issues Logged</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Goods Return <span class="text-primary-600">Requests</span></h1>
        </div>
    </div>

    {{-- Filter Nav --}}
    <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-2 mb-8 flex flex-wrap gap-2 shadow-sm">
        <a href="{{ route('procurement.grr.index', ['filter' => 'all', 'view' => 'buyer']) }}"
            class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all
            {{ $filter == 'all' ? 'bg-gray-900 text-white' : 'text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900' }}">
            All Events
        </a>
        <a href="{{ route('procurement.grr.index', ['filter' => 'pending', 'view' => 'buyer']) }}"
            class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-3
            {{ $filter == 'pending' ? 'bg-yellow-500 text-white' : 'text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900' }}">
            Awaiting Review
            @if($pendingCount > 0)
                <span class="px-2 py-0.5 bg-white/20 rounded-md text-[8px] font-black">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('procurement.grr.index', ['filter' => 'in_progress', 'view' => 'buyer']) }}"
            class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all
            {{ $filter == 'in_progress' ? 'bg-primary-600 text-white' : 'text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900' }}">
            In Resolution
        </a>
        <a href="{{ route('procurement.grr.index', ['filter' => 'resolved', 'view' => 'buyer']) }}"
            class="px-6 py-3 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-3
            {{ $filter == 'resolved' ? 'bg-emerald-600 text-white' : 'text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-900' }}">
            Archive (Resolved)
        </a>
    </div>

    @if($grrList->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-20 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 rounded-3xl flex items-center justify-center mx-auto mb-6">
                <i data-feather="thumbs-up" class="w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Zero Issues Found</h3>
            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase tracking-widest">No quantity discrepancies or technical damages have been reported.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($grrList as $grr)
                <div class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-800 p-8 shadow-sm transition-all hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-none relative group">
                    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
                        <div class="flex items-start gap-6">
                            <div class="w-14 h-14 rounded-2xl flex flex-col items-center justify-center shadow-inner
                                @if($grr->issue_type === 'rejected') bg-red-50 text-red-600
                                @elseif($grr->issue_type === 'damaged') bg-yellow-50 text-yellow-600
                                @else bg-gray-50 text-gray-400 @endif">
                                <i data-feather="alert-octagon" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-3 mb-1">
                                    <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $grr->grr_number }}</h3>
                                    <span class="px-2 py-0.5 rounded-md text-[8px] font-black uppercase tracking-widest
                                        @if($grr->resolution_status === 'resolved') bg-emerald-100 text-emerald-700
                                        @elseif($grr->resolution_status === 'pending') bg-yellow-100 text-yellow-700
                                        @else bg-primary-100 text-primary-700 @endif">
                                        {{ str_replace('_', ' ', $grr->resolution_status) }}
                                    </span>
                                </div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">
                                    ISSUE: {{ $grr->issue_type_label }} • ON {{ $grr->created_at->format('M d, Y') }}
                                </p>
                                
                                <div class="flex items-center gap-6">
                                    <div>
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Impacted Item</p>
                                        <p class="text-[11px] font-black text-gray-700 dark:text-gray-300 uppercase tracking-tight">
                                            {{ $grr->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? 'UNKNOWN ITEM' }}
                                        </p>
                                    </div>
                                    <div class="px-4 py-2 bg-gray-50 dark:bg-gray-900 rounded-xl text-center">
                                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Qty</p>
                                        <p class="text-[11px] font-black text-gray-900 dark:text-white">{{ $grr->quantity_affected }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col lg:items-end gap-3">
                            <div class="text-right">
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Resolution Strategy</p>
                                <p class="text-base font-black text-primary-600 uppercase tracking-tight">{{ $grr->resolution_type_label ?? 'AWAITING SELECTION' }}</p>
                            </div>
                            <a href="{{ route('procurement.grr.show', $grr) }}"
                               class="px-5 py-2.5 bg-gray-900 text-white dark:bg-white dark:text-gray-900 rounded-xl text-[10px] font-black uppercase tracking-widest hover:scale-105 transition-all shadow-xl shadow-gray-900/10">
                                Audit Case Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($grrList->hasPages())
            <div class="mt-8">
                {{ $grrList->links() }}
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
@endpush
