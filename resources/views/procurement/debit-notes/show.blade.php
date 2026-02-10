@extends('layouts.app', ['title' => 'Debit Note: ' . $debitNote->dn_number])

@section('content')
    <div class="max-w-4xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('procurement.debit-notes.index') }}"
                    class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    <i data-feather="arrow-left" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $debitNote->dn_number }}</h1>
                    <p class="text-gray-500 dark:text-gray-400">Created {{ $debitNote->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                @php
                    $statusColors = [
                        'pending' => 'yellow',
                        'approved' => 'green',
                        'rejected' => 'red',
                    ];
                    $statusColor = $statusColors[$debitNote->status] ?? 'gray';
                @endphp
                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900/30 dark:text-{{ $statusColor }}-400 uppercase tracking-wider">
                    {{ $debitNote->status }}
                </span>
                <a href="{{ route('procurement.debit-notes.print', $debitNote) }}" target="_blank"
                    class="px-4 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg font-medium transition flex items-center gap-2">
                    <i data-feather="printer" class="w-4 h-4"></i>
                    Print
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Main Content --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Debit Note Details --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-primary-500 to-secondary-500">
                        <h2 class="text-lg font-semibold text-white flex items-center gap-2">
                            <i data-feather="file-minus" class="w-5 h-5"></i>
                            Debit Note Information
                        </h2>
                    </div>
                    <div class="p-6">
                        {{-- Item Info --}}
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $debitNote->goodsReturnRequest->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? '-' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $debitNote->goodsReturnRequest->quantity_affected }} units affected
                            </p>
                        </div>

                        {{-- Amount Breakdown --}}
                        <div class="space-y-4">
                            <div class="flex justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Original Value</span>
                                <span
                                    class="font-medium text-gray-900 dark:text-white">{{ $debitNote->formatted_original_amount }}</span>
                            </div>
                            <div
                                class="flex justify-between py-3 border-b border-gray-200 dark:border-gray-700 text-red-600">
                                <span>Deduction ({{ number_format($debitNote->deduction_percentage, 0) }}%)</span>
                                <span class="font-medium">- {{ $debitNote->formatted_deduction_amount }}</span>
                            </div>
                            <div class="flex justify-between py-3 text-lg">
                                <span class="font-semibold text-gray-900 dark:text-white">Final Value</span>
                                <span class="font-bold text-primary-600">{{ $debitNote->formatted_adjusted_amount }}</span>
                            </div>
                        </div>

                        @if($debitNote->reason)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs font-medium text-gray-500 uppercase mb-2">Reason</p>
                                <p class="text-gray-700 dark:text-gray-300">{{ $debitNote->reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Approval Section --}}
                @if($isBuyer && $debitNote->status === 'pending')
                    <div
                        class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-6 border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-yellow-100 dark:bg-yellow-800 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-feather="alert-circle" class="w-6 h-6 text-yellow-600 dark:text-yellow-300"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Approval Required</h3>
                                <p class="text-sm text-yellow-600 dark:text-yellow-400 mb-4">
                                    Please review and decide on this debit note to finalize the price adjustment.
                                </p>
                                <div class="flex items-center gap-3">
                                    <form action="{{ route('procurement.debit-notes.approve', $debitNote) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                                            <i data-feather="check" class="w-4 h-4"></i>
                                            Approve
                                        </button>
                                    </form>
                                    <form action="{{ route('procurement.debit-notes.reject', $debitNote) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                                            <i data-feather="x" class="w-4 h-4"></i>
                                            Reject
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif($debitNote->status === 'approved')
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                                <i data-feather="check-circle" class="w-6 h-6 text-green-600 dark:text-green-300"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-green-800 dark:text-green-200">Debit Note Approved</h3>
                                <p class="text-sm text-green-600 dark:text-green-400">
                                    Approved on {{ $debitNote->approved_by_vendor_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @elseif($debitNote->status === 'rejected')
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-6 border border-red-200 dark:border-red-800">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 bg-red-100 dark:bg-red-800 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i data-feather="x-circle" class="w-6 h-6 text-red-600 dark:text-red-300"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-red-800 dark:text-red-200">Debit Note Rejected</h3>
                                    <p class="text-sm text-red-600 dark:text-red-400">
                                        Rejected on {{ $debitNote->updated_at->format('d M Y, H:i') }}
                                    </p>
                                </div>
                            </div>
                            @if($isVendor)
                                <a href="{{ route('procurement.debit-notes.create', $debitNote->goodsReturnRequest) }}"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-bold transition">
                                    <i data-feather="edit-3" class="w-4 h-4"></i>
                                    Create Revised Debit Note
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Related Info --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Related Information</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">GRR Number</p>
                            <a href="{{ route('procurement.grr.show', $debitNote->goodsReturnRequest) }}"
                                class="text-sm font-semibold text-primary-600 hover:underline">
                                {{ $debitNote->goodsReturnRequest->grr_number }}
                            </a>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Purchase Order</p>
                            <a href="{{ route('procurement.po.show', $debitNote->purchaseOrder) }}"
                                class="text-sm font-semibold text-primary-600 hover:underline">
                                {{ $debitNote->purchaseOrder->po_number }}
                            </a>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Buyer</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $debitNote->purchaseOrder->purchaseRequisition->company->name }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Vendor</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ $debitNote->purchaseOrder->vendorCompany->name }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush