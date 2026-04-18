@extends('layouts.app', [
    'title' => 'Quick Action Approvals',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Quick Approvals', 'url' => null],
    ]
])

@section('content')
    <div x-data="{ activeTab: 'all' }" class="space-y-10">
        {{-- Header & Stats --}}
        <div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                <div>
                    <div class="flex items-center gap-3 mb-1">
                        <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">COMMAND CENTER</span>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Pending Verification</span>
                    </div>
                    <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Quick Action <span class="text-primary-600">Approvals</span></h1>
                </div>
                
                <div class="flex items-center gap-4 bg-white dark:bg-gray-800 p-3 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800">
                    <div class="flex -space-x-3">
                        <div class="w-10 h-10 rounded-full bg-primary-100 border-4 border-white dark:border-gray-800 flex items-center justify-center text-[10px] font-black text-primary-600">PR</div>
                        <div class="w-10 h-10 rounded-full bg-emerald-100 border-4 border-white dark:border-gray-800 flex items-center justify-center text-[10px] font-black text-emerald-600">PO</div>
                        <div class="w-10 h-10 rounded-full bg-blue-100 border-4 border-white dark:border-gray-800 flex items-center justify-center text-[10px] font-black text-blue-600">IV</div>
                    </div>
                    <div class="pr-4">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest leading-none mb-1">Total Queue</p>
                        <p class="text-xl font-black text-gray-900 dark:text-white leading-none">
                            {{ $pendingPRs->count() + $pendingPOs->count() + $pendingInvoices->count() + $vendorHeadInvoices->count() + $pendingDebitNotes->count() + $pendingGRRs->count() }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                {{-- PRs --}}
                <button @click="activeTab = 'prs'" 
                    class="group relative bg-white dark:bg-gray-800 p-8 rounded-[2rem] border transition-all duration-300 text-left hover:shadow-2xl hover:-translate-y-1"
                    :class="activeTab === 'prs' ? 'border-primary-500 ring-4 ring-primary-500/10 shadow-xl shadow-primary-500/10' : 'border-transparent hover:border-primary-200 dark:hover:border-primary-800 shadow-sm'">
                    <div class="w-14 h-14 rounded-2xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-primary-600 dark:text-primary-400 mb-6 group-hover:scale-110 transition-transform">
                        <i data-feather="file-text" class="w-6 h-6"></i>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Requisitions</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums leading-none">{{ $pendingPRs->count() }}</p>
                    @if($pendingPRs->count() > 0)
                        <div class="absolute top-8 right-8 w-2.5 h-2.5 rounded-full bg-primary-500 animate-pulse"></div>
                    @endif
                </button>

                {{-- POs --}}
                <button @click="activeTab = 'pos'" 
                    class="group relative bg-white dark:bg-gray-800 p-8 rounded-[2rem] border transition-all duration-300 text-left hover:shadow-2xl hover:-translate-y-1"
                    :class="activeTab === 'pos' ? 'border-emerald-500 ring-4 ring-emerald-500/10 shadow-xl shadow-emerald-500/10' : 'border-transparent hover:border-emerald-200 dark:hover:border-emerald-800 shadow-sm'">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-6 group-hover:scale-110 transition-transform">
                        <i data-feather="shopping-bag" class="w-6 h-6"></i>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Orders</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums leading-none">{{ $pendingPOs->count() }}</p>
                    @if($pendingPOs->count() > 0)
                        <div class="absolute top-8 right-8 w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
                    @endif
                </button>

                {{-- Invoices --}}
                <button @click="activeTab = 'invoices'" 
                    class="group relative bg-white dark:bg-gray-800 p-8 rounded-[2rem] border transition-all duration-300 text-left hover:shadow-2xl hover:-translate-y-1"
                    :class="activeTab === 'invoices' ? 'border-blue-500 ring-4 ring-blue-500/10 shadow-xl shadow-blue-500/10' : 'border-transparent hover:border-blue-200 dark:hover:border-blue-800 shadow-sm'">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-6 group-hover:scale-110 transition-transform">
                        <i data-feather="credit-card" class="w-6 h-6"></i>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Invoices</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums leading-none">{{ $pendingInvoices->count() + $vendorHeadInvoices->count() }}</p>
                    @if(($pendingInvoices->count() + $vendorHeadInvoices->count()) > 0)
                        <div class="absolute top-8 right-8 w-2.5 h-2.5 rounded-full bg-blue-500 animate-pulse"></div>
                    @endif
                </button>

                {{-- Others --}}
                <button @click="activeTab = 'others'" 
                    class="group relative bg-white dark:bg-gray-800 p-8 rounded-[2rem] border transition-all duration-300 text-left hover:shadow-2xl hover:-translate-y-1"
                    :class="activeTab === 'others' ? 'border-orange-500 ring-4 ring-orange-500/10 shadow-xl shadow-orange-500/10' : 'border-transparent hover:border-orange-200 dark:hover:border-orange-800 shadow-sm'">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400 mb-6 group-hover:scale-110 transition-transform">
                        <i data-feather="alert-circle" class="w-6 h-6"></i>
                    </div>
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-1">Returns</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums leading-none">{{ $pendingDebitNotes->count() + $pendingGRRs->count() }}</p>
                    @if(($pendingDebitNotes->count() + $pendingGRRs->count()) > 0)
                        <div class="absolute top-8 right-8 w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></div>
                    @endif
                </button>
            </div>
        </div>

        {{-- Main Task Board --}}
        <div>
            @php
                $allItemsEmpty = $pendingPRs->isEmpty() && $pendingPOs->isEmpty() && $pendingInvoices->isEmpty() && $vendorHeadInvoices->isEmpty() && $pendingDebitNotes->isEmpty() && $pendingGRRs->isEmpty();
            @endphp
            
            @if($allItemsEmpty)
                <div class="bg-gray-50 dark:bg-gray-900/20 rounded-[3rem] p-32 text-center border-2 border-dashed border-gray-100 dark:border-gray-800">
                    <div class="w-24 h-24 bg-emerald-100 dark:bg-emerald-900/20 text-emerald-600 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-inner">
                        <i data-feather="check" class="w-12 h-12"></i>
                    </div>
                    <h3 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-3">System Healthy</h3>
                    <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-[0.2em] max-w-sm mx-auto leading-relaxed">All procurement queues are currently clear from pending approvals.</p>
                </div>
            @else
                {{-- Requisitions --}}
                <div x-show="activeTab === 'all' || activeTab === 'prs'" class="mb-12" x-transition:enter="transition ease-out duration-300">
                    @if($pendingPRs->isNotEmpty())
                        <div class="mb-6 flex items-center gap-4">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">Open Requisitions</h3>
                            <div class="h-px bg-gray-50 dark:bg-gray-800 w-full"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            @foreach($pendingPRs as $pr)
                                <a href="{{ route('procurement.pr.show', $pr) }}" class="group bg-white dark:bg-gray-800 rounded-[2rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all duration-500">
                                    <div class="flex justify-between items-start mb-6">
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-[9px] font-black rounded-lg uppercase tracking-wider">
                                            {{ $pr->tender_status === 'pending_winner_approval' ? 'Winner Selection' : 'Review' }}
                                        </span>
                                        <span class="text-[9px] font-black text-gray-400 uppercase">{{ $pr->created_at->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="text-lg font-black text-gray-900 dark:text-white mb-2 line-clamp-1 group-hover:text-primary-600 transition-colors uppercase tracking-tight">{{ $pr->title }}</h4>
                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-8">{{ $pr->pr_number }}</p>
                                    
                                    <div class="flex items-center justify-between pt-6 border-t border-gray-50 dark:border-gray-800">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-xl bg-primary-100 flex items-center justify-center text-[11px] font-black text-primary-700">
                                                {{ substr($pr->user->name, 0, 1) }}
                                            </div>
                                            <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $pr->user->name }}</span>
                                        </div>
                                        <i data-feather="arrow-right" class="w-5 h-5 text-gray-300 group-hover:text-primary-600 transition-all group-hover:translate-x-1"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Invoices --}}
                <div x-show="activeTab === 'all' || activeTab === 'invoices'" class="mb-12" x-transition:enter="transition ease-out duration-300">
                    @if($pendingInvoices->isNotEmpty() || $vendorHeadInvoices->isNotEmpty())
                        <div class="mb-6 flex items-center gap-4">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">Awaiting Settlement</h3>
                            <div class="h-px bg-gray-50 dark:bg-gray-800 w-full"></div>
                        </div>
                        <div class="space-y-4">
                            @foreach($pendingInvoices->merge($vendorHeadInvoices) as $invoice)
                                <a href="{{ route('procurement.invoices.show', $invoice) }}" class="group block bg-white dark:bg-gray-800 rounded-[1.5rem] p-6 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl hover:border-primary-300 transition-all duration-300">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-6">
                                            <div class="w-14 h-14 rounded-2xl bg-blue-50 dark:bg-blue-900/20 text-blue-600 flex items-center justify-center shadow-inner group-hover:scale-110 transition-transform">
                                                <i data-feather="file-text" class="w-6 h-6"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-base font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1 group-hover:text-primary-600 transition-colors">{{ $invoice->invoice_number }}</h4>
                                                <div class="flex items-center gap-3">
                                                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest leading-none">PO: {{ $invoice->purchaseOrder->po_number }}</span>
                                                    <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                                                    <span class="text-[10px] font-black uppercase text-primary-500 tracking-widest leading-none">{{ str_replace('_', ' ', $invoice->status) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-right flex items-center gap-8">
                                            <div>
                                                <p class="text-xl font-black text-gray-900 dark:text-white tracking-tight">{{ $invoice->formatted_total_amount }}</p>
                                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mt-1">{{ $invoice->created_at->format('M d, Y') }}</p>
                                            </div>
                                            <div class="w-10 h-10 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-300 group-hover:bg-primary-600 group-hover:text-white transition-all">
                                                <i data-feather="chevron-right" class="w-5 h-5"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Others --}}
                <div x-show="activeTab === 'all' || activeTab === 'others'" class="mb-12" x-transition:enter="transition ease-out duration-300">
                    @if($pendingDebitNotes->isNotEmpty() || $pendingGRRs->isNotEmpty())
                        <div class="mb-6 flex items-center gap-4">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] whitespace-nowrap">Exceptions & Discrepancies</h3>
                            <div class="h-px bg-gray-50 dark:bg-gray-800 w-full"></div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($pendingDebitNotes as $dn)
                                <a href="{{ route('procurement.debit-notes.show', $dn) }}" class="group bg-white dark:bg-gray-800 rounded-[2rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-2xl transition-all duration-500 relative overflow-hidden">
                                     <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity">
                                        <i data-feather="dollar-sign" class="w-24 h-24 text-red-600"></i>
                                    </div>
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="w-10 h-10 bg-red-50 dark:bg-red-900/20 text-red-600 rounded-xl flex items-center justify-center">
                                            <i data-feather="file-minus" class="w-5 h-5"></i>
                                        </div>
                                        <span class="text-[9px] font-black text-gray-400 uppercase">{{ $dn->created_at->format('M d') }}</span>
                                    </div>
                                    <h4 class="text-base font-black text-gray-900 dark:text-white mb-1 uppercase tracking-tight">Debit Note #{{ $dn->id }}</h4>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-6">Adjustment from {{ $dn->purchaseOrder->vendorCompany->name }}</p>
                                    <div class="flex items-end justify-between pt-6 border-t border-gray-50 dark:border-gray-800">
                                        <span class="text-2xl font-black text-red-600 tracking-tight">-{{ number_format($dn->deduction_amount, 2) }}</span>
                                        <span class="text-[10px] font-black uppercase text-primary-600 tracking-widest group-hover:translate-x-1 transition-transform inline-flex items-center gap-2">Audit View <i data-feather="arrow-right" class="w-4 h-4"></i></span>
                                    </div>
                                </a>
                            @endforeach

                            @foreach($pendingGRRs as $grr)
                                <a href="{{ route('procurement.grr.show', $grr) }}" class="group bg-white dark:bg-gray-800 rounded-[2rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-2xl transition-all duration-500 relative overflow-hidden">
                                     <div class="absolute top-0 right-0 p-4 opacity-[0.03] group-hover:opacity-[0.07] transition-opacity">
                                        <i data-feather="refresh-cw" class="w-24 h-24 text-orange-600"></i>
                                    </div>
                                    <div class="flex justify-between items-start mb-6">
                                        <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/20 text-orange-600 rounded-xl flex items-center justify-center">
                                            <i data-feather="alert-triangle" class="w-5 h-5"></i>
                                        </div>
                                        <span class="text-[9px] font-black text-gray-400 uppercase">{{ $grr->created_at->format('M d') }}</span>
                                    </div>
                                    <h4 class="text-base font-black text-gray-900 dark:text-white mb-1 uppercase tracking-tight">Return Claim #{{ $grr->grr_number }}</h4>
                                    <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-6">{{ ucfirst($grr->issue_type) }} • {{ $grr->createdBy->name }}</p>
                                    <div class="flex items-end justify-between pt-6 border-t border-gray-50 dark:border-gray-800">
                                        <span class="text-[10px] font-black uppercase text-orange-600 tracking-widest">{{ str_replace('_', ' ', $grr->resolution_status) }}</span>
                                        <span class="text-[10px] font-black uppercase text-primary-600 tracking-widest group-hover:translate-x-1 transition-transform inline-flex items-center gap-2">Manage Claim <i data-feather="arrow-right" class="w-4 h-4"></i></span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

            @endif
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
