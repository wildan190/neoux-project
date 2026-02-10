@extends('layouts.app', ['title' => 'GRR: ' . $goodsReturnRequest->grr_number])

@section('content')
    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('procurement.grr.index') }}"
                class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <i data-feather="arrow-left" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $goodsReturnRequest->grr_number }}</h1>
                <p class="text-gray-500 dark:text-gray-400">Dibuat
                    {{ $goodsReturnRequest->created_at->format('d M Y, H:i') }}</p>
            </div>
        </div>
        <div>
            @php
                $statusColors = [
                    'pending' => 'yellow',
                    'approved_by_vendor' => 'blue',
                    'rejected_by_vendor' => 'red',
                    'awaiting_buyer_approval' => 'purple',
                    'rejected_by_buyer' => 'orange',
                    'awaiting_replacement_shipping' => 'yellow',
                    'replacement_shipped' => 'blue',
                    'resolved' => 'green',
                ];
                $statusColor = $statusColors[$goodsReturnRequest->resolution_status] ?? 'gray';
            @endphp
            <span
                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900/30 dark:text-{{ $statusColor }}-400">
                @if($goodsReturnRequest->resolution_status === 'pending')
                    <i data-feather="clock" class="w-4 h-4 mr-2"></i> Pending
                @elseif($goodsReturnRequest->resolution_status === 'approved_by_vendor')
                    <i data-feather="check" class="w-4 h-4 mr-2"></i> Approved by Vendor
                @elseif($goodsReturnRequest->resolution_status === 'rejected_by_vendor')
                    <i data-feather="x" class="w-4 h-4 mr-2"></i> Rejected by Vendor
                @elseif($goodsReturnRequest->resolution_status === 'awaiting_buyer_approval')
                    <i data-feather="eye" class="w-4 h-4 mr-2"></i> Awaiting Buyer Review
                @elseif($goodsReturnRequest->resolution_status === 'rejected_by_buyer')
                    <i data-feather="alert-circle" class="w-4 h-4 mr-2"></i> Rejected by Buyer
                @elseif($goodsReturnRequest->resolution_status === 'awaiting_replacement_shipping')
                    <i data-feather="truck" class="w-4 h-4 mr-2"></i> Awaiting Shipping
                @elseif($goodsReturnRequest->resolution_status === 'replacement_shipped')
                    <i data-feather="send" class="w-4 h-4 mr-2"></i> Replacement Shipped
                @else
                    <i data-feather="check-circle" class="w-4 h-4 mr-2"></i> Resolved
                @endif
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Main Content --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Issue Details --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-red-50 dark:bg-red-900/20">
                    <h2 class="text-lg font-semibold text-red-700 dark:text-red-300 flex items-center gap-2">
                        <i data-feather="alert-triangle" class="w-5 h-5"></i>
                        Issue Details
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Issue Type</p>
                            @php
                                $issueColors = ['damaged' => 'yellow', 'rejected' => 'red', 'wrong_item' => 'orange'];
                                $issueColor = $issueColors[$goodsReturnRequest->issue_type] ?? 'gray';
                            @endphp
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-{{ $issueColor }}-100 text-{{ $issueColor }}-800 dark:bg-{{ $issueColor }}-900/30 dark:text-{{ $issueColor }}-400 mt-1">
                                {{ $goodsReturnRequest->issue_type_label }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Affected Quantity</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $goodsReturnRequest->quantity_affected }} units</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase mb-2">Item</p>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $goodsReturnRequest->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? '-' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                SKU:
                                {{ $goodsReturnRequest->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->sku ?? '-' }}
                            </p>
                        </div>
                    </div>

                    @if($goodsReturnRequest->issue_description)
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Issue Description</p>
                            <div class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg p-4 whitespace-pre-line">
                                {{ $goodsReturnRequest->issue_description }}
                            </div>
                        </div>
                    @endif

                    {{-- Photo Evidence --}}
                    @if($goodsReturnRequest->photo_evidence && count($goodsReturnRequest->photo_evidence) > 0)
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Evidence Photos</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($goodsReturnRequest->photo_evidence as $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Evidence"
                                        class="rounded-lg object-cover h-24 w-full cursor-pointer hover:opacity-80 transition"
                                        onclick="window.open(this.src)">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Resolution Section --}}
            @if($goodsReturnRequest->resolution_status !== 'resolved')
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Resolution</h2>
                    </div>
                    <div class="p-6">
                        @if($isBuyer && $goodsReturnRequest->resolution_status === 'pending')
                            {{-- Buyer selects resolution type --}}
                            <form action="{{ route('procurement.grr.update-resolution', $goodsReturnRequest) }}" method="POST" id="resolutionForm">
                                @csrf
                                @method('PUT')
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Choose desired resolution type:</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <label class="cursor-pointer resolution-option" onclick="selectResolution('price_adjustment', 'Price Adjustment')">
                                        <input type="radio" name="resolution_type" value="price_adjustment" class="sr-only" required>
                                        <div id="card_price_adjustment" class="border-2 border-gray-200 dark:border-gray-600 rounded-xl p-4 text-center 
                                                    hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-200 relative h-full">
                                            <div id="indicator_price_adjustment" class="hidden absolute top-2 right-2 w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <i data-feather="dollar-sign" class="w-8 h-8 mx-auto mb-2 text-primary-500"></i>
                                            <p class="font-semibold text-gray-900 dark:text-white">Price Adjustment</p>
                                            <p class="text-xs text-gray-500 mt-1">Accept items with price discount</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer resolution-option" onclick="selectResolution('replacement', 'Unit Replacement')">
                                        <input type="radio" name="resolution_type" value="replacement" class="sr-only">
                                        <div id="card_replacement" class="border-2 border-gray-200 dark:border-gray-600 rounded-xl p-4 text-center 
                                                    hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-200 relative h-full">
                                            <div id="indicator_replacement" class="hidden absolute top-2 right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <i data-feather="refresh-cw" class="w-8 h-8 mx-auto mb-2 text-blue-500"></i>
                                            <p class="font-semibold text-gray-900 dark:text-white">Unit Replacement</p>
                                            <p class="text-xs text-gray-500 mt-1">Vendor ships new units</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer resolution-option" onclick="selectResolution('return_refund', 'Return & Refund')">
                                        <input type="radio" name="resolution_type" value="return_refund" class="sr-only">
                                        <div id="card_return_refund" class="border-2 border-gray-200 dark:border-gray-600 rounded-xl p-4 text-center 
                                                    hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-200 relative h-full">
                                            <div id="indicator_return_refund" class="hidden absolute top-2 right-2 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <i data-feather="rotate-ccw" class="w-8 h-8 mx-auto mb-2 text-red-500"></i>
                                            <p class="font-semibold text-gray-900 dark:text-white">Return & Refund</p>
                                            <p class="text-xs text-gray-500 mt-1">Return items for full refund</p>
                                        </div>
                                    </label>
                                </div>
                                <div id="selectedResolutionDisplay" class="hidden mb-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                                    <p class="text-sm text-green-700 dark:text-green-300 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>You selected: <strong id="selectedResolutionText"></strong></span>
                                    </p>
                                </div>
                                <button type="submit" id="submitBtn"
                                    class="w-full py-3 bg-gray-300 cursor-not-allowed text-white rounded-lg font-semibold transition" disabled>
                                    Send Request to Vendor
                                </button>
                            </form>
                        @elseif($goodsReturnRequest->resolution_status === 'replacement_shipped' && $isBuyer)
                            {{-- Buyer confirms receipt of replacement --}}
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6 border border-blue-200 dark:border-blue-700">
                                <h3 class="font-semibold text-blue-800 dark:text-blue-300 flex items-center gap-2 mb-4">
                                    <i data-feather="package" class="w-5 h-5"></i>
                                    Replacement Shipped
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-blue-100 dark:border-blue-800">
                                        <p class="text-xs text-gray-500 uppercase">Tracking Number</p>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $goodsReturnRequest->replacementDelivery->tracking_number ?? 'N/A' }}</p>
                                    </div>
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded-lg border border-blue-100 dark:border-blue-800">
                                        <p class="text-xs text-gray-500 uppercase">Est. Delivery Date</p>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ $goodsReturnRequest->replacementDelivery->expected_delivery_date?->format('d M Y') ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="flex flex-col gap-3">
                                    <a href="{{ route('procurement.gr.create', $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder) }}?is_replacement=1" 
                                       class="w-full py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-black text-sm shadow-xl shadow-primary-500/20 flex items-center justify-center gap-2">
                                        <i data-feather="plus-circle" class="w-5 h-5"></i>
                                        Log Receipt for Replacement
                                    </a>
                                    
                                    <form action="{{ route('procurement.grr.confirm-replacement-receipt', $goodsReturnRequest) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                            class="w-full py-2 bg-white dark:bg-gray-800 border border-blue-200 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-50 transition">
                                            Manual Receipt Confirmation (No Log)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @elseif($isVendor && $goodsReturnRequest->resolution_type)
                            {{-- Vendor side actions --}}
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-4 border border-blue-200 dark:border-blue-700">
                                <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center gap-2">
                                    <i data-feather="info" class="w-4 h-4"></i>
                                    <span><strong>Resolution requested:</strong> {{ $goodsReturnRequest->resolution_type_label }}</span>
                                </p>
                            </div>

                            @if($goodsReturnRequest->resolution_status === 'rejected_by_buyer' && $goodsReturnRequest->resolution_type === 'price_adjustment')
                                <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4 mb-6 border border-red-200 dark:border-red-700">
                                    <h3 class="font-semibold text-red-800 dark:text-red-300 flex items-center gap-2 mb-2">
                                        <i data-feather="alert-circle" class="w-4 h-4"></i>
                                        Debit Note Rejected
                                    </h3>
                                    <p class="text-sm text-red-700 dark:text-red-400 mb-4">The buyer has rejected your previous price adjustment. Please submit a revised Debit Note with a better offer.</p>
                                    <a href="{{ route('procurement.debit-notes.create', $goodsReturnRequest) }}"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-bold transition">
                                        <i data-feather="edit-3" class="w-4 h-4"></i> Create Revised Debit Note
                                    </a>
                                </div>
                            @elseif($goodsReturnRequest->resolution_status === 'awaiting_replacement_shipping')
                                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-6 border border-yellow-200 dark:border-yellow-700">
                                    <h3 class="font-semibold text-yellow-800 dark:text-yellow-300 flex items-center gap-2 mb-4">
                                        <i data-feather="truck" class="w-5 h-5"></i>
                                        Ship Replacement Items
                                    </h3>
                                    <form action="{{ route('procurement.grr.ship-replacement', $goodsReturnRequest) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tracking Number</label>
                                                <input type="text" name="tracking_number" 
                                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                    placeholder="e.g. JB0012345678">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Delivery Date</label>
                                                <input type="date" name="expected_delivery_date" 
                                                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                                            </div>
                                        </div>
                                        <button type="submit" 
                                            class="w-full py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                            <i data-feather="send" class="w-4 h-4"></i>
                                            Mark as Shipped
                                        </button>
                                    </form>
                                </div>
                                @elseif($goodsReturnRequest->resolution_status === 'replacement_shipped')
                                    <div class="text-center py-6">
                                        <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                            <i data-feather="send" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                                        </div>
                                        <p class="text-gray-600 dark:text-gray-400">You have shipped the replacement items. Waiting for buyer receipt confirmation.</p>
                                        
                                        <button onclick="document.getElementById('reship-form').classList.remove('hidden'); this.classList.add('hidden')" 
                                            class="mt-4 text-sm font-bold text-primary-600 hover:text-primary-700 underline flex items-center gap-1 mx-auto">
                                            <i data-feather="edit-2" class="w-3 h-3"></i> Use different tracking / Reship
                                        </button>

                                        <div id="reship-form" class="hidden mt-6 text-left bg-gray-50 dark:bg-gray-700/30 p-4 rounded-xl border border-gray-100 dark:border-gray-600">
                                            <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Update Shipping Information</h4>
                                            <form action="{{ route('procurement.grr.ship-replacement', $goodsReturnRequest) }}" method="POST" class="space-y-4">
                                                @csrf
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tracking Number</label>
                                                        <input type="text" name="tracking_number" 
                                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                            value="{{ $goodsReturnRequest->replacementDelivery?->tracking_number }}">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Delivery Date</label>
                                                        <input type="date" name="expected_delivery_date" 
                                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                                            value="{{ $goodsReturnRequest->replacementDelivery?->expected_delivery_date?->format('Y-m-d') }}">
                                                    </div>
                                                </div>
                                                <div class="flex gap-3">
                                                    <button type="submit" 
                                                        class="flex-1 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-semibold transition">
                                                        Update Info
                                                    </button>
                                                    <button type="button" onclick="document.getElementById('reship-form').classList.add('hidden'); document.querySelector('button[onclick*=\'reship-form\']').classList.remove('hidden')"
                                                        class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-600 rounded-lg">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                            @elseif($goodsReturnRequest->resolution_status === 'pending')
                                <form action="{{ route('procurement.grr.vendor-response', $goodsReturnRequest) }}" method="POST" class="space-y-4">
                                    @csrf
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                                        <textarea name="vendor_notes" rows="3"
                                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"
                                            placeholder="Add comments for the buyer..."></textarea>
                                    </div>
                                    <div class="flex gap-4">
                                        <button type="submit" name="action" value="approve"
                                            class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                            <i data-feather="check" class="w-4 h-4"></i> Approve
                                        </button>
                                        <button type="submit" name="action" value="reject"
                                            class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                            <i data-feather="x" class="w-4 h-4"></i> Reject
                                        </button>
                                    </div>
                                </form>
                            @endif
                        @elseif($goodsReturnRequest->resolution_type)
                            {{-- Fallback status display --}}
                            <div class="text-center py-6">
                                <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                    <i data-feather="clock" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Resolusi: <strong class="text-gray-900 dark:text-white">{{ $goodsReturnRequest->resolution_type_label }}</strong>
                                </p>
                                <p class="text-sm text-gray-500 mt-2">Menunggu respon selanjutnya...</p>
                            </div>
                        @else
                            <div class="text-center py-6">
                                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                    <i data-feather="user" class="w-8 h-8 text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400">Menunggu buyer memilih jenis resolusi.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                {{-- Resolved --}}
                <div class="bg-green-50 dark:bg-green-900/20 rounded-2xl p-6 border border-green-200 dark:border-green-800">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                            <i data-feather="check-circle" class="w-6 h-6 text-green-600 dark:text-green-300"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-green-800 dark:text-green-200">GRR Resolved</h3>
                            <p class="text-sm text-green-600 dark:text-green-400">
                                Diselesaikan pada {{ $goodsReturnRequest->resolved_at?->format('d M Y, H:i') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="space-y-6">
            {{-- Related Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Related Information</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Goods Receipt</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $goodsReturnRequest->goodsReceiptItem->goodsReceipt->gr_number }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Purchase Order</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder->po_number }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Buyer</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder->purchaseRequisition->company->name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Vendor</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $goodsReturnRequest->goodsReceiptItem->goodsReceipt->purchaseOrder->vendorCompany->name }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase">Reported By</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $goodsReturnRequest->createdBy->name }}</p>
                    </div>
                </div>
            </div>

            {{-- Debit Note Link --}}
            @if($goodsReturnRequest->debitNote)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Debit Note</h2>
                    </div>
                    <div class="p-6">
                        @php
                            $dnStatusColors = [
                                'pending' => 'yellow',
                                'approved' => 'green',
                                'rejected' => 'red',
                            ];
                            $dnStatusColor = $dnStatusColors[$goodsReturnRequest->debitNote->status] ?? 'gray';
                        @endphp
                        <a href="{{ route('procurement.debit-notes.show', $goodsReturnRequest->debitNote) }}"
                            class="flex flex-col p-4 bg-{{ $dnStatusColor }}-50 dark:bg-{{ $dnStatusColor }}-900/20 rounded-lg hover:shadow-md transition border border-{{ $dnStatusColor }}-200 dark:border-{{ $dnStatusColor }}-800">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-bold text-{{ $dnStatusColor }}-700 dark:text-{{ $dnStatusColor }}-300">
                                    {{ $goodsReturnRequest->debitNote->dn_number }}
                                </p>
                                <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-{{ $dnStatusColor }}-200 dark:bg-{{ $dnStatusColor }}-800 text-{{ $dnStatusColor }}-800 dark:text-{{ $dnStatusColor }}-200">
                                    {{ $goodsReturnRequest->debitNote->status }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <p class="text-xs text-{{ $dnStatusColor }}-600 dark:text-{{ $dnStatusColor }}-400">Deduction:
                                    {{ $goodsReturnRequest->debitNote->formatted_deduction_amount }}</p>
                                <i data-feather="chevron-right" class="w-4 h-4 text-{{ $dnStatusColor }}-500"></i>
                            </div>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>


    <script>
        // Initialize feather icons
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
        
        // Simple function to handle resolution selection
        window.selectResolution = function(value, label) {
            // Reset all cards
            ['price_adjustment', 'replacement', 'return_refund'].forEach(function(type) {
                var card = document.getElementById('card_' + type);
                var indicator = document.getElementById('indicator_' + type);
                if (card) {
                    card.classList.remove('border-primary-500', 'border-blue-500', 'border-red-500', 'bg-primary-50', 'bg-blue-50', 'bg-red-50', 'ring-2', 'scale-105', 'shadow-lg');
                    card.classList.add('border-gray-200');
                }
                if (indicator) {
                    indicator.classList.add('hidden');
                }
            });
            
            // Highlight selected card
            var selectedCard = document.getElementById('card_' + value);
            var selectedIndicator = document.getElementById('indicator_' + value);
            
            if (selectedCard) {
                selectedCard.classList.remove('border-gray-200');
                if (value === 'price_adjustment') {
                    selectedCard.classList.add('border-primary-500', 'bg-primary-50', 'ring-2', 'scale-105', 'shadow-lg');
                } else if (value === 'replacement') {
                    selectedCard.classList.add('border-blue-500', 'bg-blue-50', 'ring-2', 'scale-105', 'shadow-lg');
                } else {
                    selectedCard.classList.add('border-red-500', 'bg-red-50', 'ring-2', 'scale-105', 'shadow-lg');
                }
            }
            
            if (selectedIndicator) {
                selectedIndicator.classList.remove('hidden');
            }

            // Sync with radio button
            var radio = document.querySelector('input[name="resolution_type"][value="' + value + '"]');
            if (radio) radio.checked = true;
            
            // Show confirmation text
            var displayBox = document.getElementById('selectedResolutionDisplay');
            var displayText = document.getElementById('selectedResolutionText');
            if (displayBox) displayBox.classList.remove('hidden');
            if (displayText) displayText.textContent = label;
            
            // Enable submit button
            var btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = false;
                btn.classList.remove('bg-gray-300', 'cursor-not-allowed');
                btn.classList.add('bg-primary-600', 'hover:bg-primary-700', 'cursor-pointer');
            }
        };
    </script>
@endsection