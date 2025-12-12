@extends('layouts.app', [
    'title' => 'Print Tax Invoice: ' . $invoice->purchaseOrder->invoice_number,
])

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-4 no-print">
            <button onclick="window.print()" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-bold shadow-sm">
                <i data-feather="printer" class="w-4 h-4 inline mr-2"></i>
                Print Tax Invoice
            </button>
            <a href="{{ route('procurement.po.download-pdf', $invoice->purchaseOrder) }}" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow-sm ml-2 inline-block">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Download Faktur PDF
            </a>
            <a href="{{ route('procurement.po.show', $invoice->purchaseOrder) }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold shadow-sm ml-2 inline-block">
                <i data-feather="x" class="w-4 h-4 inline mr-2"></i>
                Close
            </a>
        </div>

        <div id="printable-content" class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-primary-500 to-secondary-500 px-8 py-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <img src="{{ asset('assets/img/logo.png') }}" alt="NeoUX Logo" class="h-12 mb-2 brightness-0 invert">
                        <p class="text-sm opacity-90">Platform by HUNTR</p>
                    </div>
                    <div class="text-right">
                        <h1 class="text-3xl font-bold mb-1">TAX INVOICE - FAKTUR PAJAK</h1>
                        <p class="text-lg font-mono">{{ $invoice->purchaseOrder->invoice_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="px-8 py-6 grid grid-cols-2 gap-6 border-b-2 border-gray-200">
                <!-- Vendor Info -->
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Vendor</h3>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-bold text-lg text-gray-900">{{ $invoice->purchaseOrder->vendorCompany->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $invoice->purchaseOrder->vendorCompany->email }}</p>
                        @if($invoice->purchaseOrder->vendorCompany->phone)
                            <p class="text-sm text-gray-600">{{ $invoice->purchaseOrder->vendorCompany->phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- Buyer Info -->
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Buyer</h3>
                    <div class="bg-primary-50 p-4 rounded-lg border border-primary-200">
                        <p class="font-bold text-lg text-gray-900">{{ $invoice->purchaseOrder->purchaseRequisition->company->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $invoice->purchaseOrder->purchaseRequisition->company->email }}</p>
                        @if($invoice->purchaseOrder->purchaseRequisition->company->phone)
                            <p class="text-sm text-gray-600">{{ $invoice->purchaseOrder->purchaseRequisition->company->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="px-8 py-4 bg-gray-50 grid grid-cols-4 gap-4 text-sm border-b border-gray-200">
                <div>
                    <p class="text-gray-500 font-semibold">PO Date</p>
                    <p class="text-gray-900 font-bold">{{ $invoice->purchaseOrder->created_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">PR Number</p>
                    <p class="text-gray-900 font-bold">{{ $invoice->purchaseOrder->purchaseRequisition->pr_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Status</p>
                    <p class="text-gray-900 font-bold uppercase">{{ $invoice->purchaseOrder->status }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Created By</p>
                    <p class="text-gray-900 font-bold">{{ $invoice->purchaseOrder->createdBy->name }}</p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="px-8 py-6">
                <h3 class="text-sm font-bold text-gray-700 uppercase mb-3">Order Items</h3>
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 border-y-2 border-gray-300">
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Item Description</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Qty</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Unit Price</th>
                            <th class="px-4 py-3 text-right text-xs font-bold text-gray-600 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->purchaseOrder->items as $index => $item)
                            <tr class="border-b border-gray-200">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-semibold text-gray-900">{{ $item->purchaseRequisitionItem->catalogueItem->name }}</p>
                                    <p class="text-xs text-gray-500">SKU: {{ $item->purchaseRequisitionItem->catalogueItem->sku }}</p>
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-semibold text-gray-900">{{ $item->quantity_ordered }}</td>
                                <td class="px-4 py-3 text-right text-sm text-gray-700">{{ $item->formatted_unit_price }}</td>
                                <td class="px-4 py-3 text-right text-sm font-bold text-gray-900">{{ $item->formatted_subtotal }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t border-gray-300">
                            <td colspan="4" class="px-4 py-3 text-right text-sm font-bold text-gray-700 uppercase">Subtotal</td>
                            <td class="px-4 py-3 text-right text-lg font-bold text-gray-900">{{ $invoice->purchaseOrder->formatted_total_amount }}</td>
                        </tr>
                        @if($invoice->purchaseOrder->has_deductions)
                            <tr class="bg-red-50 border-t border-red-200">
                                <td colspan="4" class="px-4 py-2 text-right text-sm font-medium text-red-700">Potongan Harga (Debit Note)</td>
                                <td class="px-4 py-2 text-right text-lg font-bold text-red-600">- {{ $invoice->purchaseOrder->formatted_total_deduction }}</td>
                            </tr>
                            <tr class="bg-primary-50 border-t-2 border-primary-300">
                                <td colspan="4" class="px-4 py-4 text-right text-sm font-bold text-primary-700 uppercase">Total Akhir</td>
                                <td class="px-4 py-4 text-right text-xl font-bold text-primary-600">{{ $invoice->purchaseOrder->formatted_adjusted_total_amount }}</td>
                            </tr>
                        @else
                            <tr class="bg-primary-50 border-t-2 border-primary-300">
                                <td colspan="4" class="px-4 py-4 text-right text-sm font-bold text-gray-700 uppercase">Total Amount</td>
                                <td class="px-4 py-4 text-right text-xl font-bold text-primary-600">{{ $invoice->purchaseOrder->formatted_total_amount }}</td>
                            </tr>
                        @endif
                    </tfoot>
                </table>
            </div>

            <!-- Terms & Signatures -->
            <div class="px-8 py-6 bg-gray-50 border-t-2 border-gray-200">
                <div class="mb-6">
                    <h3 class="text-sm font-bold text-gray-700 uppercase mb-2">Terms & Conditions</h3>
                    <ul class="text-xs text-gray-600 space-y-1">
                        <li>• Payment terms: Net 30 days from invoice date</li>
                        <li>• Delivery must be made to the address specified by the buyer</li>
                        <li>• All items must match the specifications in this purchase order</li>
                        <li>• Any discrepancies must be reported within 48 hours of delivery</li>
                    </ul>
                </div>

                <div class="grid grid-cols-2 gap-8 mt-8">
                    {{-- Vendor Company Representative (Issuing Faktur) --}}
                    <div class="text-center">
                        <div class="h-16 flex items-end justify-center mb-2">
                            <div class="text-center">
                                <p class="text-xs text-gray-400 italic mb-1">Digital Signature</p>
                                <p class="font-mono text-sm text-gray-500 border border-gray-300 px-3 py-1 rounded bg-gray-50">
                                    {{ md5($invoice->purchaseOrder->vendorCompany->user->email . $invoice->created_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="border-t-2 border-gray-400 pt-2">
                            <p class="text-sm font-bold text-gray-700">Vendor Representative (PKP)</p>
                            <p class="text-xs font-semibold text-gray-900">{{ $invoice->purchaseOrder->vendorCompany->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $invoice->purchaseOrder->vendorCompany->name }}</p>
                            <p class="text-xs text-gray-400">{{ $invoice->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    {{-- Buyer Company Representative --}}
                    <div class="text-center">
                        <div class="h-16 flex items-end justify-center mb-2">
                            <div class="text-center">
                                <p class="text-xs text-gray-400 italic mb-1">Digital Signature</p>
                                <p class="font-mono text-sm text-gray-500 border border-gray-300 px-3 py-1 rounded bg-gray-50">
                                    {{ md5($invoice->purchaseOrder->purchaseRequisition->user->email . $invoice->created_at) }}
                                </p>
                            </div>
                        </div>
                        <div class="border-t-2 border-gray-400 pt-2">
                            <p class="text-sm font-bold text-gray-700">Buyer Representative</p>
                            <p class="text-xs font-semibold text-gray-900">{{ $invoice->purchaseOrder->purchaseRequisition->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $invoice->purchaseOrder->purchaseRequisition->company->name }}</p>
                            <p class="text-xs text-gray-400">Tax receipt confirmation</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
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

        /* Prevent page breaks within table rows */
        tr {
            page-break-inside: avoid;
        }

        /* Ensure colors print */
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
