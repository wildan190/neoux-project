@extends('layouts.app', [
    'title' => 'Offer Details',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => $purchaseRequisition->title, 'url' => route('procurement.offers.index', $purchaseRequisition)],
        ['name' => 'Offer Details', 'url' => null],
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">PROPOSAL REVIEW</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $offer->company->name }}</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Technical & Financial Analysis</h1>
        </div>
        <div class="flex items-center gap-2">
            @if($purchaseRequisition->po_generated_at)
                <a href="{{ route('procurement.po.show', $purchaseRequisition->purchaseOrder) }}" 
                    class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
                    View Purchase Order
                </a>
            @elseif($offer->status === 'accepted')
                <form action="{{ route('procurement.po.generate', $purchaseRequisition) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Generate Purchase Order?')"
                        class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all">
                        Generate PO
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main Analysis Area --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Offer Header --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 p-10 shadow-sm relative overflow-hidden group">
                @if($offer->is_recommended)
                    <div class="absolute top-0 right-0 px-6 py-2 bg-primary-600 text-white text-[9px] font-black uppercase tracking-widest rounded-bl-3xl">AI TOP CHOICE</div>
                @endif

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-8 mb-10">
                    <div class="flex items-center gap-6">
                        <div class="w-20 h-20 rounded-3xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-2xl font-black text-gray-900 dark:text-white shadow-inner">
                            #{{ $offer->rank_position }}
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $offer->company->name }}</h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Submitted by {{ $offer->user->name }} • {{ $offer->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Financial Proposal</p>
                        <p class="text-3xl font-black text-primary-600 dark:text-primary-400">{{ $offer->formatted_total_price }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-10 border-t border-gray-50 dark:border-gray-700/50">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Delivery Time</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $offer->delivery_time ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Warranty Terms</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $offer->warranty ?? 'N/A' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Payment Scheme</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase">{{ $offer->payment_scheme ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                <div class="p-8 border-b border-gray-50 dark:border-gray-700/50">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Offered Items Breakdown</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-700/50">
                        <thead>
                            <tr>
                                <th class="px-8 py-4 text-left text-[9px] font-black text-gray-400 uppercase tracking-widest">Product Description</th>
                                <th class="px-8 py-4 text-center text-[9px] font-black text-gray-400 uppercase tracking-widest">Qty</th>
                                <th class="px-8 py-4 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">Unit Price</th>
                                <th class="px-8 py-4 text-right text-[9px] font-black text-gray-400 uppercase tracking-widest">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                            @foreach($offer->items as $item)
                                <tr>
                                    <td class="px-8 py-6">
                                        <div class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $item->purchaseRequisitionItem->catalogueItem->name }}</div>
                                        <div class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">SKU: {{ $item->purchaseRequisitionItem->catalogueItem->sku }}</div>
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
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Offer Status</h3>
                
                <div class="space-y-4">
                    <div class="p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl text-center border border-gray-100 dark:border-gray-700">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Current State</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ str_replace('_', ' ', $offer->status) }}</p>
                    </div>

                    @php
                        $canApprove = Auth::user()->hasCompanyPermission($purchaseRequisition->company_id, 'approve pr');
                    @endphp

                    @if($canApprove && in_array($offer->status, ['pending', 'negotiating']))
                        <form action="{{ route('procurement.offers.accept', $offer) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Award tender to {{ $offer->company->name }}?')"
                                class="w-full py-4 bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all">
                                Select as Winner
                            </button>
                        </form>
                        
                        <button type="button" onclick="document.getElementById('negotiateModal').classList.remove('hidden')"
                            class="w-full py-4 bg-white dark:bg-gray-900 text-gray-900 dark:text-white border border-gray-100 dark:border-gray-700 text-[11px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                            Propose Negotiation
                        </button>

                        <form action="{{ route('procurement.offers.reject', $offer) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Reject this offer?')"
                                class="w-full py-3 bg-red-50 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-100 transition-all mt-4">
                                Reject Offer
                            </button>
                        </form>
                    @endif

                    @if($canApprove && $offer->status === 'winning')
                        <form action="{{ route('procurement.offers.approve-winner', $offer) }}" method="POST">
                            @csrf
                            <button type="submit" onclick="return confirm('Final approval for {{ $offer->company->name }}?')"
                                class="w-full py-4 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
                                Approve Winner
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($offer->documents->count() > 0)
                <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Technical Documents</h3>
                    <div class="space-y-3">
                        @foreach($offer->documents as $doc)
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                                class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-primary-300 transition-all group">
                                <div class="flex items-center gap-3">
                                    <i data-feather="file-text" class="w-4 h-4 text-gray-400 group-hover:text-primary-600"></i>
                                    <div>
                                        <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase truncate max-w-[120px]">{{ $doc->file_name }}</p>
                                        <p class="text-[8px] font-bold text-gray-400 uppercase">{{ $doc->formatted_file_size }}</p>
                                    </div>
                                </div>
                                <i data-feather="download" class="w-3.5 h-3.5 text-gray-300"></i>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Negotiate Modal --}}
    <div id="negotiateModal" class="hidden fixed inset-0 z-[100] overflow-auto backdrop-blur-md bg-gray-900/30 flex items-center justify-center p-6">
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] shadow-2xl w-full max-w-2xl relative border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="p-8 border-b border-gray-50 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-700/30 flex justify-between items-center">
                <h3 class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Propose Revised Terms</h3>
                <button type="button" onclick="document.getElementById('negotiateModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-feather="x" class="w-5 h-5"></i>
                </button>
            </div>
            
            <form action="{{ route('procurement.offers.submit-negotiation', $offer) }}" method="POST" id="negotiationForm" class="p-8 space-y-8">
                @csrf
                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Negotiation Rationale</label>
                    <textarea name="negotiation_message" rows="3" 
                        class="w-full p-5 rounded-xl border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-[11px] font-bold uppercase tracking-tight text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all placeholder-gray-400 dark:placeholder-gray-500" 
                        placeholder="Explain the required pricing or term adjustments..."></textarea>
                </div>

                <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl overflow-hidden shadow-inner">
                    <table class="w-full text-xs">
                        <thead>
                            <tr class="bg-gray-100 dark:bg-gray-800">
                                <th class="px-5 py-3 text-left text-[9px] font-black text-gray-500 uppercase tracking-widest">Item</th>
                                <th class="px-5 py-3 text-center text-[9px] font-black text-gray-500 uppercase tracking-widest">Qty</th>
                                <th class="px-5 py-3 text-right text-[9px] font-black text-gray-500 uppercase tracking-widest">Bid Price</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700/50">
                            @foreach($offer->items as $item)
                                <tr>
                                    <td class="px-5 py-4 text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight">
                                        {{ $item->purchaseRequisitionItem->catalogueItem->name }}
                                        <input type="hidden" name="items[{{ $loop->index }}][id]" value="{{ $item->id }}">
                                    </td>
                                    <td class="px-5 py-4">
                                        <input type="number" name="items[{{ $loop->index }}][quantity_offered]" value="{{ $item->quantity_offered }}" min="1" required 
                                            class="w-full px-3 py-2 text-center rounded-lg border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 text-[10px] font-black text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none placeholder-gray-400 dark:placeholder-gray-500 item-quantity" data-index="{{ $loop->index }}">
                                    </td>
                                    <td class="px-5 py-4">
                                        <input type="number" name="items[{{ $loop->index }}][unit_price]" value="{{ $item->unit_price }}" step="0.01" min="0" required 
                                            class="w-full px-3 py-2 text-right rounded-lg border border-gray-100 dark:border-gray-700 bg-white dark:bg-gray-800 text-[10px] font-black text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none placeholder-gray-400 dark:placeholder-gray-500 item-unit-price" data-index="{{ $loop->index }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Delivery Time</label>
                        <input type="text" name="delivery_time" value="{{ $offer->delivery_time }}" required 
                            class="w-full p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-[11px] font-black uppercase tracking-tight text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none placeholder-gray-400 dark:placeholder-gray-500">
                    </div>
                    <div class="space-y-2">
                        <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Warranty</label>
                        <input type="text" name="warranty" value="{{ $offer->warranty }}" required 
                            class="w-full p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-[11px] font-black uppercase tracking-tight text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none placeholder-gray-400 dark:placeholder-gray-500">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Payment Scheme</label>
                    <input type="text" name="payment_scheme" value="{{ $offer->payment_scheme }}" required 
                        class="w-full p-4 rounded-lg border border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 text-[11px] font-black uppercase tracking-tight text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none placeholder-gray-400 dark:placeholder-gray-500"
                        placeholder="e.g. 50% Upfront, 50% After Delivery">
                </div>

                <div class="flex gap-4 pt-4">
                    <button type="button" onclick="document.getElementById('negotiateModal').classList.add('hidden')" class="flex-1 py-4 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-gray-200 transition-all">Cancel</button>
                    <button type="submit" class="flex-1 py-4 bg-primary-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all">Send Proposal</button>
                </div>
            </form>
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
