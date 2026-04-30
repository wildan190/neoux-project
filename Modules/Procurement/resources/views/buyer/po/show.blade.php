@extends('layouts.app', [
    'title' => 'Purchase Order Detail',
    'breadcrumbs' => [
        ['name' => 'Maps', 'url' => '#'],
        ['name' => 'PO List', 'url' => route('procurement.po.index')],
        ['name' => $purchaseOrder->po_number, 'url' => null],
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $purchaseOrder->po_number }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $purchaseOrder->created_at->format('M d, Y') }}</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Purchase Order</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('procurement.po.index') }}"
                class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 transition-all">
                Close
            </a>
        </div>
    </div>

    <div class="flex flex-wrap gap-3 mb-8">
        <a href="{{ route('procurement.po.print', $purchaseOrder) }}" target="_blank"
            class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-[11px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
            <i data-feather="printer" class="w-3.5 h-3.5 inline mr-2 text-primary-600"></i>
            Print PDF
        </a>

        @if($purchaseOrder->status !== 'pending_vendor_acceptance' && $purchaseOrder->status !== 'rejected_by_vendor')
            <form action="{{ route('procurement.po.repeat-order', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Initiate a repeat order based on this PO?')">
                @csrf
                <button type="submit" 
                        class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-[11px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 transition shadow-sm">
                    <i data-feather="refresh-cw" class="w-3.5 h-3.5 inline mr-2 text-primary-600"></i>
                    Repeat Order
                </button>
            </form>

            <form action="{{ route('procurement.contracts.create-from-order', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Promote this negotiated transaction into an ANNUAL CONTRACT?')">
                @csrf
                <button type="submit" 
                        class="px-6 py-3 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-2xl text-[11px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest hover:bg-black hover:text-white transition shadow-sm">
                    <i data-feather="file-plus" class="w-3.5 h-3.5 inline mr-2 text-indigo-500"></i>
                    Create Annual Contract
                </button>
            </form>
        @endif

        @if(in_array($purchaseOrder->status, ['issued', 'confirmed']) && $purchaseOrder->escrow_status === 'pending')
            <button onclick="document.getElementById('escrowPayModal').classList.remove('hidden')" 
                    class="px-8 py-3 bg-emerald-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition">
                Pay
            </button>
        @endif

        @if(in_array($purchaseOrder->status, ['full_delivery', 'received', 'completed']) && $purchaseOrder->escrow_status === 'paid')
            <form action="{{ route('procurement.po.escrow-release', $purchaseOrder) }}" method="POST" onsubmit="return handlePrFormSubmit(this)">
                @csrf
                <button type="submit" class="px-8 py-3 bg-green-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-green-600/20 hover:bg-green-700 transition">
                    Release Payout
                </button>
            </form>
        @endif

        @php
            $totalOrdered = $purchaseOrder->items->sum('quantity_ordered');
            $totalReceived = $purchaseOrder->items->sum('quantity_received');
            $canReceive = $purchaseOrder->status !== 'pending_vendor_acceptance' && $totalReceived < $totalOrdered;
        @endphp

        @if($canReceive)
            <a href="{{ route('procurement.gr.create', $purchaseOrder) }}" 
                class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition">
                Log Receipt
            </a>
        @endif
    </div>

    {{-- The Map: Progress Stepper --}}
    @include('procurement::partials.po_progress_stepper')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Main PO Document Area --}}
        <div class="lg:col-span-2 space-y-8">
            @include('procurement::partials.po_document_card')
            
            {{-- Delivery Logs --}}
            @include('procurement::partials.po_delivery_logs', ['isBuyer' => true, 'isVendor' => false])
        </div>

        {{-- Sidebar Insights --}}
        <div class="space-y-8">
            @include('procurement::partials.po_sidebar_logistics', ['isBuyer' => true])

            {{-- New Subsequent Orders Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-[2rem] border border-gray-100 dark:border-gray-700 overflow-hidden shadow-sm">
                <div class="px-8 py-5 border-b border-gray-50 dark:border-gray-700 bg-gray-50/30 dark:bg-gray-900/10 flex items-center justify-between">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Linked Repeat Orders</h3>
                    <i data-feather="refresh-cw" class="w-3.5 h-3.5 text-primary-600"></i>
                </div>
                <div class="divide-y divide-gray-50 dark:divide-gray-700">
                    @forelse($purchaseOrder->relatedRequisitions()->whereHas('purchaseOrder')->with('purchaseOrder')->latest()->get() as $req)
                        <a href="{{ route('procurement.po.show', $req->purchaseOrder) }}" class="flex items-center justify-between px-8 py-4 hover:bg-gray-50 dark:hover:bg-gray-900 transition-colors group">
                            <div>
                                <p class="text-xs font-black text-gray-900 dark:text-white group-hover:text-primary-600 uppercase">{{ $req->purchaseOrder->po_number }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-tighter">{{ $req->created_at->format('d M Y') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black text-gray-900 dark:text-white">{{ $req->purchaseOrder->formatted_total_amount }}</p>
                            </div>
                        </a>
                    @empty
                        <div class="px-8 py-10 text-center">
                            <p class="text-[10px] font-bold text-gray-300 uppercase italic">No linked orders yet</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @include('procurement::partials.po_sidebar_status_history')
            @include('procurement::partials.po_sidebar_escrow')
            @include('procurement::partials.po_sidebar_invoices', ['isVendor' => false])
        </div>
    </div>

    {{-- Modals --}}
    @include('procurement::partials.po_escrow_pay_modal')
@endsection

@push('scripts')
    @include('procurement::partials.po_show_scripts')
@endpush
