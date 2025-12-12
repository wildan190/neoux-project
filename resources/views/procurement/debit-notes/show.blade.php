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
                    <p class="text-gray-500 dark:text-gray-400">Dibuat {{ $debitNote->created_at->format('d M Y, H:i') }}
                    </p>
                </div>
            </div>
            <a href="{{ route('procurement.debit-notes.print', $debitNote) }}" target="_blank"
                class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition flex items-center gap-2">
                <i data-feather="printer" class="w-4 h-4"></i>
                Print
            </a>
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
                            Debit Note
                        </h2>
                    </div>
                    <div class="p-6">
                        {{-- Item Info --}}
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $debitNote->goodsReturnRequest->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? '-' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $debitNote->goodsReturnRequest->quantity_affected }} unit bermasalah
                            </p>
                        </div>

                        {{-- Amount Breakdown --}}
                        <div class="space-y-4">
                            <div class="flex justify-between py-3 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-gray-600 dark:text-gray-400">Nilai Original</span>
                                <span
                                    class="font-medium text-gray-900 dark:text-white">{{ $debitNote->formatted_original_amount }}</span>
                            </div>
                            <div
                                class="flex justify-between py-3 border-b border-gray-200 dark:border-gray-700 text-red-600">
                                <span>Potongan ({{ $debitNote->deduction_percentage }}%)</span>
                                <span class="font-medium">- {{ $debitNote->formatted_deduction_amount }}</span>
                            </div>
                            <div class="flex justify-between py-3 text-lg">
                                <span class="font-semibold text-gray-900 dark:text-white">Nilai Akhir</span>
                                <span class="font-bold text-primary-600">{{ $debitNote->formatted_adjusted_amount }}</span>
                            </div>
                        </div>

                        @if($debitNote->reason)
                            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                <p class="text-xs font-medium text-gray-500 uppercase mb-2">Alasan</p>
                                <p class="text-gray-700 dark:text-gray-300">{{ $debitNote->reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Approval Section --}}
                @if($isBuyer && !$debitNote->isApprovedByVendor())
                    <div
                        class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-6 border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-yellow-100 dark:bg-yellow-800 rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-feather="alert-circle" class="w-6 h-6 text-yellow-600 dark:text-yellow-300"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Persetujuan Diperlukan</h3>
                                <p class="text-sm text-yellow-600 dark:text-yellow-400 mb-4">
                                    Harap review dan setujui debit note ini untuk memfinalisasi penyesuaian harga.
                                </p>
                                <form action="{{ route('procurement.debit-notes.approve', $debitNote) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition">
                                        <i data-feather="check" class="w-4 h-4 inline mr-2"></i>
                                        Setujui Debit Note
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @elseif($debitNote->isApprovedByVendor())
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6 border border-green-200 dark:border-green-800">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                                <i data-feather="check-circle" class="w-6 h-6 text-green-600 dark:text-green-300"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-green-800 dark:text-green-200">Debit Note Disetujui</h3>
                                <p class="text-sm text-green-600 dark:text-green-400">
                                    Disetujui pada {{ $debitNote->approved_by_vendor_at->format('d M Y, H:i') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">
                {{-- Related Info --}}
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Terkait</h2>
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