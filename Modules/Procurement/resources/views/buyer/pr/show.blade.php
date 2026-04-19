@extends('layouts.app', [
    'title' => 'Purchase Requisition Detail',
    'breadcrumbs' => [
        ['name' => 'Maps', 'url' => '#'],
        ['name' => 'Requisitions', 'url' => route('procurement.pr.index')],
        ['name' => $purchaseRequisition->pr_number ?: 'REQ-' . $purchaseRequisition->id, 'url' => '#']
    ]
])

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $purchaseRequisition->pr_number ?: 'REQ-' . $purchaseRequisition->id }}</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">{{ $purchaseRequisition->created_at->format('M d, Y') }}</span>
            </div>
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $purchaseRequisition->title }}</h1>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('procurement.pr.index') }}"
                class="px-5 py-2.5 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 rounded-xl text-[11px] font-black text-gray-500 uppercase tracking-widest hover:bg-gray-50 transition-all">
                Close
            </a>
            @if($purchaseRequisition->approval_status === 'draft' || $purchaseRequisition->approval_status === 'rejected')
                <button onclick="document.getElementById('editPrForm').submit()" class="px-5 py-2.5 bg-primary-600 text-white rounded-xl text-[11px] font-black uppercase tracking-widest shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all">
                    Edit Request
                </button>
            @endif
        </div>
    </div>

    {{-- The Map: Progress Stepper --}}
    {{-- The Map: Progress Stepper --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 mb-6 shadow-sm">
        <div class="flex items-center justify-between relative max-w-4xl mx-auto">
            {{-- Background Lines --}}
            <div class="absolute left-0 top-[18px] w-full h-0.5 bg-gray-100 dark:bg-gray-700 -z-0"></div>
            <div class="absolute left-0 top-[18px] h-0.5 bg-primary-600 transition-all duration-1000 -z-0" 
                style="width: {{ 
                    $purchaseRequisition->status === 'ordered' ? '100%' : 
                    ($purchaseRequisition->status === 'awarded' ? '75%' : 
                    ($purchaseRequisition->status === 'open' ? '50%' : 
                    ($purchaseRequisition->approval_status === 'approved' ? '50%' : 
                    (str_starts_with($purchaseRequisition->approval_status, 'pending') ? '25%' : '0%')))) 
                }}"></div>
            
            @php
                $steps = [
                    ['id' => 'draft', 'label' => 'DRAFT', 'icon' => 'edit-3', 'active' => true],
                    ['id' => 'approval', 'label' => 'APPROVAL', 'icon' => 'shield', 'active' => !str_starts_with($purchaseRequisition->approval_status, 'draft')],
                    ['id' => 'open', 'label' => 'OPEN', 'icon' => 'box', 'active' => $purchaseRequisition->approval_status === 'approved' || in_array($purchaseRequisition->status, ['open', 'awarded', 'ordered'])],
                    ['id' => 'awarded', 'label' => 'AWARDED', 'icon' => 'award', 'active' => in_array($purchaseRequisition->status, ['awarded', 'ordered'])],
                    ['id' => 'ordered', 'label' => 'ORDERED', 'icon' => 'shopping-bag', 'active' => $purchaseRequisition->status === 'ordered'],
                ];
            @endphp

            @foreach($steps as $index => $step)
                <div class="relative z-10 flex flex-col items-center group">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center transition-all duration-500 border-2 border-white dark:border-gray-800
                        {{ $step['active'] ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/30' : 'bg-gray-50 dark:bg-gray-700 text-gray-300' }}">
                        <i data-feather="{{ $step['icon'] }}" class="w-3.5 h-3.5"></i>
                    </div>
                    <span class="absolute top-11 text-[9px] font-bold tracking-widest transition-colors duration-500 {{ $step['active'] ? 'text-primary-600' : 'text-gray-400' }}">
                        {{ $step['label'] }}
                    </span>
                    @if($step['active'] && $loop->remaining > 0 && !$steps[$index+1]['active'])
                         <div class="absolute -right-3 top-3">
                             <span class="flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary-500"></span>
                             </span>
                         </div>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="mt-14 text-center">
            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.3em]">Lifecycle Terminal</p>
        </div>
    </div>

    {{-- Approval Actions Section --}}
    @php
        $currentUser = Auth::user();
        $companyId = $purchaseRequisition->company_id;
        $isCreator = $purchaseRequisition->user_id === $currentUser->id;
        
        $canApprove = str_starts_with($purchaseRequisition->approval_status, 'pending') && $currentUser->hasCompanyPermission($companyId, 'approve pr');
    @endphp

    @if($canApprove)
        <div class="relative overflow-hidden group mb-8">
            {{-- Premium Glassmorphism Card --}}
            <div class="bg-white/70 dark:bg-gray-800/70 backdrop-blur-xl rounded-2xl border border-yellow-200/50 dark:border-yellow-900/30 p-6 md:p-8 shadow-2xl shadow-yellow-500/5 transition-all duration-700 hover:shadow-yellow-500/10 relative z-10">
                <div class="flex flex-col md:flex-row items-start gap-6">
                    {{-- Pulsing Icon Box --}}
                    <div class="relative">
                        <div class="w-12 h-12 bg-gradient-to-br from-yellow-100 to-yellow-200 dark:from-yellow-900/30 dark:to-yellow-800/20 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg shadow-yellow-500/10">
                            <i data-feather="shield" class="w-6 h-6 text-yellow-600 dark:text-yellow-500"></i>
                        </div>
                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-yellow-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-yellow-500"></span>
                        </span>
                    </div>

                    <div class="flex-1 w-full">
                        <div class="mb-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white uppercase tracking-tight mb-1">Approval Required</h3>
                            <div class="flex items-center gap-2">
                                <div class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></div>
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-[0.2em]">Awaiting Management Confirmation</p>
                            </div>
                        </div>
                        
                        <form action="{{ route('procurement.pr.approve', $purchaseRequisition) }}" method="POST" class="max-w-3xl" onsubmit="return handlePrFormSubmit(this)">
                            @csrf
                            <div class="relative mb-6 group/input">
                                <textarea name="approval_notes" rows="2" 
                                    placeholder="Add constructive notes for the requestor..." 
                                    class="w-full bg-gray-50/50 dark:bg-gray-900/50 rounded-2xl border border-gray-100 dark:border-gray-800 p-4 text-sm font-semibold text-gray-900 dark:text-white placeholder:text-gray-300 dark:placeholder:text-gray-600 transition-all focus:border-primary-500/50 focus:ring-4 focus:ring-primary-500/5 outline-none resize-none shadow-inner"
                                ></textarea>
                                <div class="absolute bottom-3 right-4 pointer-events-none">
                                    <i data-feather="edit-3" class="w-3.5 h-3.5 text-gray-200 group-focus-within/input:text-primary-400 transition-colors"></i>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-3">
                                <button type="submit" class="flex-1 md:flex-none px-8 py-3.5 bg-gradient-to-r from-primary-600 to-primary-700 text-white font-bold text-[10px] uppercase tracking-[0.2em] rounded-xl shadow-xl shadow-primary-500/20 hover:shadow-primary-500/40 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center justify-center gap-2">
                                    <i data-feather="check-circle" class="w-3.5 h-3.5"></i>
                                    Approve Request
                                </button>
                                <button type="button" 
                                    onclick="document.getElementById('rejectModal').classList.remove('hidden')" 
                                    class="flex-1 md:flex-none px-6 py-3.5 bg-white dark:bg-gray-800 border-2 border-red-50 dark:border-red-900/10 text-red-600 font-bold text-[10px] uppercase tracking-[0.2em] rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 active:scale-95 transition-all flex items-center justify-center gap-2"
                                >
                                    <i data-feather="x-circle" class="w-3.5 h-3.5"></i>
                                    Decline Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(($purchaseRequisition->approval_status === 'draft' || $purchaseRequisition->approval_status === 'rejected') && ($isCreator || Auth::user()->hasCompanyPermission($companyId, 'create pr')))
        <div class="bg-primary-600 rounded-2xl p-8 mb-8 text-white relative overflow-hidden group">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="max-w-xl">
                    <h3 class="text-xl font-bold uppercase tracking-tight mb-2">Submit for Final Review</h3>
                    <p class="text-primary-100 text-xs opacity-90 leading-relaxed font-bold">Your request is ready. Once submitted, it will be entered into the queue for management approval.</p>
                </div>
                <form action="{{ route('procurement.pr.submit-approval', $purchaseRequisition) }}" method="POST" onsubmit="return handlePrFormSubmit(this)">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-8 py-4 bg-white text-primary-600 font-bold text-[10px] uppercase tracking-[0.2em] rounded-xl shadow-2xl hover:bg-gray-50 transition-all transform hover:-translate-y-0.5">
                        Submit PR Now
                        <i data-feather="send" class="w-3.5 h-3.5"></i>
                    </button>
                </form>
            </div>
            {{-- Abstract bg pattern --}}
            <div class="absolute -right-20 -top-20 w-64 h-64 bg-white opacity-[0.05] rounded-full blur-3xl"></div>
        </div>
    @endif



    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-8">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">General Information</h3>
        </div>
        <div class="px-6 py-5">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-6">
                <div>
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">PR Number</dt>
                    <dd class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-bold bg-primary-100 text-primary-800 dark:bg-primary-900/30 dark:text-primary-400">
                            <i data-feather="hash" class="w-4 h-4 mr-1"></i>
                            {{ $purchaseRequisition->pr_number ?: 'N/A' }}
                        </span>
                    </dd>
                </div>

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
                            @if($purchaseRequisition->approval_status === 'approved') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                            @elseif($purchaseRequisition->approval_status === 'rejected') bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400
                            @elseif(str_starts_with($purchaseRequisition->approval_status, 'pending')) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400
                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                            {{ strtoupper(str_replace('_', ' ', $purchaseRequisition->approval_status)) }}
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
                
                {{-- Offers Section (PR Creator Only) --}}
            </dl>
        </div>
    </div>

    {{-- Offers Table Section (Inline Negotiation) --}}
    @if($purchaseRequisition->offers->count() > 0)
        <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-8">
            <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30 flex justify-between items-center">
                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Vendor Offers ({{ $purchaseRequisition->offers->count() }})</h3>
                <a href="{{ route('procurement.offers.index', $purchaseRequisition) }}" class="text-sm font-semibold text-primary-600 hover:text-primary-700">Detailed Rank View</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Vendor</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price (Rp)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Delivery</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Warranty</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($purchaseRequisition->offers as $offer)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                             @if($offer->company->logo_url)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $offer->company->logo_url }}" alt="">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs">
                                                    {{ substr($offer->company->name, 0, 2) }}
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $offer->company->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $offer->company->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">
                                    {{ $offer->formatted_total_price }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $offer->delivery_time }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $offer->warranty }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-bold rounded-full 
                                        @if($offer->status === 'winning') bg-green-100 text-green-800
                                        @elseif($offer->status === 'negotiating') bg-blue-100 text-blue-800
                                        @elseif($offer->status === 'rejected') bg-red-100 text-red-800
                                        @else bg-gray-100 text-gray-800 @endif">
                                        {{ ucfirst($offer->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('procurement.offers.show', $offer) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-lg text-xs font-semibold transition">
                                        <i data-feather="eye" class="w-3.5 h-3.5"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif


    {{-- Items Section --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700">
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
    @endif    {{-- Comments Section --}}
    <div class="bg-white dark:bg-gray-800 shadow-sm overflow-hidden rounded-2xl border border-gray-100 dark:border-gray-700 mb-8">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
            <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white uppercase tracking-tight">Questions & Comments ({{ $purchaseRequisition->comments->count() }})</h3>
        </div>
        
        {{-- Comment Form --}}
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-700">
            <form id="main-comment-form" action="{{ route('procurement.pr.add-comment', $purchaseRequisition) }}" method="POST">
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
                            <button type="submit" class="px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition shadow-xl shadow-primary-600/20">
                                <i data-feather="send" class="w-3.5 h-3.5 inline mr-1"></i>
                                Post Comment
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Comments List --}}
        <div id="comments-list" class="px-6 py-4 space-y-6">
            @forelse($purchaseRequisition->comments->where('parent_id', null) as $comment)
                <div class="comment-item bg-white dark:bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 transition-all hover:shadow-lg hover:shadow-primary-600/5">
                    {{-- Main Comment --}}
                    <div class="flex gap-4">
                        @if($comment->user->userDetail && $comment->user->userDetail->profile_photo_url)
                            <img src="{{ $comment->user->userDetail->profile_photo_url }}" alt="{{ $comment->user->name }}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0">
                        @else
                            <div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold text-xs flex-shrink-0">
                                {{ substr($comment->user->name, 0, 2) }}
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-1">
                                <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $comment->user->name }}</p>
                                <span class="text-xs text-gray-300 dark:text-gray-600">•</span>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed bg-gray-50 dark:bg-gray-900/50 p-4 rounded-xl border border-gray-50 dark:border-gray-800">
                                {!! preg_replace('/@(\w+)/', '<span class="text-primary-600 dark:text-primary-400 font-semibold">@$1</span>', e($comment->comment)) !!}
                            </div>
                            
                            <div class="mt-3 flex items-center gap-4">
                                {{-- Reply Button --}}
                                <a href="javascript:void(0)" onclick="toggleReplyForm('{{ $comment->id }}')" class="text-[10px] font-black uppercase tracking-widest text-primary-600 dark:text-primary-400 hover:text-primary-700 transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                    <i data-feather="corner-down-right" class="w-3.5 h-3.5"></i>
                                    Reply
                                </a>
                            </div>

                            {{-- Reply Form (Hidden by default) --}}
                            <div id="reply-form-{{ $comment->id }}" class="hidden mt-4">
                                <form action="{{ route('procurement.pr.add-comment', $purchaseRequisition) }}" method="POST" class="flex gap-3 reply-form" data-parent-id="{{ $comment->id }}">
                                    @csrf
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    @if(Auth::user()->userDetail && Auth::user()->userDetail->profile_photo_url)
                                        <img src="{{ Auth::user()->userDetail->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center text-primary-600 dark:text-primary-400 font-bold text-[10px] flex-shrink-0">
                                            {{ substr(Auth::user()->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <textarea name="comment" rows="2" required placeholder="Write a reply..." class="block w-full rounded-xl border border-gray-100 dark:border-gray-800 px-4 py-3 text-xs shadow-inner focus:border-primary-500/50 focus:ring-4 focus:ring-primary-500/5 dark:bg-gray-900 dark:text-white outline-none resize-none"></textarea>
                                        <div class="mt-2 flex gap-2 justify-end">
                                            <button type="button" onclick="toggleReplyForm('{{ $comment->id }}')" class="px-3 py-2 bg-gray-100 dark:bg-gray-800 text-gray-500 text-[9px] font-black uppercase tracking-widest rounded-lg hover:bg-gray-200 transition-all">
                                                Cancel
                                            </button>
                                            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-[9px] font-black uppercase tracking-widest rounded-lg transition shadow-lg shadow-primary-600/10">
                                                Post Reply
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Nested Replies --}}
                    <div id="replies-container-{{ $comment->id }}" class="ml-12 mt-6 space-y-6 border-l-2 border-gray-100 dark:border-gray-800 pl-6 {{ $comment->replies->count() > 0 ? '' : 'hidden' }}">
                        @foreach($comment->replies as $reply)
                            <div>
                                <div class="flex gap-3">
                                    @if($reply->user->userDetail && $reply->user->userDetail->profile_photo_url)
                                        <img src="{{ $reply->user->userDetail->profile_photo_url }}" alt="{{ $reply->user->name }}" class="w-9 h-9 rounded-xl object-cover flex-shrink-0">
                                    @else
                                        <div class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-900/50 flex items-center justify-center text-gray-500 dark:text-gray-400 font-bold text-[10px] flex-shrink-0">
                                            {{ substr($reply->user->name, 0, 2) }}
                                        </div>
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1">
                                            <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $reply->user->name }}</p>
                                            <span class="text-xs text-gray-300">•</span>
                                            <p class="text-[9px] font-semibold text-gray-400 uppercase tracking-widest">{{ $reply->created_at->diffForHumans() }}</p>
                                        </div>
                                        <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed bg-white dark:bg-gray-800/30 p-3 rounded-xl border border-gray-50 dark:border-gray-700/50">
                                            {!! preg_replace('/@(\w+)/', '<span class="text-primary-600 dark:text-primary-400 font-semibold">@$1</span>', e($reply->comment)) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-gray-50 dark:bg-gray-900/50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-gray-100 dark:border-gray-800">
                        <i data-feather="message-circle" class="w-8 h-8 text-gray-200 dark:text-gray-700"></i>
                    </div>
                    <p class="text-[10px] font-black text-gray-300 dark:text-gray-600 uppercase tracking-[0.2em]">No comments yet recorded</p>
                </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    @endpush

    <script>
        function handlePrFormSubmit(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                
                submitBtn.innerHTML = '<span class="flex items-center gap-2">' +
                    '<svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                    '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                    '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
                    '</svg>' +
                    'Processing...' +
                    '</span>';
            }
            return true;
        }

        function openNegotiateModal(actionUrl) {
            const modal = document.getElementById('negotiateModal');
            const form = document.getElementById('negotiateForm');
            form.action = actionUrl;
            modal.classList.remove('hidden');
        }

        function closeNegotiateModal() {
            document.getElementById('negotiateModal').classList.add('hidden');
        }

        // AJAX Comment Logic
        window.toggleReplyForm = function(formId) {
            const form = document.getElementById('reply-form-' + formId);
            
            if (form.classList.contains('hidden')) {
                document.querySelectorAll('[id^="reply-form-"]').forEach(f => {
                    f.classList.add('hidden');
                    const textarea = f.querySelector('textarea');
                    if (textarea) textarea.value = '';
                });
                form.classList.remove('hidden');
                const textarea = form.querySelector('textarea');
                if (textarea) textarea.focus();
            } else {
                form.classList.add('hidden');
                const textarea = form.querySelector('textarea');
                if (textarea) textarea.value = '';
            }
            feather.replace();
        }

        window.setupAjaxComments = function() {
            const mainForm = document.getElementById('main-comment-form');
            if (mainForm) {
                mainForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    submitComment(this);
                });
            }

            document.querySelectorAll('.reply-form').forEach(form => {
                form.removeEventListener('submit', handleReplySubmit);
                form.addEventListener('submit', handleReplySubmit);
            });
        }

        function handleReplySubmit(e) {
            e.preventDefault();
            submitComment(this);
        }

        window.submitComment = async function(form) {
            const submitBtn = form.querySelector('button[type="submit"]');
            const originalBtnHtml = submitBtn.innerHTML;
            const textarea = form.querySelector('textarea');
            const formData = new FormData(form);

            if (!textarea.value.trim()) return;

            submitBtn.disabled = true;
            submitBtn.innerHTML = `<span class="flex items-center gap-1.5"><svg class="animate-spin h-3.5 w-3.5" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> SENDING...</span>`;

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Server returned error:', response.status, errorText);
                    throw new Error(`Server Error (${response.status})`);
                }

                const data = await response.json();

                if (data.status === 'success') {
                    appendComment(data.comment);
                    textarea.value = '';
                    if (form.classList.contains('reply-form')) {
                        const parentId = formData.get('parent_id');
                        const container = document.getElementById('reply-form-' + parentId);
                        if (container) container.classList.add('hidden');
                    }
                } else {
                    alert(data.message || 'Failed to post comment.');
                }
            } catch (error) {
                console.error('AJAX Error:', error);
                alert('An error occurred. Please check your connection and try again.');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnHtml;
            }
        }

        window.appendComment = function(comment) {
            const emptyState = document.querySelector('#comments-list .text-center');
            if (emptyState) emptyState.remove();

            const avatarHtml = comment.user_avatar 
                ? `<img src="${comment.user_avatar}" class="w-12 h-12 rounded-xl object-cover flex-shrink-0">`
                : `<div class="w-12 h-12 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-700 dark:text-primary-400 font-bold text-xs flex-shrink-0 uppercase">${comment.user_initials}</div>`;

            const nestedAvatarHtml = comment.user_avatar 
                ? `<img src="${comment.user_avatar}" class="w-9 h-9 rounded-xl object-cover flex-shrink-0">`
                : `<div class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-900/50 flex items-center justify-center text-gray-500 font-bold text-[10px] flex-shrink-0 uppercase">${comment.user_initials}</div>`;

            const commentHtml = `
                <div class="flex gap-4">
                    ${comment.parent_id ? nestedAvatarHtml : avatarHtml}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">${comment.user_name}</p>
                            <span class="text-xs text-gray-300">•</span>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">${comment.created_at}</p>
                        </div>
                        <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed bg-white dark:bg-gray-800/30 p-3 rounded-xl border border-gray-50 dark:border-gray-700/50">
                            ${comment.content}
                        </div>
                        ${!comment.parent_id ? `
                            <div class="mt-3">
                                <a href="javascript:void(0)" onclick="toggleReplyForm('${comment.id}')" class="text-[10px] font-black uppercase tracking-widest text-primary-600 dark:text-primary-400 hover:text-primary-700 transition-all inline-flex items-center gap-1.5 cursor-pointer">
                                    <i data-feather="corner-down-right" class="w-3.5 h-3.5"></i>
                                    Reply
                                </a>
                            </div>
                        ` : ''}
                    </div>
                </div>
            `;

            if (comment.parent_id) {
                const container = document.getElementById(`replies-container-${comment.parent_id}`);
                if (container) {
                    container.classList.remove('hidden');
                    const wrapper = document.createElement('div');
                    wrapper.innerHTML = commentHtml;
                    container.appendChild(wrapper);
                }
            } else {
                const list = document.getElementById('comments-list');
                const wrapper = document.createElement('div');
                wrapper.className = 'comment-item bg-white dark:bg-gray-800/50 p-6 rounded-2xl border border-gray-100 dark:border-gray-700 transition-all hover:shadow-lg hover:shadow-primary-600/5';
                wrapper.innerHTML = commentHtml;
                list.insertBefore(wrapper, list.firstChild);
            }

            feather.replace();
        }

        window.initCommentSystem = function() {
            setupAjaxComments();
            feather.replace();
        }

        document.addEventListener('DOMContentLoaded', initCommentSystem);
        document.addEventListener('livewire:navigated', initCommentSystem);
        document.addEventListener('turbo:load', initCommentSystem);
    </script>
@endsection
```