@extends('layouts.app', ['title' => 'Create Debit Note'])

@section('content')
    <div class="max-w-3xl mx-auto">
        {{-- Header --}}
        <div class="flex items-center gap-4 mb-6">
            <a href="{{ route('procurement.grr.show', $goodsReturnRequest) }}"
                class="p-2 rounded-lg bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                <i data-feather="arrow-left" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Buat Debit Note</h1>
                <p class="text-gray-500 dark:text-gray-400">Penyesuaian harga untuk GRR:
                    {{ $goodsReturnRequest->grr_number }}</p>
            </div>
        </div>

        <form action="{{ route('procurement.debit-notes.store', $goodsReturnRequest) }}" method="POST">
            @csrf

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Detail Item</h2>
                </div>
                <div class="p-6">
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">
                                    {{ $goodsReturnRequest->goodsReceiptItem->purchaseOrderItem->purchaseRequisitionItem->catalogueItem->name ?? '-' }}
                                </p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $goodsReturnRequest->quantity_affected }} unit @
                                    Rp
                                    {{ number_format($goodsReturnRequest->goodsReceiptItem->purchaseOrderItem->unit_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 uppercase">Nilai Original</p>
                                <p class="text-xl font-bold text-gray-900 dark:text-white">Rp
                                    {{ number_format($originalAmount, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Persentase Potongan (%)
                            </label>
                            <div class="flex gap-4">
                                @foreach([10, 20, 30, 50] as $percent)
                                    <label class="cursor-pointer flex-1">
                                        <input type="radio" name="deduction_percentage" value="{{ $percent }}"
                                            class="peer hidden"
                                            onchange="calculateDeduction({{ $originalAmount }}, {{ $percent }})">
                                        <div
                                            class="border-2 border-gray-200 dark:border-gray-600 rounded-lg p-3 text-center peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 transition">
                                            <span class="font-bold text-lg text-gray-900 dark:text-white">{{ $percent }}%</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="text-center text-gray-500">— atau —</div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Nominal Potongan Manual
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="number" name="deduction_amount" id="deduction_amount"
                                    class="w-full pl-12 pr-4 py-3 rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary-500 focus:border-primary-500"
                                    placeholder="0" min="0" max="{{ $originalAmount }}"
                                    oninput="updateSummary({{ $originalAmount }})">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Alasan Potongan
                            </label>
                            <textarea name="reason" rows="3"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary-500 focus:border-primary-500"
                                placeholder="Jelaskan alasan potongan harga...">{{ $goodsReturnRequest->issue_description }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Summary --}}
            <div class="bg-primary-50 dark:bg-primary-900/20 rounded-xl p-6 mb-6">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-4">Ringkasan</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Nilai Original</span>
                        <span class="font-medium text-gray-900 dark:text-white">Rp
                            {{ number_format($originalAmount, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-red-600">
                        <span>Potongan</span>
                        <span class="font-medium" id="display_deduction">- Rp 0</span>
                    </div>
                    <hr class="border-gray-300 dark:border-gray-600">
                    <div class="flex justify-between text-lg">
                        <span class="font-semibold text-gray-900 dark:text-white">Nilai Akhir</span>
                        <span class="font-bold text-primary-600" id="display_final">Rp
                            {{ number_format($originalAmount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full py-4 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-bold text-lg transition">
                <i data-feather="file-text" class="w-5 h-5 inline mr-2"></i>
                Buat Debit Note
            </button>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        feather.replace();

        function calculateDeduction(original, percentage) {
            const deduction = original * (percentage / 100);
            document.getElementById('deduction_amount').value = Math.round(deduction);
            updateSummary(original);
        }

        function updateSummary(original) {
            const deduction = parseFloat(document.getElementById('deduction_amount').value) || 0;
            const final = original - deduction;

            document.getElementById('display_deduction').textContent = '- Rp ' + deduction.toLocaleString('id-ID');
            document.getElementById('display_final').textContent = 'Rp ' + final.toLocaleString('id-ID');
        }
    </script>
@endpush