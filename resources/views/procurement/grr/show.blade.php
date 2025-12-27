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
                    'resolved' => 'green',
                ];
                $statusColor = $statusColors[$goodsReturnRequest->resolution_status] ?? 'gray';
            @endphp
            <span
                class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-{{ $statusColor }}-100 text-{{ $statusColor }}-800 dark:bg-{{ $statusColor }}-900/30 dark:text-{{ $statusColor }}-400">
                @if($goodsReturnRequest->resolution_status === 'pending')
                    <i data-feather="clock" class="w-4 h-4 mr-2"></i> Pending
                @elseif($goodsReturnRequest->resolution_status === 'approved_by_vendor')
                    <i data-feather="check" class="w-4 h-4 mr-2"></i> Disetujui Vendor
                @elseif($goodsReturnRequest->resolution_status === 'rejected_by_vendor')
                    <i data-feather="x" class="w-4 h-4 mr-2"></i> Ditolak Vendor
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
                        Detail Masalah
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase">Jenis Masalah</p>
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
                            <p class="text-xs font-medium text-gray-500 uppercase">Jumlah Bermasalah</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">
                                {{ $goodsReturnRequest->quantity_affected }} unit</p>
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
                            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Deskripsi Masalah</p>
                            <p class="text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                {{ $goodsReturnRequest->issue_description }}
                            </p>
                        </div>
                    @endif

                    {{-- Photo Evidence --}}
                    @if($goodsReturnRequest->photo_evidence && count($goodsReturnRequest->photo_evidence) > 0)
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase mb-2">Bukti Foto</p>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($goodsReturnRequest->photo_evidence as $photo)
                                    <img src="{{ asset('storage/' . $photo) }}" alt="Evidence"
                                        class="rounded-lg object-cover h-24 w-full">
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
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Resolusi</h2>
                    </div>
                    <div class="p-6">
                        @if($isBuyer && $goodsReturnRequest->resolution_status === 'pending')
                            {{-- Buyer selects resolution type --}}
                            <form action="{{ route('procurement.grr.update-resolution', $goodsReturnRequest) }}" method="POST" id="resolutionForm">
                                @csrf
                                @method('PUT')
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Pilih jenis resolusi yang diinginkan:</p>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                    <label class="cursor-pointer resolution-option" onclick="selectResolution('price_adjustment', 'Penyesuaian Harga')">
                                        <input type="radio" name="resolution_type" value="price_adjustment" class="sr-only" required>
                                        <div id="card_price_adjustment" class="border-2 border-gray-200 dark:border-gray-600 rounded-xl p-4 text-center 
                                                    hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-200 relative">
                                            <div id="indicator_price_adjustment" class="hidden absolute top-2 right-2 w-6 h-6 bg-primary-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <i data-feather="dollar-sign" class="w-8 h-8 mx-auto mb-2 text-primary-500"></i>
                                            <p class="font-semibold text-gray-900 dark:text-white">Penyesuaian Harga</p>
                                            <p class="text-xs text-gray-500 mt-1">Terima barang dengan potongan harga</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer resolution-option" onclick="selectResolution('replacement', 'Penggantian Unit')">
                                        <input type="radio" name="resolution_type" value="replacement" class="sr-only">
                                        <div id="card_replacement" class="border-2 border-gray-200 dark:border-gray-600 rounded-xl p-4 text-center 
                                                    hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-200 relative">
                                            <div id="indicator_replacement" class="hidden absolute top-2 right-2 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <i data-feather="refresh-cw" class="w-8 h-8 mx-auto mb-2 text-blue-500"></i>
                                            <p class="font-semibold text-gray-900 dark:text-white">Penggantian Unit</p>
                                            <p class="text-xs text-gray-500 mt-1">Vendor kirim unit baru</p>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer resolution-option" onclick="selectResolution('return_refund', 'Return & Refund')">
                                        <input type="radio" name="resolution_type" value="return_refund" class="sr-only">
                                        <div id="card_return_refund" class="border-2 border-gray-200 dark:border-gray-600 rounded-xl p-4 text-center 
                                                    hover:border-gray-300 dark:hover:border-gray-500 transition-all duration-200 relative">
                                            <div id="indicator_return_refund" class="hidden absolute top-2 right-2 w-6 h-6 bg-red-500 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            </div>
                                            <i data-feather="rotate-ccw" class="w-8 h-8 mx-auto mb-2 text-red-500"></i>
                                            <p class="font-semibold text-gray-900 dark:text-white">Return & Refund</p>
                                            <p class="text-xs text-gray-500 mt-1">Kembalikan barang, refund penuh</p>
                                        </div>
                                    </label>
                                </div>
                                
                                {{-- Selected Resolution Display --}}
                                <div id="selectedResolutionDisplay" class="hidden mb-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                                    <p class="text-sm text-green-700 dark:text-green-300 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        <span>Anda memilih: <strong id="selectedResolutionText"></strong></span>
                                    </p>
                                </div>
                                
                                <button type="submit" id="submitBtn"
                                    class="w-full py-3 bg-gray-300 cursor-not-allowed text-white rounded-lg font-semibold transition" disabled>
                                    Kirim Permintaan ke Vendor
                                </button>
                            </form>
                        @elseif($isVendor && $goodsReturnRequest->resolution_type && $goodsReturnRequest->resolution_status === 'pending')
                            {{-- Vendor responds --}}
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-4 border border-blue-200 dark:border-blue-700">
                                <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center gap-2">
                                    <i data-feather="info" class="w-4 h-4"></i>
                                    <span><strong>Permintaan Resolusi:</strong> {{ $goodsReturnRequest->resolution_type_label }}</span>
                                </p>
                            </div>
                            <form action="{{ route('procurement.grr.vendor-response', $goodsReturnRequest) }}" method="POST"
                                class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan
                                        (Opsional)</label>
                                    <textarea name="vendor_notes" rows="3"
                                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white focus:ring-primary-500 focus:border-primary-500"></textarea>
                                </div>
                                <div class="flex gap-4">
                                    <button type="submit" name="action" value="approve"
                                        class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                        <i data-feather="check" class="w-4 h-4"></i> Setuju
                                    </button>
                                    <button type="submit" name="action" value="reject"
                                        class="flex-1 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition flex items-center justify-center gap-2">
                                        <i data-feather="x" class="w-4 h-4"></i> Tolak
                                    </button>
                                </div>
                            </form>
                        @elseif($goodsReturnRequest->resolution_type)
                            <div class="text-center py-6">
                                <div class="w-16 h-16 mx-auto mb-4 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                                    <i data-feather="clock" class="w-8 h-8 text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">
                                    Resolusi: <strong class="text-gray-900 dark:text-white">{{ $goodsReturnRequest->resolution_type_label }}</strong>
                                </p>
                                <p class="text-sm text-gray-500 mt-2">Menunggu respon dari vendor...</p>
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
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Terkait</h2>
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
                        <p class="text-xs font-medium text-gray-500 uppercase">Dilaporkan Oleh</p>
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
                        <a href="{{ route('procurement.debit-notes.show', $goodsReturnRequest->debitNote) }}"
                            class="flex items-center justify-between p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg hover:bg-primary-100 dark:hover:bg-primary-900/30 transition border border-primary-200 dark:border-primary-800">
                            <div>
                                <p class="font-semibold text-primary-700 dark:text-primary-300">
                                    {{ $goodsReturnRequest->debitNote->dn_number }}</p>
                                <p class="text-sm text-primary-600 dark:text-primary-400">Potongan:
                                    {{ $goodsReturnRequest->debitNote->formatted_deduction_amount }}</p>
                            </div>
                            <i data-feather="chevron-right" class="w-5 h-5 text-primary-500"></i>
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