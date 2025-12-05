@extends('layouts.app', [
    'title' => 'Request Details',
    'breadcrumbs' => [
        ['name' => 'All Requests', 'url' => route('procurement.pr.public-feed')],
        ['name' => 'Request #' . $purchaseRequisition->id, 'url' => '#']
    ]
])

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ url()->previous() }}"
            class="bg-white py-2 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700 transition-all">
            Back
        </a>
    </div>

    {{-- Winner Announcement --}}
    @if($purchaseRequisition->tender_status === 'awarded' && $purchaseRequisition->winningOffer)
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 border-2 border-green-200 dark:border-green-800 rounded-2xl p-6 mb-6">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <i data-feather="award" class="w-6 h-6 text-green-600 dark:text-green-400"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-bold text-green-900 dark:text-green-200 mb-1">
                        🎉 Tender Awarded
                    </h3>
                    <p class="text-sm text-green-700 dark:text-green-300">
                        This tender has been awarded to <strong class="font-bold">{{ $purchaseRequisition->winningOffer->company->name }}</strong>
                        with a total offer of <strong class="font-bold">{{ $purchaseRequisition->winningOffer->formatted_total_price }}</strong>.
                    </p>
                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">
                        Tender is now closed for new offers.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-8">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">General Information</h3>
        </div>
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Company</dt>
                    <dd class="mt-2">
                        <div class="flex items-center gap-4 p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex-shrink-0">
                                @if($purchaseRequisition->company && $purchaseRequisition->company->logo_url)
                                    <img class="h-16 w-24 rounded border border-gray-200 dark:border-gray-500 object-contain bg-white dark:bg-gray-800 p-2" src="{{ $purchaseRequisition->company->logo_url }}" alt="{{ $purchaseRequisition->company->name }}">
                                @else
                                    <div class="h-16 w-24 rounded border border-gray-200 dark:border-gray-500 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-sm font-bold text-gray-400 dark:text-gray-500">{{ $purchaseRequisition->company ? strtoupper(substr($purchaseRequisition->company->name, 0, 3)) : 'LOGO' }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <div class="text-base font-bold text-gray-900 dark:text-white">{{ $purchaseRequisition->company ? $purchaseRequisition->company->name : 'N/A' }}</div>
                                <div class="mt-1 space-y-0.5">
                                    @if($purchaseRequisition->company && $purchaseRequisition->company->email)
                                        <div class="text-xs text-gray-600 dark:text-gray-400 flex items-center">
                                            <i data-feather="mail" class="w-3 h-3 mr-1"></i>
                                            {{ $purchaseRequisition->company->email }}
                                        </div>
                                    @endif
                                    @if($purchaseRequisition->company && $purchaseRequisition->company->category)
                                        <div class="text-xs text-gray-500 dark:text-gray-400">
                                            <span class="px-2 py-0.5 bg-gray-200 dark:bg-gray-600 rounded">{{ ucfirst($purchaseRequisition->company->category) }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Title</dt>
                    <dd class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ $purchaseRequisition->title }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchaseRequisition->description ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                    <dd class="mt-1">
                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-bold rounded-full 
                            @if($purchaseRequisition->status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                            @elseif($purchaseRequisition->status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                            @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400 @endif">
                            {{ ucfirst($purchaseRequisition->status) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                    <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $purchaseRequisition->created_at->format('M d, Y H:i') }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Requested By</dt>
                    <dd class="mt-1">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8">
                                @if($purchaseRequisition->user->userDetail && $purchaseRequisition->user->userDetail->profile_photo_url)
                                    <img class="h-8 w-8 rounded-lg object-cover" src="{{ $purchaseRequisition->user->userDetail->profile_photo_url }}" alt="{{ $purchaseRequisition->user->name }}">
                                @else
                                    <div class="h-8 w-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold text-xs">
                                        {{ substr($purchaseRequisition->user->name, 0, 2) }}
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $purchaseRequisition->user->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $purchaseRequisition->user->email }}</div>
                            </div>
                        </div>
                    </dd>
                </div>
            </dl>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-8">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Items</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">SKU</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($purchaseRequisition->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                {{ $item->catalogueItem->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->catalogueItem->sku }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                Rp {{ number_format($item->price, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-bold">
                                Rp {{ number_format($item->quantity * $item->price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-right text-sm font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Grand Total</td>
                        <td class="px-6 py-4 text-left text-base font-bold text-primary-600 dark:text-primary-400">
                            Rp {{ number_format($purchaseRequisition->items->sum(fn($i) => $i->quantity * $i->price), 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Submit Offer Section --}}
    @php
        $selectedCompanyId = session('selected_company_id');
        $canSubmitOffer = Auth::check() && 
                         $selectedCompanyId &&
                         $selectedCompanyId != $purchaseRequisition->company_id &&
                         $purchaseRequisition->tender_status === 'open';
        
        $hasExistingOffer = Auth::check() && 
                           $selectedCompanyId &&
                           $purchaseRequisition->offers()
                               ->where('company_id', $selectedCompanyId)
                               ->exists();
    @endphp

    @if($canSubmitOffer && !$hasExistingOffer)
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-8">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-primary-50/50 dark:bg-primary-900/10">
                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">
                    <i data-feather="tag" class="w-5 h-5 inline mr-2 text-primary-600 dark:text-primary-400"></i>
                    Submit Your Offer
                </h3>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Provide your pricing and quantities for this procurement request</p>
            </div>

            <form action="{{ route('procurement.offers.store', $purchaseRequisition) }}" method="POST" id="offerForm" enctype="multipart/form-data">
                @csrf
                <div class="px-6 py-5">
                    {{-- Error Alert --}}
                    @if($errors->any())
                        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div class="flex items-start gap-3">
                                <i data-feather="alert-circle" class="w-5 h-5 text-red-600 dark:text-red-400 flex-shrink-0 mt-0.5"></i>
                                <div class="flex-1">
                                    <h3 class="text-sm font-bold text-red-900 dark:text-red-200 mb-1">Validation Errors</h3>
                                    <ul class="list-disc list-inside space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li class="text-sm text-red-700 dark:text-red-300">{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Items Table --}}
                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Product</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Requested Qty</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Your Qty</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Unit Price (Rp)</th>
                                    <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($purchaseRequisition->items as $index => $item)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 offer-item-row">
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->catalogueItem->name }}</div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">SKU: {{ $item->catalogueItem->sku }}</div>
                                            <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->id }}">
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $item->quantity }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="number" 
                                                   name="items[{{ $index }}][quantity_offered]" 
                                                   class="offer-quantity block w-24 rounded-lg border @error('items.'.$index.'.quantity_offered') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white" 
                                                   min="1" 
                                                   value="{{ old('items.'.$index.'.quantity_offered', $item->quantity) }}"
                                                   required
                                                   data-row="{{ $index }}">
                                            @error('items.'.$index.'.quantity_offered')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="number" 
                                                   name="items[{{ $index }}][unit_price]" 
                                                   class="offer-price block w-32 rounded-lg border @error('items.'.$index.'.unit_price') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror px-3 py-2 text-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white" 
                                                   min="0" 
                                                   step="0.01"
                                                   placeholder="0.00"
                                                   value="{{ old('items.'.$index.'.unit_price') }}"
                                                   required
                                                   data-row="{{ $index }}">
                                            @error('items.'.$index.'.unit_price')
                                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="offer-subtotal text-sm font-bold text-gray-900 dark:text-white" data-row="{{ $index }}">Rp 0.00</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50 dark:bg-gray-700/50">
                                <tr>
                                    <td colspan="4" class="px-4 py-4 text-right text-sm font-bold text-gray-700 dark:text-gray-300 uppercase">Total Offer Price</td>
                                    <td class="px-4 py-4">
                                        <span id="totalOfferPrice" class="text-lg font-bold text-primary-600 dark:text-primary-400">Rp 0.00</span>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Supporting Documents Upload --}}
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-feather="paperclip" class="w-4 h-4 inline mr-1"></i>
                            Supporting Documents (Optional)
                        </label>
                        <input type="file" 
                               name="documents[]" 
                               id="documents" 
                               multiple 
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png"
                               class="block w-full text-sm text-gray-900 dark:text-gray-300 border @error('documents') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-l-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 dark:file:bg-primary-900/30 dark:file:text-primary-400">
                        @error('documents')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        @error('documents.*')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <i data-feather="info" class="w-3 h-3 inline mr-1"></i>
                            Upload up to 5 files (PDF, DOC, DOCX, XLS, XLSX, JPG, PNG). Max 10MB per file.
                        </p>
                        {{-- File Preview --}}
                        <div id="filePreview" class="mt-3 space-y-2 hidden"></div>
                    </div>

                    {{-- Proposal Notes --}}
                    <div class="mb-6">
                        <label for="notes" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">
                            <i data-feather="file-text" class="w-4 h-4 inline mr-1"></i>
                            Proposal Notes (Optional)
                        </label>
                        <textarea name="notes" 
                                  id="notes" 
                                  rows="4" 
                                  placeholder="Describe your offer, delivery terms, warranty, or any special conditions..."
                                  class="block w-full rounded-lg border @error('notes') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"
                                  maxlength="2000">{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum 2000 characters</p>
                    </div>

                    {{-- Submit Button --}}
                    <div class="flex items-center justify-between">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <i data-feather="info" class="w-4 h-4 inline mr-1"></i>
                            Your offer will be ranked and compared with others
                        </p>
                        <button type="submit" 
                                class="px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-lg transition shadow-sm hover:shadow flex items-center gap-2">
                            <i data-feather="send" class="w-4 h-4"></i>
                            Submit Offer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    @elseif($hasExistingOffer)
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-6 mb-8">
            <div class="flex items-start gap-3">
                <i data-feather="check-circle" class="w-6 h-6 text-blue-600 dark:text-blue-400 flex-shrink-0"></i>
                <div>
                    <h3 class="text-base font-bold text-blue-900 dark:text-blue-200">Offer Already Submitted</h3>
                    <p class="text-sm text-blue-700 dark:text-blue-300 mt-1">Your company has already submitted an offer for this requisition.</p>
                    <a href="{{ route('procurement.offers.my') }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 mt-2 inline-flex items-center gap-1">
                        View My Offers
                        <i data-feather="arrow-right" class="w-3 h-3"></i>
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Supporting Documents Section --}}
    @if($purchaseRequisition->documents->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-8">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Supporting Documents</h3>
            </div>
            <div class="px-6 py-4 space-y-3">
                @foreach($purchaseRequisition->documents as $doc)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-primary-300 dark:hover:border-primary-600 transition group">
                        <div class="flex items-center gap-4 flex-1 min-w-0">
                            <div class="flex-shrink-0 w-12 h-12 bg-white dark:bg-gray-800 rounded-lg flex items-center justify-center border border-gray-200 dark:border-gray-600">
                                @if(str_contains($doc->file_type, 'pdf'))
                                    <i data-feather="file-text" class="w-6 h-6 text-red-500"></i>
                                @elseif(str_contains($doc->file_type, 'word'))
                                    <i data-feather="file-text" class="w-6 h-6 text-blue-500"></i>
                                @elseif(str_contains($doc->file_type, 'sheet') || str_contains($doc->file_type, 'excel'))
                                    <i data-feather="file-text" class="w-6 h-6 text-green-500"></i>
                                @elseif(str_contains($doc->file_type, 'image'))
                                    <i data-feather="image" class="w-6 h-6 text-purple-500"></i>
                                @else
                                    <i data-feather="file" class="w-6 h-6 text-gray-500"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ $doc->original_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $doc->formatted_size }} • Uploaded by {{ $doc->uploader->name }}</p>
                            </div>
                        </div>
                        <a href="{{ route('procurement.pr.download-document', $doc) }}" class="flex-shrink-0 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition flex items-center gap-2">
                            <i data-feather="download" class="w-4 h-4"></i>
                            Download
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Comments Section --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Questions & Comments ({{ $purchaseRequisition->comments->count() }})</h3>
        </div>
        
        {{-- Comment Form --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
            <form action="{{ route('procurement.pr.add-comment', $purchaseRequisition) }}" method="POST">
                @csrf
                <div class="flex gap-3">
                    @if(Auth::user()->userDetail && Auth::user()->userDetail->profile_photo_url)
                        <img src="{{ Auth::user()->userDetail->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                    @else
                        <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold text-xs flex-shrink-0">
                            {{ substr(Auth::user()->name, 0, 2) }}
                        </div>
                    @endif
                    <div class="flex-1">
                        <textarea name="comment" id="comment" rows="3" required placeholder="Ask a question or leave a comment..." class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"></textarea>
                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition">
                                <i data-feather="send" class="w-4 h-4 inline mr-1"></i>
                                Post Comment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Comments List --}}
        <div class="px-6 py-4 space-y-6">
            @forelse($purchaseRequisition->comments as $comment)
                <div class="comment-item">
                    {{-- Main Comment --}}
                    <div class="flex gap-3">
                        @if($comment->user->userDetail && $comment->user->userDetail->profile_photo_url)
                            <img src="{{ $comment->user->userDetail->profile_photo_url }}" alt="{{ $comment->user->name }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold text-xs flex-shrink-0">
                                {{ substr($comment->user->name, 0, 2) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $comment->user->name }}</p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">•</span>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{!! preg_replace('/@(\w+)/', '<span class="text-primary-600 dark:text-primary-400 font-semibold">@$1</span>', e($comment->comment)) !!}</p>
                            
                            {{-- Reply Button --}}
                            <a href="javascript:void(0)" onclick="toggleReplyForm('{{ $comment->id }}')" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 inline-flex items-center gap-1 cursor-pointer">
                                <i data-feather="corner-down-right" class="w-3 h-3"></i>
                                Reply
                            </a>

                            {{-- Reply Form (Hidden by default) --}}
                            <div id="reply-form-{{ $comment->id }}" class="hidden mt-3">
                                <form action="{{ route('procurement.pr.add-comment', $purchaseRequisition) }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    @if(Auth::user()->userDetail && Auth::user()->userDetail->profile_photo_url)
                                        <img src="{{ Auth::user()->userDetail->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold text-xs flex-shrink-0">
                                            {{ substr(Auth::user()->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <textarea name="comment" rows="2" required placeholder="Write a reply..." class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-xs shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white"></textarea>
                                        <div class="mt-2 flex gap-2 justify-end">
                                            <button type="button" onclick="toggleReplyForm('{{ $comment->id }}')" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-lg transition">
                                                Cancel
                                            </button>
                                            <button type="submit" class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition">
                                                Post Reply
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Nested Replies --}}
                    @if($comment->replies->count() > 0)
                        <div class="ml-12 mt-4 space-y-4 border-l-2 border-gray-200 dark:border-gray-700 pl-4">
                            @foreach($comment->replies as $reply)
                                <div>
                                    <div class="flex gap-3">
                                        @if($reply->user->userDetail && $reply->user->userDetail->profile_photo_url)
                                            <img src="{{ $reply->user->userDetail->profile_photo_url }}" alt="{{ $reply->user->name }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-8 h-8 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-gray-600 dark:text-gray-400 font-bold text-xs flex-shrink-0">
                                                {{ substr($reply->user->name, 0, 2) }}
                                            </div>
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $reply->user->name }}</p>
                                                <span class="text-xs text-gray-500 dark:text-gray-400">•</span>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $reply->created_at->diffForHumans() }}</p>
                                            </div>
                                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-2">{!! preg_replace('/@(\w+)/', '<span class="text-primary-600 dark:text-primary-400 font-semibold">@$1</span>', e($reply->comment)) !!}</p>
                                            
                                            {{-- Reply Button --}}
                                            <a href="javascript:void(0)" onclick="toggleReplyForm('reply-{{ $reply->id }}')" class="text-xs font-semibold text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 inline-flex items-center gap-1 cursor-pointer">
                                                <i data-feather="corner-down-right" class="w-3 h-3"></i>
                                                Reply
                                            </a>

                                            {{-- Individual Reply Form for this nested reply --}}
                                            <div id="reply-form-reply-{{ $reply->id }}" class="hidden mt-3">
                                                <form action="{{ route('procurement.pr.add-comment', $purchaseRequisition) }}" method="POST" class="flex gap-2">
                                                    @csrf
                                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                    @if(Auth::user()->userDetail && Auth::user()->userDetail->profile_photo_url)
                                                        <img src="{{ Auth::user()->userDetail->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-8 h-8 rounded-lg object-cover flex-shrink-0">
                                                    @else
                                                        <div class="w-8 h-8 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold text-xs flex-shrink-0">
                                                            {{ substr(Auth::user()->name, 0, 2) }}
                                                        </div>
                                                    @endif
                                                    <div class="flex-1">
                                                        <textarea name="comment" rows="2" required placeholder="Write a reply..." class="block w-full rounded-lg border border-gray-300 dark:border-gray-600 px-3 py-2 text-xs shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-white reply-textarea" data-mention="&#64;{{ $reply->user->name }}"></textarea>
                                                        <div class="mt-2 flex gap-2 justify-end">
                                                            <button type="button" onclick="toggleReplyForm('reply-{{ $reply->id }}')" class="px-3 py-1.5 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 text-xs font-semibold rounded-lg transition">
                                                                Cancel
                                                            </button>
                                                            <button type="submit" class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white text-xs font-semibold rounded-lg transition">
                                                                Post Reply
                                                            </button>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-8">
                    <i data-feather="message-circle" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No comments yet. Be the first to ask a question!</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
        <script>
            function toggleReplyForm(formId) {
                const form = document.getElementById('reply-form-' + formId);
                
                if (form.classList.contains('hidden')) {
                    // Hide all other reply forms first
                    document.querySelectorAll('[id^="reply-form-"]').forEach(f => {
                        f.classList.add('hidden');
                        // Clear textarea when hiding
                        const textarea = f.querySelector('textarea');
                        if (textarea) {
                            textarea.value = '';
                        }
                    });
                    
                    form.classList.remove('hidden');
                    
                    // Auto-fill with mention if available
                    const textarea = form.querySelector('textarea');
                    const mention = textarea.getAttribute('data-mention');
                    if (mention) {
                        textarea.value = mention + ' ';
                        // Move cursor to end
                        textarea.setSelectionRange(textarea.value.length, textarea.value.length);
                    }
                    textarea.focus();
                } else {
                    form.classList.add('hidden');
                    // Clear textarea
                    const textarea = form.querySelector('textarea');
                    if (textarea) {
                        textarea.value = '';
                    }
                }
                
                feather.replace();
            }

            // Offer Form Auto-calculation
            function calculateOfferSubtotal(row) {
                const quantityInput = document.querySelector(`.offer-quantity[data-row="${row}"]`);
                const priceInput = document.querySelector(`.offer-price[data-row="${row}"]`);
                const subtotalSpan = document.querySelector(`.offer-subtotal[data-row="${row}"]`);

                if (quantityInput && priceInput && subtotalSpan) {
                    const quantity = parseFloat(quantityInput.value) || 0;
                    const price = parseFloat(priceInput.value) || 0;
                    const subtotal = quantity * price;

                    subtotalSpan.textContent = 'Rp ' + subtotal.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    calculateOfferTotal();
                }
            }

            function calculateOfferTotal() {
                let total = 0;
                document.querySelectorAll('.offer-item-row').forEach((row, index) => {
                    const quantityInput = document.querySelector(`.offer-quantity[data-row="${index}"]`);
                    const priceInput = document.querySelector(`.offer-price[data-row="${index}"]`);

                    if (quantityInput && priceInput) {
                        const quantity = parseFloat(quantityInput.value) || 0;
                        const price = parseFloat(priceInput.value) || 0;
                        total += quantity * price;
                    }
                });

                const totalElement = document.getElementById('totalOfferPrice');
                if (totalElement) {
                    totalElement.textContent = 'Rp ' + total.toLocaleString('id-ID', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });
                }
            }

            // Attach event listeners to offer inputs
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.offer-quantity, .offer-price').forEach(input => {
                    input.addEventListener('input', function() {
                        const row = this.getAttribute('data-row');
                        calculateOfferSubtotal(row);
                    });

                    // Trigger initial calculation
                    const row = input.getAttribute('data-row');
                    calculateOfferSubtotal(row);
                });

                feather.replace();
            });
        </script>
    @endpush
@endsection