@extends('layouts.app', [
    'title' => 'Contract: ' . $contract->contract_number,
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => url('/')],
        ['name' => 'Sales Contracts', 'url' => route('procurement.contracts.index')],
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
            <p class="text-gray-500 font-medium italic">Annual Sales Master Agreement</p>
        </div>
        
        @if($contract->status === 'proposed')
            <div class="flex items-center gap-4">
                <form action="{{ route('procurement.contracts.vendor-sign', $contract) }}" method="POST" onsubmit="return confirm('Sign this annual contract and commit to these prices for one year?')">
                    @csrf
                    <button type="submit" class="h-16 px-10 flex items-center bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all active:scale-[0.98]">
                        <i data-feather="edit-3" class="w-4 h-4 mr-2"></i>
                        Sign & Commit Contract
                    </button>
                </form>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Contract Insights --}}
        <div class="space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm p-10 space-y-8">
                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Buyer Entity</label>
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-gray-50 dark:bg-gray-900 rounded-xl flex items-center justify-center text-gray-400">
                            <i data-feather="user" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ $contract->buyer->name ?? 'Unknown Buyer' }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase">Valued Client</p>
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

                {{-- Signing Details --}}
                @if($contract->vendor_signed_at)
                    <div class="pt-8 border-t border-gray-50 dark:border-gray-800/50">
                        <label class="block text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em] mb-4">Digitally Signed</label>
                        <div class="p-4 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl border border-emerald-100 dark:border-emerald-800/20">
                            <p class="text-[10px] font-black text-emerald-700 dark:text-emerald-400 uppercase mb-1">Signed By</p>
                            <p class="text-sm font-black text-gray-900 dark:text-white uppercase">{{ $contract->vendorSignedBy->name }}</p>
                            <p class="text-[9px] font-bold text-gray-400 uppercase mt-2">{{ $contract->vendor_signed_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                @endif
            </div>

            <div class="bg-emerald-600 rounded-[2.5rem] p-10 text-white space-y-4 shadow-xl shadow-emerald-600/20">
                <i data-feather="check-circle" class="w-10 h-10 mb-4 opacity-50"></i>
                <h4 class="text-xl font-black uppercase tracking-tight">Price Commitment</h4>
                <p class="text-emerald-100 text-xs leading-relaxed font-medium">By signing this agreement, you guarantee to provide the items listed at the negotiated fixed prices for the entire duration of the contract term.</p>
            </div>
        </div>

        {{-- Fixed Matrix Content --}}
        <div class="lg:col-span-2 space-y-8">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4">Committed Item Matrix</h3>
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Catalogue Item</th>
                            <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Committed Price</th>
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
                                    <p class="text-sm font-black text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $item->formatted_price }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">LOCKED PRICE</p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($contract->status === 'active')
                <div class="pt-12">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-4">Order History</h3>
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
                                            <a href="{{ route('procurement.po.show', $req->purchaseOrder) }}" class="text-sm font-black text-gray-900 dark:text-white uppercase group-hover:text-emerald-600 transition-colors">
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
                                            No orders received under this contract yet
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
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
