@extends('layouts.app', [
    'title' => 'Invoice Detail',
    'breadcrumbs' => [
        ['name' => 'Maps', 'url' => '#'],
        ['name' => 'Finance', 'url' => '#'],
        ['name' => 'Invoices', 'url' => route('procurement.invoices.index')],
        ['name' => $invoice->invoice_number, 'url' => null],
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $invoice->invoice_number }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $invoice->created_at->format('M d, Y') }}</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Invoice Detail</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('procurement.invoices.print', $invoice) }}" target="_blank"
                class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 transition-all">
                Print
            </a>
            <a href="{{ route('procurement.invoices.download-pdf', $invoice) }}"
                class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all">
                Download PDF
            </a>
        </div>
    </div>

    {{-- The Map: Invoice Lifecycle --}}
    @include('procurement::partials.invoice_lifecycle_stepper')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Area --}}
        <div class="lg:col-span-2 space-y-8">
            {{-- Invoice Items --}}
            @include('procurement::partials.invoice_items_card')
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Related Context</h3>
                <div class="space-y-4">
                    <a href="{{ route('procurement.po.show', $invoice->purchaseOrder) }}" class="block p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-emerald-300 transition-all group">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Purchase Order</p>
                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase group-hover:text-emerald-600 transition-colors">{{ $invoice->purchaseOrder->po_number }}</p>
                    </a>

                    @if($invoice->purchaseOrder->offer)
                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <h4 class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Negotiated Terms</h4>
                        <div class="space-y-3">
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Payment Scheme</p>
                                <p class="text-[10px] font-bold text-gray-900 dark:text-white uppercase">{{ $invoice->purchaseOrder->offer->payment_scheme ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Promised Delivery</p>
                                <p class="text-[10px] font-bold text-gray-900 dark:text-white uppercase">{{ $invoice->purchaseOrder->offer->delivery_time ?? 'N/A' }} Days</p>
                            </div>
                            <div>
                                <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Warranty</p>
                                <p class="text-[10px] font-bold text-gray-900 dark:text-white uppercase">{{ $invoice->purchaseOrder->offer->warranty ?? 'N/A' }} Months</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-700 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Internal Approval</h3>
                
                <div class="space-y-4">
                    @if($invoice->status === 'matched' || $invoice->status === 'pending')
                        <form action="{{ route('procurement.invoices.vendor-approve', $invoice) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-4 bg-emerald-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition">
                                Approve (Vendor Head)
                            </button>
                        </form>
                    @endif

                    @if($invoice->status === 'paid')
                        <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800 text-center">
                            <i data-feather="check-circle" class="w-8 h-8 text-green-600 dark:text-green-400 mx-auto mb-2"></i>
                            <p class="text-sm font-bold text-green-900 dark:text-green-100">Paid & Disbursed</p>
                            <p class="text-xs text-green-700 dark:text-green-300 mt-1">Funds have been released.</p>
                        </div>
                    @else
                        <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-700 text-center">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Workflow State</p>
                            <p class="text-xs font-bold text-gray-700 dark:text-gray-300 mt-1 capitalize">{{ str_replace('_', ' ', $invoice->status) }}</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tax Invoice Section --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-3xl border border-gray-100 dark:border-gray-700 p-8">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Tax Invoice (Faktur)</h3>
                
                @if($invoice->tax_invoice_number)
                    <div class="p-5 bg-emerald-50 dark:bg-emerald-900/20 rounded-2xl border border-emerald-100 dark:border-emerald-800 mb-6">
                        <div class="flex items-start gap-3">
                            <i data-feather="check-circle" class="w-5 h-5 text-emerald-600 dark:text-emerald-400 mt-0.5"></i>
                            <div class="flex-1">
                                <p class="text-[10px] font-black text-emerald-700 dark:text-emerald-300 uppercase">ISSUED</p>
                                <p class="text-xs font-bold text-gray-900 dark:text-white mt-1 font-mono">{{ $invoice->tax_invoice_number }}</p>
                                <p class="text-[9px] text-emerald-600 dark:text-emerald-400 mt-1 uppercase">{{ $invoice->tax_invoice_issued_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-col gap-3">
                        <a href="{{ route('procurement.invoices.tax-invoice-print', $invoice) }}" target="_blank"
                            class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            <i data-feather="printer" class="w-3.5 h-3.5"></i>
                            Print Faktur
                        </a>
                        
                        <a href="{{ route('procurement.invoices.tax-invoice-pdf', $invoice) }}"
                            class="inline-flex items-center justify-center gap-2 px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition shadow-lg shadow-emerald-500/20">
                            <i data-feather="download" class="w-3.5 h-3.5"></i>
                            Download PDF
                        </a>
                    </div>
                @elseif($invoice->status === 'vendor_approved' || $invoice->status === 'purchasing_approved' || $invoice->status === 'paid')
                    <form action="{{ route('procurement.invoices.issue-tax-invoice', $invoice) }}" method="POST">
                        @csrf
                        <button type="submit" 
                            class="w-full inline-flex items-center justify-center gap-2 px-4 py-4 bg-primary-600 hover:bg-primary-700 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl transition shadow-xl shadow-primary-500/20">
                            <i data-feather="file-plus" class="w-4 h-4"></i>
                            Issue Tax Invoice
                        </button>
                    </form>
                    <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest mt-3 text-center">Generate Faktur Pajak for legal compliance</p>
                @else
                    <div class="p-4 bg-gray-50 dark:bg-gray-900/50 rounded-xl border border-gray-100 dark:border-gray-700 text-center">
                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-tight leading-relaxed">Faktur pajak dapat diterbitkan setelah invoice disetujui Vendor Head.</p>
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
