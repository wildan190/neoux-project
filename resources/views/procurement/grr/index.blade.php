@extends('layouts.app', ['title' => 'Goods Return Requests'])

@section('content')
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i data-feather="alert-triangle" class="w-6 h-6 text-yellow-500"></i>
                Goods Return Requests
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola laporan barang rusak atau tidak sesuai</p>
        </div>
    </div>

    {{-- Filter Tabs --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-6">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px overflow-x-auto">
                <a href="{{ route('procurement.grr.index', ['filter' => 'all']) }}"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap {{ $filter == 'all' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Semua
                </a>
                <a href="{{ route('procurement.grr.index', ['filter' => 'pending']) }}"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap flex items-center gap-2 {{ $filter == 'pending' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Pending
                    @if($pendingCount > 0)
                        <span
                            class="bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                    @endif
                </a>
                <a href="{{ route('procurement.grr.index', ['filter' => 'in_progress']) }}"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap {{ $filter == 'in_progress' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    In Progress
                </a>
                <a href="{{ route('procurement.grr.index', ['filter' => 'resolved']) }}"
                    class="px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap flex items-center gap-2 {{ $filter == 'resolved' ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Resolved
                    @if($resolvedCount > 0)
                        <span
                            class="bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 text-xs font-semibold px-2 py-0.5 rounded-full">{{ $resolvedCount }}</span>
                    @endif
                </a>
            </nav>
        </div>
    </div>

    {{-- GRR List --}}
    @if($grrList->isEmpty())
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="check-circle" class="w-8 h-8 text-green-500"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Tidak Ada Laporan</h3>
            <p class="text-gray-500 dark:text-gray-400">Belum ada barang bermasalah yang dilaporkan.</p>
        </div>
    @else
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            No. GRR</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Item</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Masalah</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Qty</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Resolusi</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($grrList as $grr)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $grr->grr_number }}</div>
                                    <div class="text-xs text-gray-500">{{ $grr->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $grr->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? '-' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        GR: {{ $grr->goodsReceiptItem->goodsReceipt->gr_number }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $issueColors = [
                                            'damaged' => 'yellow',
                                            'rejected' => 'red',
                                            'wrong_item' => 'orange',
                                        ];
                                        $color = $issueColors[$grr->issue_type] ?? 'gray';
                                    @endphp
                         <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-800 dark:bg-{{ $color }}-900/30 dark:text-{{ $color }}-400">
                                        {{ $grr->issue_type_label }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span
                                        class="text-sm font-semibold text-gray-900 dark:text-white">{{ $grr->quantity_affected }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700 dark:text-gray-300">
                                        {{ $grr->resolution_type_label }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColors = [
                                            'pending' => 'yellow',
                                            'approved_by_vendor' => 'blue',
                                            'rejected_by_vendor' => 'red',
                                            'resolved' => 'green',
                                        ];
                                        $statusColor = $statusColors[$grr->resolution_status] ?? 'gray';
                                        $statusLabels = [
                                            'pending' => 'Pending',
                                            'approved_by_vendor' => 'Disetujui Vendor',
                                            'rejected_by_vendor' => 'Ditolak Vendor',
                                            'resolved' => 'Resolved',
                                        ];
                                    @endphp
                         <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900/30 dark:text-{{ $statusColor }}-400">
                                        {{ $statusLabels[$grr->resolution_status] ?? $grr->resolution_status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('procurement.grr.show', $grr) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-primary-100 hover:bg-primary-200 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 text-primary-700 dark:text-primary-400 rounded-lg text-sm font-medium transition">
                                        <i data-feather="eye" class="w-4 h-4 mr-1"></i>
                                        Detail
                                    </a>
                                </td>
                            </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $grrList->links() }}
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush