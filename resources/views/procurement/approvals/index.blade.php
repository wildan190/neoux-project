@extends('layouts.app', [
    'title' => 'Quick Action Approvals',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Quick Approvals', 'url' => null],
    ]
])

@section('content')
    <div x-data="{ activeTab: 'all' }" class="space-y-8">
        {{-- Header & Stats --}}
        <div>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Quick Action Approvals</h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2 text-lg">Centralized dashboard for all items awaiting your action.</p>
                </div>
                <div class="flex items-center gap-3 bg-white dark:bg-gray-800 p-2 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <span class="text-xs font-bold text-gray-500 uppercase tracking-wider px-3">Total Pending</span>
                    <span class="px-3 py-1 bg-red-500 text-white text-sm font-black rounded-lg shadow-lg shadow-red-500/30">
                        {{ $pendingPRs->count() + $pendingPOs->count() + $pendingInvoices->count() + $vendorHeadInvoices->count() + $pendingDebitNotes->count() + $pendingGRRs->count() }}
                    </span>
                </div>
            </div>

            {{-- Summary Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
                {{-- PRs --}}
                <button @click="activeTab = 'prs'" 
                    class="group relative bg-white dark:bg-gray-800 p-6 rounded-3xl border transition-all duration-300 text-left hover:shadow-xl hover:-translate-y-1"
                    :class="activeTab === 'prs' ? 'border-primary-500 ring-2 ring-primary-500/20 shadow-lg shadow-primary-500/10' : 'border-gray-100 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800'">
                    <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-primary-600 dark:text-primary-400 mb-4 group-hover:scale-110 transition-transform">
                        <i data-feather="file-text" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Requisitions</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums">{{ $pendingPRs->count() }}</p>
                    @if($pendingPRs->count() > 0)
                        <div class="absolute top-6 right-6 w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                    @endif
                </button>

                {{-- POs --}}
                <button @click="activeTab = 'pos'" 
                    class="group relative bg-white dark:bg-gray-800 p-6 rounded-3xl border transition-all duration-300 text-left hover:shadow-xl hover:-translate-y-1"
                    :class="activeTab === 'pos' ? 'border-emerald-500 ring-2 ring-emerald-500/20 shadow-lg shadow-emerald-500/10' : 'border-gray-100 dark:border-gray-700 hover:border-emerald-200 dark:hover:border-emerald-800'">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center text-emerald-600 dark:text-emerald-400 mb-4 group-hover:scale-110 transition-transform">
                        <i data-feather="shopping-bag" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Purchase Orders</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums">{{ $pendingPOs->count() }}</p>
                    @if($pendingPOs->count() > 0)
                        <div class="absolute top-6 right-6 w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                    @endif
                </button>

                {{-- Invoices --}}
                <button @click="activeTab = 'invoices'" 
                    class="group relative bg-white dark:bg-gray-800 p-6 rounded-3xl border transition-all duration-300 text-left hover:shadow-xl hover:-translate-y-1"
                    :class="activeTab === 'invoices' ? 'border-blue-500 ring-2 ring-blue-500/20 shadow-lg shadow-blue-500/10' : 'border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800'">
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600 dark:text-blue-400 mb-4 group-hover:scale-110 transition-transform">
                        <i data-feather="credit-card" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Invoices</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums">{{ $pendingInvoices->count() + $vendorHeadInvoices->count() }}</p>
                    @if(($pendingInvoices->count() + $vendorHeadInvoices->count()) > 0)
                        <div class="absolute top-6 right-6 w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                    @endif
                </button>

                {{-- Others --}}
                <button @click="activeTab = 'others'" 
                    class="group relative bg-white dark:bg-gray-800 p-6 rounded-3xl border transition-all duration-300 text-left hover:shadow-xl hover:-translate-y-1"
                    :class="activeTab === 'others' ? 'border-orange-500 ring-2 ring-orange-500/20 shadow-lg shadow-orange-500/10' : 'border-gray-100 dark:border-gray-700 hover:border-orange-200 dark:hover:border-orange-800'">
                    <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400 mb-4 group-hover:scale-110 transition-transform">
                        <i data-feather="inbox" class="w-6 h-6"></i>
                    </div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Returns / Debits</p>
                    <p class="text-3xl font-black text-gray-900 dark:text-white tabular-nums">{{ $pendingDebitNotes->count() + $pendingGRRs->count() }}</p>
                    @if(($pendingDebitNotes->count() + $pendingGRRs->count()) > 0)
                        <div class="absolute top-6 right-6 w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                    @endif
                </button>
            </div>
        </div>

        {{-- Filters / Tabs Navigation --}}
        <div class="border-b border-gray-200 dark:border-gray-700 overflow-x-auto">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <button @click="activeTab = 'all'" 
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-bold text-sm transition-colors"
                    :class="activeTab === 'all' ? 'border-gray-900 text-gray-900 dark:text-white dark:border-white' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                    All Items
                </button>
                <button @click="activeTab = 'prs'" 
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    :class="activeTab === 'prs' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                    Requisitions
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $pendingPRs->count() }}</span>
                </button>
                <button @click="activeTab = 'pos'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    :class="activeTab === 'pos' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                    Purchase Orders
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $pendingPOs->count() }}</span>
                </button>
                <button @click="activeTab = 'invoices'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    :class="activeTab === 'invoices' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                    Invoices
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $pendingInvoices->count() + $vendorHeadInvoices->count() }}</span>
                </button>
                <button @click="activeTab = 'others'"
                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                    :class="activeTab === 'others' ? 'border-orange-500 text-orange-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                    Returns & Adjustments
                    <span class="ml-2 py-0.5 px-2 rounded-full text-xs bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400">{{ $pendingDebitNotes->count() + $pendingGRRs->count() }}</span>
                </button>
            </nav>
        </div>

        {{-- Tab Content --}}
        <div>
            @php
                $allItemsEmpty = $pendingPRs->isEmpty() && $pendingPOs->isEmpty() && $pendingInvoices->isEmpty() && $vendorHeadInvoices->isEmpty() && $pendingDebitNotes->isEmpty() && $pendingGRRs->isEmpty();
            @endphp
            
            {{-- Empty State --}}
            @if($allItemsEmpty)
                <div class="bg-white dark:bg-gray-800 rounded-3xl p-16 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
                    <div class="w-24 h-24 bg-green-50 dark:bg-green-900/20 rounded-full flex items-center justify-center mx-auto mb-6 ring-8 ring-green-50/50 dark:ring-green-900/10">
                        <i data-feather="check" class="w-10 h-10 text-green-500"></i>
                    </div>
                    <h3 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">All Caught Up!</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-2 max-w-sm mx-auto text-lg leading-relaxed">No pending items requiring your attention. Grab a coffee! ☕</p>
                </div>
            @else

                {{-- PRs Section --}}
                <div x-show="activeTab === 'all' || activeTab === 'prs'" class="mb-12" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    @if($pendingPRs->isNotEmpty())
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <i data-feather="file-text" class="w-4 h-4"></i>
                                Pending Requisitions
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($pendingPRs as $pr)
                                <a href="{{ route('procurement.pr.show', $pr) }}" class="group bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-primary-200 dark:hover:border-primary-800 transition-all duration-300 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                        <i data-feather="file-text" class="w-24 h-24 text-primary-500"></i>
                                    </div>
                                    <div class="relative">
                                        <div class="flex justify-between items-start mb-4">
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 text-[10px] font-black rounded-lg uppercase tracking-wider">
                                                {{ $pr->tender_status === 'pending_winner_approval' ? 'Winner Approval' : str_replace('_', ' ', $pr->approval_status) }}
                                            </span>
                                            <span class="text-xs font-bold text-gray-400">{{ $pr->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-1 line-clamp-1 group-hover:text-primary-600 transition-colors">{{ $pr->title }}</h4>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-6">{{ $pr->pr_number }}</p>
                                        
                                        <div class="flex items-center justify-between pt-4 border-t border-gray-50 dark:border-gray-700">
                                            <div class="flex items-center gap-2">
                                                <div class="w-6 h-6 rounded-full bg-primary-100 flex items-center justify-center text-[10px] font-black text-primary-700">
                                                    {{ substr($pr->user->name, 0, 1) }}
                                                </div>
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $pr->user->name }}</span>
                                            </div>
                                            <span class="text-xs font-bold text-primary-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                                Review <i data-feather="arrow-right" class="w-3 h-3"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    @if($pendingPRs->isEmpty())
                        <div x-show="activeTab === 'prs'" class="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                            <p class="text-gray-400 font-medium">No pending requisitions.</p>
                        </div>
                    @endif
                </div>

                {{-- POs Section --}}
                <div x-show="activeTab === 'all' || activeTab === 'pos'" class="mb-12" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    @if($pendingPOs->isNotEmpty())
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <i data-feather="shopping-bag" class="w-4 h-4"></i>
                                Needs Acceptance (Vendor)
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($pendingPOs as $po)
                                <a href="{{ route('procurement.po.show', $po) }}" class="group bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-emerald-200 dark:hover:border-emerald-800 transition-all duration-300 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                                        <i data-feather="truck" class="w-24 h-24 text-emerald-500"></i>
                                    </div>
                                    <div class="relative">
                                        <div class="flex justify-between items-start mb-4">
                                            <span class="px-3 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-[10px] font-black rounded-lg uppercase tracking-wider">
                                                New Order
                                            </span>
                                            <span class="text-xs font-bold text-gray-400">{{ $po->created_at->diffForHumans() }}</span>
                                        </div>
                                        <h4 class="text-lg font-black text-gray-900 dark:text-white mb-1 group-hover:text-emerald-600 transition-colors">{{ $po->formatted_total_amount }}</h4>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-6">PO #{{ $po->po_number }}</p>
                                        
                                        <div class="flex items-center justify-between pt-4 border-t border-gray-50 dark:border-gray-700">
                                            <div class="flex items-center gap-2">
                                                <i data-feather="briefcase" class="w-3 h-3 text-gray-400"></i>
                                                <span class="text-xs font-medium text-gray-600 dark:text-gray-400 line-clamp-1">{{ $po->buyerCompany?->name ?? 'N/A' }}</span>
                                            </div>
                                            <span class="text-xs font-bold text-emerald-600 flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                                                Accept <i data-feather="arrow-right" class="w-3 h-3"></i>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($pendingPOs->isEmpty())
                        <div x-show="activeTab === 'pos'" class="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                            <p class="text-gray-400 font-medium">No pending purchase orders.</p>
                        </div>
                    @endif
                </div>

                {{-- Invoices Section --}}
                <div x-show="activeTab === 'all' || activeTab === 'invoices'" class="mb-12" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    @if($pendingInvoices->isNotEmpty() || $vendorHeadInvoices->isNotEmpty())
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <i data-feather="credit-card" class="w-4 h-4"></i>
                                Invoice Approvals
                            </h3>
                        </div>
                        <div class="space-y-4">
                            {{-- Merge collections for unified view or keep separate if distinct flows --}}
                            @foreach($pendingInvoices->merge($vendorHeadInvoices) as $invoice)
                                <a href="{{ route('procurement.invoices.show', $invoice) }}" class="group block bg-white dark:bg-gray-800 rounded-2xl p-4 border border-gray-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-lg transition-all duration-300">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center text-blue-600">
                                                <i data-feather="file" class="w-5 h-5"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-bold text-gray-900 dark:text-white">{{ $invoice->invoice_number }}</h4>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-[10px] font-black uppercase text-gray-400">PO: {{ $invoice->purchaseOrder->po_number }}</span>
                                                    <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                                                    <span class="text-[10px] font-medium text-gray-500">{{ $invoice->created_at->format('d M') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-6">
                                            <div class="text-right">
                                                <p class="text-sm font-black text-gray-900 dark:text-white">{{ $invoice->formatted_total_amount }}</p>
                                                <span class="text-[9px] font-bold uppercase text-blue-600 tracking-wider">
                                                    {{ str_replace('_', ' ', $invoice->status) }}
                                                </span>
                                            </div>
                                            <div class="w-8 h-8 rounded-full bg-gray-50 dark:bg-gray-700 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                                <i data-feather="chevron-right" class="w-4 h-4"></i>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($pendingInvoices->isEmpty() && $vendorHeadInvoices->isEmpty())
                        <div x-show="activeTab === 'invoices'" class="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                            <p class="text-gray-400 font-medium">No pending invoices.</p>
                        </div>
                    @endif
                </div>

                {{-- Others Section --}}
                <div x-show="activeTab === 'all' || activeTab === 'others'" class="mb-12" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">
                    @if($pendingDebitNotes->isNotEmpty() || $pendingGRRs->isNotEmpty())
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-sm font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                                <i data-feather="inbox" class="w-4 h-4"></i>
                                Returns & Adjustments
                            </h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach($pendingDebitNotes as $dn)
                                <a href="{{ route('procurement.debit-notes.show', $dn) }}" class="group bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 hover:border-red-300 transition-all hover:shadow-lg">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="p-2 bg-red-50 dark:bg-red-900/20 rounded-lg text-red-600">
                                            <i data-feather="minus-circle" class="w-5 h-5"></i>
                                        </div>
                                        <span class="text-xs font-bold text-gray-400">{{ $dn->created_at->format('d M') }}</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Debit Note #{{ $dn->dn_number }}</h4>
                                    <p class="text-xs text-gray-500 mb-4 line-clamp-2">PO Adjustment from {{ $dn->purchaseOrder->vendorCompany->name }}</p>
                                    <div class="flex items-end justify-between">
                                        <span class="text-lg font-black text-red-600">-{{ $dn->formatted_deduction_amount }}</span>
                                        <span class="text-[10px] font-black uppercase bg-gray-100 px-2 py-1 rounded text-gray-600">Review</span>
                                    </div>
                                </a>
                            @endforeach

                            @foreach($pendingGRRs as $grr)
                                <a href="{{ route('procurement.grr.show', $grr) }}" class="group bg-white dark:bg-gray-800 rounded-3xl p-6 border border-gray-100 dark:border-gray-700 hover:border-orange-300 transition-all hover:shadow-lg">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="p-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg text-orange-600">
                                            <i data-feather="refresh-cw" class="w-5 h-5"></i>
                                        </div>
                                        <span class="text-xs font-bold text-gray-400">{{ $grr->created_at->format('d M') }}</span>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-1">Return Request #{{ $grr->grr_number }}</h4>
                                    <p class="text-xs text-gray-500 mb-4">{{ ucfirst($grr->issue_type) }} - {{ $grr->creator->name }}</p>
                                    <div class="flex items-end justify-between">
                                        <span class="text-[10px] font-black uppercase text-orange-600 tracking-wider">{{ str_replace('_', ' ', $grr->resolution_status) }}</span>
                                        <span class="text-[10px] font-black uppercase bg-gray-100 px-2 py-1 rounded text-gray-600">Resolve</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    @if($pendingDebitNotes->isEmpty() && $pendingGRRs->isEmpty())
                        <div x-show="activeTab === 'others'" class="text-center py-12 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                            <p class="text-gray-400 font-medium">No pending adjustments.</p>
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
