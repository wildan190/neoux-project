@extends('layouts.app', ['title' => 'Debit Notes'])

@section('content')
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                <i data-feather="{{ $view === 'vendor' ? 'file-plus' : 'file-minus' }}"
                    class="w-6 h-6 {{ $view === 'vendor' ? 'text-green-500' : 'text-red-500' }}"></i>
                {{ $view === 'vendor' ? 'Credit Notes' : 'Debit Notes' }}
            </h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">
                {{ $view === 'vendor' ? 'List of credit notes issued to customers' : 'List of price adjustments for rejected or damaged goods' }}
            </p>
        </div>
    </div>

    {{-- Debit Notes List --}}
    @if($debitNotes->isEmpty())
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-feather="file-text" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No
                {{ $view === 'vendor' ? 'Credit' : 'Debit' }} Notes Found</h3>
            <p class="text-gray-500 dark:text-gray-400">
                {{ $view === 'vendor'
                ? 'Credit notes will appear after you approve a price adjustment in a GRR.'
                : 'Debit notes will appear after a price adjustment resolution is selected in a GRR.' }}
            </p>
        </div>
    @else
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            {{ $view === 'vendor' ? 'CN No.' : 'DN No.' }}
                        </th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            PO Number</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Original Value</th>
                        <th
                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Deduction</th>
                        <th
                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Status</th>
                        <th
                            class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($debitNotes as $dn)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $dn->dn_number }}</div>
                                <div class="text-xs text-gray-500">{{ $dn->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <a href="{{ route('procurement.po.show', $dn->purchaseOrder) }}"
                                    class="text-sm text-primary-600 hover:underline dark:text-primary-400">
                                    {{ $dn->purchaseOrder->po_number }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $dn->formatted_original_amount }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-semibold text-red-600 dark:text-red-400">-
                                    {{ $dn->formatted_deduction_amount }}</span>
                                <span class="text-xs text-gray-500 block">{{ number_format($dn->deduction_percentage, 0) }}%</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusColors = [
                                        'pending' => 'yellow',
                                        'approved' => 'green',
                                        'rejected' => 'red',
                                    ];
                                    $statusColor = $statusColors[$dn->status] ?? 'gray';
                                @endphp
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900/30 dark:text-{{ $statusColor }}-400 uppercase">
                                    {{ $dn->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <a href="{{ route('procurement.debit-notes.show', $dn) }}"
                                        class="p-2 bg-primary-100 hover:bg-primary-200 dark:bg-primary-900/30 dark:hover:bg-primary-900/50 text-primary-700 dark:text-primary-400 rounded-lg transition"
                                        title="View Details">
                                        <i data-feather="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('procurement.debit-notes.print', $dn) }}" target="_blank"
                                        class="p-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg transition"
                                        title="Print">
                                        <i data-feather="printer" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $debitNotes->links() }}
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush