@extends('layouts.app', ['title' => 'Print Debit Note: ' . $debitNote->dn_number])

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-4 no-print">
            <button onclick="window.print()"
                class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-bold shadow-sm">
                <i data-feather="printer" class="w-4 h-4 inline mr-2"></i>
                Print Debit Note
            </button>
            <a href="{{ route('procurement.debit-notes.show', $debitNote) }}"
                class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold shadow-sm ml-2 inline-block">
                <i data-feather="x" class="w-4 h-4 inline mr-2"></i>
                Close
            </a>
        </div>

        <div id="printable-content" class="bg-white shadow-lg rounded-lg overflow-hidden">
            {{-- Header --}}
            <div class="bg-gradient-to-r from-red-500 to-orange-500 px-8 py-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-12 mb-2 brightness-0 invert">
                        <p class="text-sm opacity-90">Platform by HUNTR</p>
                    </div>
                    <div class="text-right">
                        <h1 class="text-3xl font-bold mb-1">DEBIT NOTE</h1>
                        <p class="text-lg font-mono">{{ $debitNote->dn_number }}</p>
                    </div>
                </div>
            </div>

            {{-- Info Section --}}
            <div class="px-8 py-6 grid grid-cols-2 gap-6 border-b-2 border-gray-200">
                {{-- Vendor --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Dari (Vendor)</h3>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-bold text-lg text-gray-900">{{ $debitNote->purchaseOrder->vendorCompany->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $debitNote->purchaseOrder->vendorCompany->email }}</p>
                    </div>
                </div>

                {{-- Buyer --}}
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Kepada (Buyer)</h3>
                    <div class="bg-red-50 p-4 rounded-lg border border-red-200">
                        <p class="font-bold text-lg text-gray-900">
                            {{ $debitNote->purchaseOrder->purchaseRequisition->company->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $debitNote->purchaseOrder->purchaseRequisition->company->email }}</p>
                    </div>
                </div>
            </div>

            {{-- Document Details --}}
            <div class="px-8 py-4 bg-gray-50 grid grid-cols-4 gap-4 text-sm border-b border-gray-200">
                <div>
                    <p class="text-gray-500 font-semibold">Tanggal</p>
                    <p class="text-gray-900 font-bold">{{ $debitNote->created_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">No. PO</p>
                    <p class="text-gray-900 font-bold">{{ $debitNote->purchaseOrder->po_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">No. GRR</p>
                    <p class="text-gray-900 font-bold">{{ $debitNote->goodsReturnRequest->grr_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Status</p>
                    <p class="text-gray-900 font-bold">{{ $debitNote->isApprovedByVendor() ? 'Approved' : 'Pending' }}</p>
                </div>
            </div>

            {{-- Item Details --}}
            <div class="px-8 py-6">
                <h3 class="text-sm font-bold text-gray-700 uppercase mb-3">Detail Penyesuaian</h3>
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 border-y-2 border-gray-300">
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Item</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Nilai Original</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Potongan</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Nilai Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3">
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $debitNote->goodsReturnRequest->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? '-' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Masalah: {{ $debitNote->goodsReturnRequest->issue_type_label }}
                                </p>
                            </td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">
                                {{ $debitNote->goodsReturnRequest->quantity_affected }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-700">
                                {{ $debitNote->formatted_original_amount }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-red-600 font-semibold">
                                - {{ $debitNote->formatted_deduction_amount }}
                                <span class="text-xs text-gray-500">({{ $debitNote->deduction_percentage }}%)</span>
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">
                                {{ $debitNote->formatted_adjusted_amount }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-red-50 border-t-2 border-red-300">
                            <td colspan="3" class="px-4 py-4 text-right text-sm font-bold text-gray-700 uppercase">Total
                                Potongan</td>
                            <td class="px-4 py-4 text-right text-xl font-bold text-red-600">
                                {{ $debitNote->formatted_deduction_amount }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Reason --}}
            @if($debitNote->reason)
                <div class="px-8 py-4 bg-yellow-50 border-y border-yellow-200">
                    <h3 class="text-sm font-bold text-gray-700 uppercase mb-2">Alasan Penyesuaian</h3>
                    <p class="text-sm text-gray-700">{{ $debitNote->reason }}</p>
                </div>
            @endif

            {{-- Signatures --}}
            <div class="px-8 py-6 bg-gray-50 border-t-2 border-gray-200">
                <div class="grid grid-cols-2 gap-8 mt-8">
                    {{-- Vendor Representative --}}
                    <div class="text-center">
                        <div class="h-16 flex items-end justify-center mb-2">
                            <div class="text-center">
                                <p class="text-xs text-gray-400 italic mb-1">Digital Signature</p>
                                <p
                                    class="font-mono text-sm text-gray-500 border border-gray-300 px-3 py-1 rounded bg-gray-50">
                                    {{ md5($debitNote->purchaseOrder->vendorCompany->user->email . $debitNote->created_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="border-t-2 border-gray-400 pt-2">
                            <p class="text-sm font-bold text-gray-700">Vendor Representative</p>
                            <p class="text-xs font-semibold text-gray-900">
                                {{ $debitNote->purchaseOrder->vendorCompany->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $debitNote->purchaseOrder->vendorCompany->name }}</p>
                        </div>
                    </div>

                    {{-- Buyer Representative --}}
                    <div class="text-center">
                        <div class="h-16 flex items-end justify-center mb-2">
                            <div class="text-center">
                                <p class="text-xs text-gray-400 italic mb-1">Digital Signature</p>
                                <p
                                    class="font-mono text-sm text-gray-500 border border-gray-300 px-3 py-1 rounded bg-gray-50">
                                    {{ md5($debitNote->purchaseOrder->purchaseRequisition->user->email . $debitNote->created_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="border-t-2 border-gray-400 pt-2">
                            <p class="text-sm font-bold text-gray-700">Buyer Representative</p>
                            <p class="text-xs font-semibold text-gray-900">
                                {{ $debitNote->purchaseOrder->purchaseRequisition->user->name }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $debitNote->purchaseOrder->purchaseRequisition->company->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-8 py-4 text-white text-center">
                <p class="text-xs opacity-75">
                    This is a computer-generated document. No signature is required.
                    <br>
                    Generated on {{ now()->format('d F Y, H:i') }}
                </p>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .no-print {
                display: none !important;
            }

            #printable-content {
                box-shadow: none !important;
                border-radius: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            @page {
                size: A4;
                margin: 0;
            }

            tr {
                page-break-inside: avoid;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush