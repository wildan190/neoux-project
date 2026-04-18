@extends('layouts.app', [
    'title' => 'Financial Adjustments (Debit Notes)',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => 'Debit Notes', 'url' => null],
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">FINANCIAL RECONCILIATION</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $debitNotes->total() }} Adjustment Records</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Purchase <span class="text-primary-600">Debit Notes</span></h1>
        </div>
    </div>

    {{-- Stats Bar --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Adjustment Volume</p>
            <p class="text-2xl font-black text-gray-900 dark:text-white">{{ number_format($debitNotes->sum('deduction_amount'), 2) }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Pending Approvals</p>
            <p class="text-2xl font-black text-yellow-500">{{ $debitNotes->where('status', 'pending')->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-3xl border border-gray-100 dark:border-gray-800 p-6 shadow-sm">
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Resolved (Approved)</p>
            <p class="text-2xl font-black text-emerald-500">{{ $debitNotes->where('status', 'approved')->count() }}</p>
        </div>
    </div>

    @if($debitNotes->isEmpty())
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-20 text-center border border-gray-100 dark:border-gray-700 shadow-sm">
            <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900 text-gray-200 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-inner">
                <i data-feather="dollar-sign" class="w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">No Financial Adjustments</h3>
            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 mt-2 uppercase tracking-widest">All procurement transactions are currently balanced without debit notes.</p>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-10">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-800">
                            <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Transaction Reference</th>
                            <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Authority (PO)</th>
                            <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Vendor Entity</th>
                            <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Adjustment Value</th>
                            <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">State</th>
                            <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        @foreach($debitNotes as $dn)
                            <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                                <td class="px-8 py-8">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 flex items-center justify-center shadow-inner">
                                            <i data-feather="file-text" class="w-6 h-6"></i>
                                        </div>
                                        <div>
                                            <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-1">DN-{{ $dn->id }}</p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">ISSUED: {{ $dn->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-8 font-black text-[11px] text-gray-700 dark:text-white uppercase tracking-tight">
                                    {{ $dn->purchaseOrder->po_number }}
                                </td>
                                <td class="px-8 py-8">
                                    <div class="flex items-center gap-3">
                                        <div class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-md text-[9px] font-black uppercase tracking-widest text-gray-500">
                                            {{ substr($dn->purchaseOrder->vendorCompany->name, 0, 3) }}
                                        </div>
                                        <span class="text-[11px] font-black text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $dn->purchaseOrder->vendorCompany->name }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-8">
                                    <p class="text-[11px] font-black text-red-600 tracking-tight leading-none mb-1">-{{ number_format($dn->deduction_amount, 2) }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $dn->deduction_percentage }}% DEDUCTION</p>
                                </td>
                                <td class="px-8 py-8">
                                    <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest
                                        @if($dn->status === 'approved') bg-emerald-100 text-emerald-700
                                        @elseif($dn->status === 'pending') bg-yellow-100 text-yellow-700
                                        @else bg-red-100 text-red-700 @endif">
                                        {{ $dn->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-8">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('procurement.debit-notes.show', $dn) }}" class="w-9 h-9 rounded-xl border border-gray-100 dark:border-gray-800 flex items-center justify-center text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('procurement.debit-notes.print', $dn) }}" target="_blank" class="w-9 h-9 rounded-xl border border-gray-100 dark:border-gray-800 flex items-center justify-center text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all">
                                            <i data-feather="printer" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($debitNotes->hasPages())
                <div class="px-8 py-6 border-t border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-900/20">
                    {{ $debitNotes->links() }}
                </div>
            @endif
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();
        });
    </script>
@endpush
