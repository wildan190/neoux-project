@extends('layouts.app', [
    'title' => 'Print DO: ' . $deliveryOrder->do_number,
])

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-4 no-print flex justify-between">
            <div>
                <button onclick="window.print()" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-bold shadow-sm">
                    <i data-feather="printer" class="w-4 h-4 inline mr-2"></i>
                    Print DO
                </button>
                <a href="{{ route('procurement.po.show', $purchaseOrder) }}" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-bold shadow-sm ml-2 inline-block">
                    <i data-feather="x" class="w-4 h-4 inline mr-2"></i>
                    Close
                </a>
            </div>
        </div>

        <div id="printable-content" class="bg-white shadow-lg rounded-lg overflow-hidden">
            <!-- Header Section -->
            <div class="bg-gradient-to-r from-indigo-500 to-primary-500 px-8 py-6 text-white">
                <div class="flex justify-between items-start">
                    @php
                        $vendor = $purchaseOrder->vendorCompany;
                        $buyer = $purchaseOrder->purchaseRequisition?->company ?? $purchaseOrder->buyerCompany;
                    @endphp
                    <div>
                        @if($vendor->logo)
                            <img src="{{ asset('storage/' . $vendor->logo) }}" alt="{{ $vendor->name }} Logo" class="h-12 mb-2 brightness-0 invert">
                        @else
                            <div class="text-xl font-bold mb-1">{{ $vendor->name }}</div>
                        @endif
                        <p class="text-sm opacity-90">Delivery Order Document</p>
                    </div>
                    <div class="text-right">
                        <h1 class="text-3xl font-bold mb-1">DELIVERY ORDER</h1>
                        <p class="text-lg font-mono">{{ $deliveryOrder->do_number }}</p>
                    </div>
                </div>
            </div>

            <!-- Address Section -->
            <div class="px-8 py-6 grid grid-cols-2 gap-6 border-b-2 border-gray-200">
                <!-- From -->
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">From (Sender)</h3>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 min-h-[120px]">
                        <p class="font-bold text-lg text-gray-900">{{ $vendor->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $vendor->address ?? 'No address provided' }}</p>
                        @if($vendor->phone)
                            <p class="text-sm text-gray-600">Tel: {{ $vendor->phone }}</p>
                        @endif
                    </div>
                </div>

                <!-- Ship To -->
                <div>
                    <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Ship To (Recipient)</h3>
                    <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200 min-h-[120px]">
                        <p class="font-bold text-lg text-gray-900">{{ $buyer->name }}</p>
                        <p class="text-sm text-gray-600 mt-1">{{ $buyer->address ?? 'No address provided' }}</p>
                        @if($buyer->phone)
                            <p class="text-sm text-gray-600">Tel: {{ $buyer->phone }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Delivery Details -->
            <div class="px-8 py-4 bg-gray-50 grid grid-cols-4 gap-4 text-sm border-b border-gray-200">
                <div>
                    <p class="text-gray-500 font-semibold">DO Date</p>
                    <p class="text-gray-900 font-bold">{{ $deliveryOrder->created_at->format('d M Y') }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">PO Number</p>
                    <p class="text-gray-900 font-bold">{{ $purchaseOrder->po_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Status</p>
                    <p class="text-gray-900 font-bold uppercase">{{ $deliveryOrder->status }}</p>
                </div>
                <div>
                    <p class="text-gray-500 font-semibold">Tracking #</p>
                    <p class="text-gray-900 font-bold">{{ $deliveryOrder->tracking_number ?? '-' }}</p>
                </div>
            </div>

            <!-- Items Table -->
            <div class="px-8 py-6">
                <h3 class="text-sm font-bold text-gray-700 uppercase mb-3">Shipped Items</h3>
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 border-y-2 border-gray-300">
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">Item Description</th>
                            <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase">SKU</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Qty Shipped</th>
                            <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase">Received Qty</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($deliveryOrder->items as $index => $item)
                            <tr class="border-b border-gray-200">
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">
                                    {{ $item->purchaseOrderItem->purchaseRequisitionItem?->catalogueItem->name ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $item->purchaseOrderItem->purchaseRequisitionItem?->catalogueItem->sku ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm font-bold text-gray-900">
                                    {{ $item->quantity_shipped }}
                                </td>
                                <td class="px-4 py-3 text-center text-sm text-gray-300">
                                    [ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ]
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Notes & Signatures -->
            <div class="px-8 py-6 bg-gray-50 border-t-2 border-gray-200">
                <div class="grid grid-cols-2 gap-8 mb-6">
                    @if($purchaseOrder->offer)
                    <div>
                        <h3 class="text-sm font-bold text-gray-700 uppercase mb-2">Negotiated Terms</h3>
                        <ul class="text-xs text-gray-600 space-y-1">
                            <li><strong>Payment Scheme:</strong> {{ $purchaseOrder->offer->payment_scheme ?? 'N/A' }}</li>
                            <li><strong>Promised Delivery:</strong> {{ $purchaseOrder->offer->delivery_time ?? 'N/A' }} Days</li>
                            <li><strong>Warranty:</strong> {{ $purchaseOrder->offer->warranty ?? 'N/A' }} Months</li>
                        </ul>
                    </div>
                    @endif

                    @if($deliveryOrder->notes)
                    <div>
                        <h3 class="text-sm font-bold text-gray-700 uppercase mb-2">Vendor Notes</h3>
                        <p class="text-xs text-gray-600 italic">"{{ $deliveryOrder->notes }}"</p>
                    </div>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-8 mt-12">
                    {{-- Vendor Signature --}}
                    <div class="text-center">
                        <div class="h-24 flex items-center justify-center mb-2">
                             <img src="{{ App\Support\QrCodeHelper::generateBase64Svg($deliveryOrder->do_number . '|SHIPPED|' . $deliveryOrder->shipped_at, 80) }}" class="w-20 h-20">
                        </div>
                        <div class="border-t-2 border-gray-400 pt-2">
                            <p class="text-sm font-bold text-gray-700">Vendor Warehouse</p>
                            <p class="text-xs text-gray-500">{{ $vendor->name }}</p>
                        </div>
                    </div>

                    {{-- Recipient Signature --}}
                    <div class="text-center">
                        <div class="h-24 flex items-center justify-center mb-2">
                            <div class="w-32 h-20 border-2 border-dashed border-gray-200 rounded-xl flex items-center justify-center">
                                <span class="text-[8px] font-black text-gray-300 uppercase leading-tight px-2 text-center">Receiver Signature & Stamp</span>
                            </div>
                        </div>
                        <div class="border-t-2 border-gray-400 pt-2">
                            <p class="text-sm font-bold text-gray-700">Recipient Signature</p>
                            <p class="text-xs text-gray-500">{{ $buyer->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="bg-gradient-to-r from-gray-700 to-gray-900 px-8 py-4 text-white text-center">
                <p class="text-xs opacity-75">
                    This document serves as proof of delivery and must be signed by the recipient upon receipt of goods.
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
        body { margin: 0; padding: 0; }
        .no-print { display: none !important; }
        #printable-content { box-shadow: none !important; border-radius: 0 !important; margin: 0 !important; width: 100% !important; }
        @page { size: A4; margin: 0; }
        tr { page-break-inside: avoid; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }
</style>
@endpush

@push('scripts')
<script>
    feather.replace();
</script>
@endpush
