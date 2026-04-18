@extends('layouts.app', [
    'title' => 'Logistics & Receiving',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => url('/')],
        ['name' => 'Logistic', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-[1400px] mx-auto">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">SUPPLY CHAIN</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $goodsReceipts->total() }} Logistics Records</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white tracking-tight leading-none uppercase">
                Incoming <span class="text-primary-600">Shipments</span>
            </h1>
        </div>
    </div>

    {{-- Filter & Search Bar --}}
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm p-4 mb-8">
        <form action="{{ route('procurement.gr.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1 relative">
                <i data-feather="search" class="w-5 h-5 absolute left-5 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by GR No, PO No, or Vendor..." 
                    class="w-full pl-14 pr-6 py-4 rounded-2xl bg-gray-50 dark:bg-gray-900/50 border-transparent focus:bg-white dark:focus:bg-gray-900 focus:ring-2 focus:ring-primary-500 transition-all text-sm font-bold text-gray-900 dark:text-white uppercase tracking-tight">
            </div>
            
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-4 rounded-2xl font-black text-[11px] uppercase tracking-widest transition-all shadow-xl shadow-primary-600/20 active:scale-95">
                Apply Filters
            </button>
        </form>
    </div>

    {{-- Table / List --}}
    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden mb-10">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-800">
                        <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Shipment Identity</th>
                        <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Authority (PO)</th>
                        <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Source Entity</th>
                        <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Reception Event</th>
                        <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Storage Point</th>
                        <th class="px-8 py-6 text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($goodsReceipts as $gr)
                        <tr class="group hover:bg-gray-50/50 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-primary-50 dark:bg-primary-900/30 text-primary-600 flex items-center justify-center shadow-inner">
                                        <i data-feather="package" class="w-6 h-6"></i>
                                    </div>
                                    <div>
                                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-1">{{ $gr->gr_number }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">DN: {{ $gr->delivery_note_number ?? 'NONE' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-8">
                                <a href="{{ route('procurement.po.show', $gr->purchaseOrder) }}" class="text-[11px] font-black text-primary-600 hover:text-primary-700 uppercase tracking-tight">
                                    {{ $gr->purchaseOrder->po_number }}
                                </a>
                            </td>
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-3">
                                    <div class="px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-md text-[9px] font-black uppercase tracking-widest text-gray-500">
                                        {{ substr($gr->purchaseOrder->vendorCompany->name, 0, 3) }}
                                    </div>
                                    <span class="text-[11px] font-black text-gray-700 dark:text-gray-300 uppercase tracking-tight">{{ $gr->purchaseOrder->vendorCompany->name }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-8">
                                <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-1">{{ $gr->received_at->format('M d, Y') }}</p>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">BY {{ $gr->receivedBy->name }}</p>
                            </td>
                            <td class="px-8 py-8">
                                <span class="px-3 py-1 bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-lg text-[9px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                                    {{ $gr->warehouse->name ?? 'CENTRAL' }}
                                </span>
                            </td>
                            <td class="px-8 py-8">
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('procurement.gr.print', $gr->id) }}" target="_blank" class="w-9 h-9 rounded-xl border border-gray-100 dark:border-gray-800 flex items-center justify-center text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all">
                                        <i data-feather="printer" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('procurement.gr.download-pdf', $gr->id) }}" class="w-9 h-9 rounded-xl border border-gray-100 dark:border-gray-800 flex items-center justify-center text-gray-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all">
                                        <i data-feather="download" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-32 text-center">
                                <div class="w-24 h-24 mx-auto mb-8 bg-gray-50 dark:bg-gray-900 rounded-[2rem] flex justify-center items-center shadow-inner">
                                    <i data-feather="archive" class="w-10 h-10 text-gray-200"></i>
                                </div>
                                <h3 class="text-xl font-black text-gray-900 dark:text-white mb-2 uppercase tracking-tight">Zero Arrivals</h3>
                                <p class="text-[11px] font-bold text-gray-500 uppercase tracking-widest max-w-sm mx-auto leading-relaxed">Your procurement intake matches your search criteria perfectly; nothing has arrived yet.</p>
                                <a href="{{ route('procurement.po.index') }}" class="mt-10 inline-flex px-8 py-4 bg-primary-600 hover:bg-primary-700 text-white text-[10px] font-black rounded-2xl transition-all shadow-xl shadow-primary-600/20 uppercase tracking-widest active:scale-95">
                                    Receive Goods from Orders
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($goodsReceipts->hasPages())
            <div class="px-8 py-6 border-t border-gray-50 dark:border-gray-800 bg-gray-50/30 dark:bg-gray-900/20">
                {{ $goodsReceipts->links() }}
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
