@extends('layouts.app', [
    'title' => 'Sales Order Detail',
    'breadcrumbs' => [
        ['name' => 'Maps', 'url' => '#'],
        ['name' => 'Sales Order List', 'url' => route('procurement.po.index')],
        ['name' => $purchaseOrder->po_number, 'url' => null],
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $purchaseOrder->po_number }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $purchaseOrder->created_at->format('M d, Y') }}</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Sales Order</h1>
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

        @if($purchaseOrder->status === 'pending_vendor_acceptance')
            <form action="{{ route('procurement.po.vendor-accept', $purchaseOrder) }}" method="POST" onsubmit="return handlePrFormSubmit(this)" class="inline">
                @csrf
                <button type="submit" class="px-8 py-3 bg-primary-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition">
                    Accept Order
                </button>
            </form>
            <form action="{{ route('procurement.po.vendor-reject', $purchaseOrder) }}" method="POST" onsubmit="return confirm('Reject this PO?')" class="inline">
                @csrf
                <button type="submit" class="px-8 py-3 bg-red-50 text-red-600 rounded-2xl text-[11px] font-black uppercase tracking-widest hover:bg-red-100 transition">
                    Decline
                </button>
            </form>
        @endif

        @if($purchaseOrder->status === 'issued')
            <a href="{{ route('procurement.do.create', $purchaseOrder) }}" 
                class="px-8 py-3 bg-indigo-600 text-white rounded-2xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-indigo-600/20 hover:bg-indigo-700 transition flex items-center gap-2">
                <i data-feather="truck" class="w-3.5 h-3.5"></i>
                Arrange Delivery
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
            @include('procurement::partials.po_delivery_logs', ['isBuyer' => false, 'isVendor' => true])
        </div>

        {{-- Sidebar Insights --}}
        <div class="space-y-8">
            @include('procurement::partials.po_sidebar_logistics', ['isBuyer' => false])
            @include('procurement::partials.po_sidebar_status_history')
            @include('procurement::partials.po_sidebar_escrow')
            @include('procurement::partials.po_sidebar_invoices', ['isVendor' => true])
        </div>
    </div>
@endsection

@push('scripts')
    @include('procurement::partials.po_show_scripts')
@endpush
