@extends('layouts.app', [
    'title' => 'Print DO: ' . $goodsReceipt->gr_number,
])

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-4 no-print">
            <button onclick="window.print()" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-bold shadow-sm">
                <i data-feather="printer" class="w-4 h-4 inline mr-2"></i>
                Print DO
            </button>
            <a href="{{ route('procurement.gr.download-pdf', $goodsReceipt->id) }}" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow-sm ml-2 inline-block">
                <i data-feather="download" class="w-4 h-4 inline mr-2"></i>
                Download PDF
            </a>
            <a href="{{ route('procurement.po.show', $goodsReceipt->purchaseOrder) }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold shadow-sm ml-2 inline-block">
                <i data-feather="x" class="w-4 h-4 inline mr-2"></i>
                Close
            </a>
        </div>

        <div id="printable-content" class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-green-600 to-green-500 px-8 py-6 text-white">
                <div class="flex justify-between items-start">
                    <div>
                        <img src="{{ asset('assets/img/logo.png') }}" alt="NeoUX Logo" class="h-12 mb-2 brightness-0 invert">
                        <p class="text-sm opacity-90">Platform by HUNTR</p>
                    </div>
                    <div class="text-right">
                        <h1 class="text-3xl font-bold mb-1">DELIVERY ORDER</h1>
                        <p class="text-lg font-mono">{{ $goodsReceipt->gr_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Info Section -->
            <div class="px-8 py-6 grid grid-cols-2 gap-6 border-b-2 border-gray-200">
                <!-- From (Vendor) -->
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">From (Vendor)</h3>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                        <p class="font-bold text-lg text-gray-900">{{ $goodsReceipt->purchaseOrder->vendorCompany->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $goodsReceipt->purchaseOrder->vendorCompany->email }}</p>
                        @if($goodsReceipt->purchaseOrder->vendorCompany->phone)
                            <p class="text-sm text-gray-600">{{ $goodsReceipt->purchaseOrder->vendorCompany->phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- To (Buyer) -->
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">To (Buyer)</h3>
                    <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                        <p class="font-bold text-lg text-gray-900">{{ $goodsReceipt->purchaseOrder->purchaseRequisition->company->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $goodsReceipt->purchaseOrder->purchaseRequisition->company->email }}</p>
                        @if($goodsReceipt->purchaseOrder->purchaseRequisition->company->phone)
                            <p class="text-sm text-gray-600">{{ $goodsReceipt->purchaseOrder->purchaseRequisition->company->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Details -->
            <div class="px-8 py-4 bg-gray-50 grid grid-cols-4 gap-4 text-sm border-b border-gray-200">
                <div>
                    <p class="text-gray-500 font-semibold">DO Date</p>
                    <p class="text-gray-900 font-bold">{{ $goodsReceipt->received_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">PO Number</p>
                    <p class="text-gray-900 font-bold">{{ $goodsReceipt->purchaseOrder->po_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Delivery Note #</p>
                    <p class="text-gray-900 font-bold">{{ $goodsReceipt->delivery_note_number ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Received By</p>
                    <p class="text-gray-900 font-bold">{{ $goodsReceipt->receivedBy->name }}</p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="px-8 py-6">
                <h3 class="text-sm font-bold text-gray-700 uppercase mb-3">Delivered Items</h3>
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 border-y-2 border-gray-300">
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Item Description</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Qty Ordered</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Qty Delivered</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Condition</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($goodsReceipt->items as $index => $item)
                            <tr class="border-b border-gray-200">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <p class="text-sm font-semibold text-gray-900">{{ $item->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name }}</p>
                                    <p class="text-xs text-gray-500">SKU: {{ $item->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->sku }}</p>
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-700">{{ $item->purchaseOrderItem->quantity_ordered }}</td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-green-600">{{ $item->quantity_received }}</td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $item->condition_notes ?: 'Good' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-green-50 border-t-2 border-green-300">
                            <td colspan="3" class="px-4 py-4 text-right text-sm font-bold text-gray-700 uppercase">Total Items Delivered</td>
                            <td class="px-4 py-4 text-center text-xl font-bold text-green-600">{{ $goodsReceipt->items->sum('quantity_received') }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes -->
            @if($goodsReceipt->notes)
                <div class="px-8 py-4 bg-yellow-50 border-y border-yellow-200">
                    <h3 class="text-sm font-bold text-gray-700 uppercase mb-2">Special Notes</h3>
                    <p class="text-sm text-gray-700">{{ $goodsReceipt->notes }}</p>
                </div>
            @endif

            <!-- Signatures -->
            <div class="px-8 py-6 bg-gray-50">
                <div class="grid grid-cols-2 gap-8 mt-4">
                    {{-- Vendor Company Representative (Delivering) --}}
                    <div class="text-center">
                        <div class="h-14 flex items-end justify-center mb-2">
                            <p class="font-mono text-xs text-gray-400 border border-gray-200 px-2 py-0.5 rounded bg-gray-50">
                                {{ substr(md5($goodsReceipt->purchaseOrder->vendorCompany->user->email . $goodsReceipt->received_at), 0, 12) }}
                            </p>
                        </div>
                        <div class="border-t-2 border-gray-400 pt-2">
                            <p class="text-xs font-bold text-gray-700">Vendor Representative</p>
                            <p class="text-xs font-semibold text-gray-900">{{ $goodsReceipt->purchaseOrder->vendorCompany->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $goodsReceipt->purchaseOrder->vendorCompany->name }}</p>
                            <p class="text-xs text-gray-400">{{ $goodsReceipt->received_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    {{-- Buyer Company Representative (Receiving) --}}
                    <div class="text-center">
                        <div class="h-14 flex items-end justify-center mb-2">
                            <p class="font-mono text-xs text-gray-400 border border-gray-200 px-2 py-0.5 rounded bg-gray-50">
                                {{ substr(md5($goodsReceipt->receivedBy->email . $goodsReceipt->received_at), 0, 12) }}
                            </p>
                        </div>
                        <div class="border-t-2 border-gray-400 pt-2">
                            <p class="text-xs font-bold text-gray-700">Buyer Representative</p>
                            <p class="text-xs font-semibold text-gray-900">{{ $goodsReceipt->receivedBy->name }}</p>
                            <p class="text-xs text-gray-500">{{ $goodsReceipt->purchaseOrder->purchaseRequisition->company->name }}</p>
                            <p class="text-xs text-gray-400">{{ $goodsReceipt->received_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-8 py-4 text-white text-center">
                <p class="text-xs opacity-75">
                    This is a computer-generated delivery order. No signature is required for printing.
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
