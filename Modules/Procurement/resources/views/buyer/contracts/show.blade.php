@extends('layouts.app', [
    'title' => 'Contract: ' . $contract->contract_number,
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => url('/')],
        ['name' => 'Contracts', 'url' => route('procurement.contracts.index')],
        ['name' => $contract->contract_number, 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-{{ $contract->status_color }}-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $contract->status }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">VALID UNTIL {{ $contract->end_date->format('M d, Y') }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-3">
                {{ $contract->title }}
            </h1>
            <p class="text-gray-500 font-medium italic">Fixed Pricing Master Agreement</p>
        </div>
        
        <div class="flex items-center gap-4">
            @if($contract->status === 'draft')
                <form action="{{ route('procurement.contracts.propose', $contract) }}" method="POST">
                    @csrf
                    <button type="submit" class="h-16 px-10 flex items-center bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-[0.98]">
                        <i data-feather="send" class="w-4 h-4 mr-2"></i>
                        Propose to Vendor
                    </button>
                </form>
            @elseif($contract->status === 'signed')
                <div class="flex items-center gap-4">
                    <form action="{{ route('procurement.contracts.reject', $contract) }}" method="POST">
                        @csrf
                        <input type="hidden" name="reason" value="Sent back for revision">
                        <button type="submit" class="h-16 px-8 flex items-center bg-white border border-gray-200 text-gray-500 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                            <i data-feather="x-circle" class="w-4 h-4 mr-2"></i>
                            Reject
                        </button>
                    </form>
                    <form action="{{ route('procurement.contracts.approve', $contract) }}" method="POST">
                        @csrf
                        <button type="submit" class="h-16 px-10 flex items-center bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all active:scale-[0.98]">
                            <i data-feather="check-circle" class="w-4 h-4 mr-2"></i>
                            Approve & Activate
                        </button>
                    </form>
                </div>
            @elseif($contract->status === 'active')
                <form action="{{ route('procurement.contracts.repeat-order', $contract) }}" method="POST" onsubmit="return confirm('Initiate a repeat order based on this contract?')">
                    @csrf
                    <button type="submit" class="h-16 px-10 flex items-center bg-indigo-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition-all active:scale-[0.98]">
                        <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                        Initiate Repeat Order
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Rejection Message if Draft --}}
    @if($contract->status === 'draft' && $contract->rejection_reason)
        <div class="p-6 bg-red-50 dark:bg-red-900/10 border border-red-100 dark:border-red-900/20 rounded-[2rem]">
            <div class="flex items-start gap-4">
                <i data-feather="alert-circle" class="w-5 h-5 text-red-500 mt-1"></i>
                <div>
                    <h5 class="text-xs font-black text-red-600 uppercase tracking-widest mb-1">Contract Sent Back</h5>
                    <p class="text-sm text-red-400 font-medium italic">"{{ $contract->rejection_reason }}"</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Contract Insights --}}
        <div class="space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm p-10 space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Vendor Entity</label>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-gray-400">
                            <i data-feather="briefcase" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ $contract->vendor->name ?? 'Unknown Vendor' }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Registered Partner</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6 pt-8 border-t border-gray-50 dark:border-gray-800/50">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Effective From</label>
                        <p class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ $contract->start_date->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-2">Term Ends</label>
                        <p class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ $contract->end_date->format('M d, Y') }}</p>
                    </div>
                </div>

                @if($contract->sourcePo)
                    <div class="pt-8 border-t border-gray-50 dark:border-gray-800/50">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Baseline Negotiation</label>
                        <a href="{{ route('procurement.po.show', $contract->sourcePo) }}" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl hover:bg-gray-100 transition-colors">
                            <span class="text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase">{{ $contract->sourcePo->po_number }}</span>
                            <i data-feather="external-link" class="w-3.5 h-3.5 text-gray-400"></i>
                        </a>
                    </div>
                @endif

                {{-- Signing Details --}}
                <div class="space-y-4 pt-8 border-t border-gray-50 dark:border-gray-800/50">
                    @if($contract->vendor_signed_at)
                        <div>
                            <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-3">Vendor Signature</label>
                            <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-800/20">
                                <p class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase mb-1">Signed By</p>
                                <p class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ $contract->vendorSignedBy->name }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase mt-2">{{ $contract->vendor_signed_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    @else
                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3">Vendor Signature</label>
                            <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-dashed border-gray-200 dark:border-gray-800 flex items-center justify-center italic text-[10px] text-gray-400 font-bold uppercase">
                                Awaiting Signature
                            </div>
                        </div>
                    @endif

                    @if($contract->buyer_approved_at)
                        <div>
                            <label class="block text-[10px] font-black text-indigo-600 uppercase tracking-[0.2em] mb-3">Buyer Approval</label>
                            <div class="p-4 bg-indigo-50 dark:bg-indigo-900/10 rounded-2xl border border-indigo-100 dark:border-indigo-800/20">
                                <p class="text-[10px] font-black text-indigo-700 dark:text-indigo-400 uppercase mb-1">Approved By</p>
                                <p class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ $contract->buyerApprovedBy->name }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase mt-2">{{ $contract->buyer_approved_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-indigo-600 rounded-[2.5rem] p-10 text-white space-y-4 shadow-xl shadow-indigo-600/20">
                <i data-feather="shield" class="w-10 h-10 mb-4 opacity-50"></i>
                <h4 class="text-xl font-black uppercase tracking-tight">Annual Guarantee</h4>
                <p class="text-indigo-100 text-xs leading-relaxed font-medium">Pricing listed in this agreement is locked until expiration. Repeat orders will bypass standard bidding protocols.</p>
            </div>
        </div>

        {{-- Fixed Matrix Content --}}
        <div class="lg:col-span-2 space-y-8">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4">Negotiated Item Matrix</h3>
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Catalogue Item</th>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Fixed Contract Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($contract->items as $item)
                            <tr>
                                <td class="px-10 py-8">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 bg-gray-50 dark:bg-gray-900 rounded-2xl flex items-center justify-center text-gray-300">
                                            <i data-feather="package" class="w-5 h-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $item->catalogueItem->name ?? 'Unknown Item' }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $item->catalogueItem->sku ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-8 text-right">
                                    <p class="text-sm font-black text-indigo-600 dark:text-indigo-400 tabular-nums">{{ $item->formatted_price }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">LOCKED PRICE</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- New Ordered History Section --}}
            <div class="pt-12">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4">Utilization History</h3>
                <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Order Number</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Date</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @forelse($contract->relatedRequisitions()->whereHas('purchaseOrder')->with('purchaseOrder')->latest()->get() as $req)
                                <tr class="group hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors">
                                    <td class="px-10 py-6">
                                        <a href="{{ route('procurement.po.show', $req->purchaseOrder) }}" class="text-sm font-black text-gray-900 dark:text-white uppercase group-hover:text-indigo-600 transition-colors">
                                            {{ $req->purchaseOrder->po_number }}
                                        </a>
                                    </td>
                                    <td class="px-10 py-6 text-center">
                                        <span class="text-xs font-bold text-gray-500 uppercase tracking-tighter">{{ $req->created_at->format('d M Y') }}</span>
                                    </td>
                                    <td class="px-10 py-6 text-right">
                                        <span class="text-sm font-black text-gray-900 dark:text-white">{{ $req->purchaseOrder->formatted_total_amount }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-10 py-12 text-center text-gray-300 italic text-xs font-bold uppercase tracking-widest">
                                        No subsequent orders detected yet
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Dangerous Territory --}}
    <div class="pt-20">
        <div class="p-10 bg-red-50/50 dark:bg-red-900/10 rounded-[3rem] border border-red-100 dark:border-red-900/20 flex flex-col md:flex-row md:items-center justify-between gap-8">
            <div>
                <h4 class="text-lg font-black text-red-600 uppercase tracking-tight mb-1">Contract Termination</h4>
                <p class="text-xs text-red-400 font-medium">Archiving this contract will remove fixed pricing guarantees and disable repeat order automation.</p>
            </div>
            <form action="{{ route('procurement.contracts.destroy', $contract) }}" method="POST" onsubmit="return confirm('TERMINATE this annual agreement?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="h-14 px-8 bg-white dark:bg-gray-900 border border-red-200 dark:border-red-900/50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-600 hover:text-white transition-all shadow-sm">
                    Archive Agreement
                </button>
            </form>
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
