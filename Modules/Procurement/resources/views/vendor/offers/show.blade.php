@extends('layouts.app', [
    'title' => 'Offer Details',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'My Offers', 'url' => route('procurement.offers.my')],
        ['name' => 'Offer Details', 'url' => null],
    ]
])

@section('content')
    {{-- Premium Loading Screen --}}
    <div id="page-loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-white dark:bg-gray-900 transition-opacity duration-700">
        <div class="flex flex-col items-center">
            <div class="relative w-24 h-24">
                <div class="absolute inset-0 border-4 border-primary-100 dark:border-primary-900/30 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-primary-600 rounded-full border-t-transparent animate-spin"></div>
                <div class="absolute inset-4 bg-gradient-to-tr from-primary-600 to-secondary-500 rounded-full opacity-20 animate-pulse"></div>
            </div>
            <div class="mt-8 text-center">
                <h2 class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-[0.3em] animate-pulse">Retrieving Proposal</h2>
                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-2">Syncing Quotation Data</p>
            </div>
        </div>
    </div>

    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">SALES REP</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">SUBMITTED PROPOSAL</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $purchaseRequisition->title }}</h1>
        </div>
        <div class="flex items-center gap-2">
            @if($offer->purchaseOrder)
                <a href="{{ route('procurement.po.show', $offer->purchaseOrder) }}" 
                    class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
                    View Purchase Order
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Proposal Area --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Offer Header --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 p-10 shadow-sm relative overflow-hidden group">
                @if($offer->status === 'accepted')
                    <div class="absolute top-0 right-0 px-6 py-2 bg-emerald-600 text-white text-[9px] font-black uppercase tracking-widest rounded-bl-3xl">TENDER WON</div>
                @endif

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-10">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-3xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-2xl font-black text-gray-900 dark:text-white shadow-inner">
                            #{{ $offer->rank_position }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Bid Summary</h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">FOR {{ $purchaseRequisition->company->name }} • {{ $offer->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Financial Proposal</p>
                        <p class="text-3xl font-black text-primary-600 dark:text-primary-400">{{ $offer->formatted_total_price }}</p>
                    </div>
                </div>

                {{-- Negotiation Message Notice --}}
                @if($offer->status === 'negotiating' && $offer->negotiation_message)
                    <div class="mb-10 p-6 bg-indigo-50 dark:bg-indigo-900/20 rounded-3xl border border-indigo-100 dark:border-indigo-800">
                        <div class="flex items-start gap-4">
                            <i data-feather="message-circle" class="w-6 h-6 text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-1"></i>
                            <div>
                                <h4 class="text-[10px] font-black text-indigo-900 dark:text-indigo-200 uppercase tracking-widest mb-2">Message from Buyer</h4>
                                <p class="text-[11px] font-bold text-indigo-700 dark:text-indigo-300 uppercase leading-relaxed font-mono">"{{ $offer->negotiation_message }}"</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-10 border-t border-gray-50 dark:border-gray-700/50">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Promised Delivery</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $offer->delivery_time ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Warranty Claim</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $offer->warranty ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Payment Terms</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $offer->payment_scheme ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                <div class="p-8 border-b border-gray-50 dark:border-gray-700/50">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Quotation Breakdown</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-700/50">
                        <thead>
                            <tr>
                                <th class="px-8 py-4 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">Product Details</th>
                                <th class="px-8 py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">Bid Qty</th>
                                <th class="px-8 py-4 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">Proposed Rate</th>
                                <th class="px-8 py-4 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach($offer->items as $item)
                                <tr>
                                    <td class="px-8 py-6">
                                        <div class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $item->purchaseRequisitionItem->catalogueItem->name }}</div>
                                        <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">CLIENT SKU: {{ $item->purchaseRequisitionItem->catalogueItem->sku }}</div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        <span class="text-[11px] font-black text-gray-900 dark:text-white">{{ $item->quantity_offered }}</span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <span class="text-[11px] font-bold text-gray-600 dark:text-gray-400">{{ $item->formatted_unit_price }}</span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <span class="text-[11px] font-black text-gray-900 dark:text-white">{{ $item->formatted_subtotal }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-900/10">
                            <tr>
                                <td colspan="3" class="px-8 py-6 text-right text-[10px] font-black text-gray-400 uppercase tracking-widest">GRAND TOTAL</td>
                                <td class="px-8 py-6 text-right text-lg font-black text-primary-600 tracking-tight">{{ $offer->formatted_total_price }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        {{-- Sidebar Actions --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Pipeline Status</h3>
                
                <div class="space-y-4">
                    <div class="p-6 bg-gray-50 dark:bg-gray-900 rounded-3xl text-center border border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Workflow Stage</p>
                        <p class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ str_replace('_', ' ', $offer->status) }}</p>
                    </div>

                    @if($offer->status === 'negotiating')
                        <form id="acceptNegotiationForm" action="{{ route('procurement.offers.vendor-accept-negotiation', $offer) }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmAction('acceptNegotiationForm', 'Accept Revised Terms?', 'Are you sure you want to accept the new terms proposed by the buyer?', 'success', 'Yes, Accept')"
                                class="w-full py-4 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition">
                                Accept Revised Terms
                            </button>
                        </form>
                        
                        <form id="rejectNegotiationForm" action="{{ route('procurement.offers.vendor-reject-negotiation', $offer) }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmAction('rejectNegotiationForm', 'Reject Terms?', 'This will likely lead to bid disqualification. Are you sure you want to withdraw?', 'warning', 'Yes, Reject')"
                                class="w-full py-3 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-100 transition mt-2">
                                Reject & Withdraw
                            </button>
                        </form>
                    @endif

                    <div class="p-6 bg-gray-900 text-white rounded-3xl shadow-xl shadow-gray-900/10">
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Competitive Rank</h4>
                        <div class="flex items-end justify-between">
                            <span class="text-3xl font-black">#{{ $offer->rank_position }}</span>
                            <span class="text-[11px] font-bold text-primary-400 uppercase tracking-widest">{{ $offer->rank_score }} POINTS</span>
                        </div>
                    </div>
                </div>
            </div>

            @if($offer->documents->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Submitted Docs</h3>
                    <div class="space-y-3">
                        @foreach($offer->documents as $doc)
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-primary-300 transition-all group">
                                <div class="flex items-center gap-3">
                                    <i data-feather="file" class="w-4 h-4 text-gray-400 group-hover:text-primary-600"></i>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase truncate max-w-[120px]">{{ $doc->file_name }}</p>
                                        <p class="text-[8px] font-bold text-gray-400 uppercase">{{ strtoupper(pathinfo($doc->file_name, PATHINFO_EXTENSION)) }}</p>
                                    </div>
                                </div>
                                <i data-feather="external-link" class="w-3.5 h-3.5 text-gray-300"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();

            // Handle page loader
            const loader = document.getElementById('page-loader');
            if (loader) {
                setTimeout(() => {
                    loader.classList.add('opacity-0', 'pointer-events-none');
                    setTimeout(() => loader.remove(), 700);
                }, 500); // Small delay for visual impact
            }
        });

        window.confirmAction = function(formId, title, text, icon, confirmText) {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                showCancelButton: true,
                confirmButtonColor: icon === 'success' ? '#10b981' : '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: confirmText,
                cancelButtonText: 'Cancel',
                background: document.documentElement.classList.contains('dark') ? '#1f2937' : '#fff',
                color: document.documentElement.classList.contains('dark') ? '#fff' : '#000',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
@endpush
