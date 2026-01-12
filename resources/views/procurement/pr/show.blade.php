@extends('layouts.app', [
    'title' => 'Request Details',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => $purchaseRequisition->pr_number ?: 'Request #' . $purchaseRequisition->id, 'url' => '#']
    ]
])

@section('content')
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $purchaseRequisition->pr_number ?: 'Request #' . $purchaseRequisition->id }}</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Created by {{ $purchaseRequisition->user->name }} on {{ $purchaseRequisition->created_at->format('M d, Y') }}</p>
        </div>
        <a href="{{ url()->previous() }}"
            class="bg-white py-2 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700 transition-all">
            Back
        </a>
    </div>

    {{-- Progress Stepper --}}
    <div class="mb-8">
        <div class="flex items-center justify-between relative">
            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 w-full h-1 bg-gray-200 dark:bg-gray-700 -z-10"></div>
            
            <!-- Step 1: Draft/Submitted -->
            <div class="bg-white dark:bg-gray-800 px-4">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white mb-2
                        {{ $purchaseRequisition->submitted_at ? 'bg-green-600' : 'bg-gray-300 dark:bg-gray-600' }}">
                        @if($purchaseRequisition->submitted_at) <i data-feather="check" class="w-4 h-4"></i> @else 1 @endif
                    </div>
                    <span class="text-xs font-bold {{ $purchaseRequisition->submitted_at ? 'text-green-600' : 'text-gray-500' }}">Draft</span>
                </div>
            </div>

            <!-- Step 2: Supervisor Approval -->
            <div class="bg-white dark:bg-gray-800 px-4">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white mb-2
                        {{ in_array($purchaseRequisition->approval_status, ['pending_head', 'approved', 'awarded', 'ordered']) ? 'bg-green-600' : 
                          ($purchaseRequisition->approval_status === 'pending_supervisor' ? 'bg-blue-600 animate-pulse' : 'bg-gray-300 dark:bg-gray-600') }}">
                         @if(in_array($purchaseRequisition->approval_status, ['pending_head', 'approved', 'awarded', 'ordered'])) <i data-feather="check" class="w-4 h-4"></i> @else 2 @endif
                    </div>
                    <span class="text-xs font-bold 
                        {{ in_array($purchaseRequisition->approval_status, ['pending_head', 'approved', 'awarded', 'ordered']) ? 'text-green-600' : 
                          ($purchaseRequisition->approval_status === 'pending_supervisor' ? 'text-blue-600' : 'text-gray-500') }}">Supervisor</span>
                </div>
            </div>

            <!-- Step 3: Head Approval -->
            <div class="bg-white dark:bg-gray-800 px-4">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white mb-2
                        {{ in_array($purchaseRequisition->approval_status, ['approved', 'awarded', 'ordered']) ? 'bg-green-600' : 
                          ($purchaseRequisition->approval_status === 'pending_head' ? 'bg-blue-600 animate-pulse' : 'bg-gray-300 dark:bg-gray-600') }}">
                         @if(in_array($purchaseRequisition->approval_status, ['approved', 'awarded', 'ordered'])) <i data-feather="check" class="w-4 h-4"></i> @else 3 @endif
                    </div>
                    <span class="text-xs font-bold 
                        {{ in_array($purchaseRequisition->approval_status, ['approved', 'awarded', 'ordered']) ? 'text-green-600' : 
                          ($purchaseRequisition->approval_status === 'pending_head' ? 'text-blue-600' : 'text-gray-500') }}">Head Approver</span>
                </div>
            </div>

             <!-- Step 4: Approved/Tender -->
             <div class="bg-white dark:bg-gray-800 px-4">
                <div class="flex flex-col items-center">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-white mb-2
                        {{ $purchaseRequisition->status === 'open' || $purchaseRequisition->status === 'awarded' || $purchaseRequisition->status === 'ordered' ? 'bg-green-600' : 'bg-gray-300 dark:bg-gray-600' }}">
                        @if($purchaseRequisition->status === 'open' || $purchaseRequisition->status === 'awarded' || $purchaseRequisition->status === 'ordered') <i data-feather="check" class="w-4 h-4"></i> @else 4 @endif
                    </div>
                    <span class="text-xs font-bold {{ $purchaseRequisition->status === 'open' || $purchaseRequisition->status === 'awarded' || $purchaseRequisition->status === 'ordered' ? 'text-green-600' : 'text-gray-500' }}">
                        {{ $purchaseRequisition->type === 'tender' ? 'Tender Open' : 'Approved' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Approval Actions Section --}}
    @php
        $currentUser = Auth::user();
        $userRole = $currentUser->companies->find($purchaseRequisition->company_id)?->pivot->role ?? 'staff';
        $isSupervisor = $purchaseRequisition->approver_id === $currentUser->id;
        $isHead = $purchaseRequisition->head_approver_id === $currentUser->id;
        $isCreator = $purchaseRequisition->user_id === $currentUser->id;
        $isCompanyOwner = $purchaseRequisition->company->user_id === $currentUser->id;
        $isAdmin = $userRole === 'admin';
        $isManager = $userRole === 'manager';
        
        $canApproveSupervisor = $purchaseRequisition->approval_status === 'pending_supervisor' && ($isSupervisor || $isAdmin || $isCompanyOwner);
        $canApproveHead = $purchaseRequisition->approval_status === 'pending_head' && ($isHead || $isAdmin || $isCompanyOwner);
    @endphp

    @if($canApproveSupervisor || $canApproveHead)
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border-l-4 border-yellow-500 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Approval Required</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                @if($canApproveSupervisor)
                    This requisition is waiting for <strong>Supervisor</strong> approval.
                @else
                    This requisition is waiting for <strong>Head</strong> approval.
                @endif
            </p>
            <form action="{{ route('procurement.pr.approve', $purchaseRequisition) }}" method="POST" class="inline-block" onsubmit="return handlePrFormSubmit(this)">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes (Optional)</label>
                    <textarea name="approval_notes" rows="2" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500 max-w-lg"></textarea>
                </div>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-sm transition">
                    Approve Request
                </button>
            </form>
            <button onclick="document.getElementById('rejectModal').classList.remove('hidden')" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-bold rounded-lg shadow-sm transition ml-2">
                Reject Request
            </button>
        </div>
    @endif

    {{-- Submission Section --}}
    @if(($purchaseRequisition->approval_status === 'draft' || $purchaseRequisition->approval_status === 'rejected') && ($isCreator || $isAdmin))
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Submit for Approval</h3>
            <form action="{{ route('procurement.pr.submit-approval', $purchaseRequisition) }}" method="POST" onsubmit="return handlePrFormSubmit(this)">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Supervisor</label>
                        <select name="approver_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                             <option value="">-- Choose Supervisor --</option>
                             @foreach($purchaseRequisition->company->members as $member)
                                 @if(in_array($member->pivot->role, ['admin', 'manager', 'approver']))
                                     <option value="{{ $member->id }}">{{ $member->name }} ({{ ucfirst($member->pivot->role) }})</option>
                                 @endif
                             @endforeach
                             @if($purchaseRequisition->company->user && !in_array($purchaseRequisition->company->user->pivot->role ?? '', ['admin', 'manager', 'approver']))
                                <option value="{{ $purchaseRequisition->company->user_id }}">
                                    {{ $purchaseRequisition->company->user->name }} (Owner)
                                </option>
                             @endif
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Head Approver</label>
                        <select name="head_approver_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                             <option value="">-- Choose Head Approver --</option>
                             @foreach($purchaseRequisition->company->members as $member)
                                 @if(in_array($member->pivot->role, ['admin', 'manager']))
                                     <option value="{{ $member->id }}">{{ $member->name }} ({{ ucfirst($member->pivot->role) }})</option>
                                 @endif
                             @endforeach
                             @if($purchaseRequisition->company->user)
                                <option value="{{ $purchaseRequisition->company->user_id }}">
                                    {{ $purchaseRequisition->company->user->name }} (Owner)
                                </option>
                             @endif
                        </select>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg shadow-sm transition">
                        Submit PR for Approval
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Assignment Section (Admin/Manager/Owner) --}}
    @if($isAdmin || $isManager || $isCompanyOwner)
         <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Task Assignment</h3>
             <form action="{{ route('procurement.pr.assign', $purchaseRequisition) }}" method="POST" class="max-w-md" onsubmit="return handlePrFormSubmit(this)">
                @csrf
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assign To</label>
                        <select name="assigned_to" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                             <option value="">Select Member</option>
                             {{-- Add Owner Explicitly if not in members list --}}
                             @if($purchaseRequisition->company->user)
                                <option value="{{ $purchaseRequisition->company->user_id }}" {{ $purchaseRequisition->assigned_to == $purchaseRequisition->company->user_id ? 'selected' : '' }}>
                                    {{ $purchaseRequisition->company->user->name }} (Owner)
                                </option>
                             @endif
                             
                             @foreach($purchaseRequisition->company->members as $member)
                                 <option value="{{ $member->id }}" {{ $purchaseRequisition->assigned_to == $member->id ? 'selected' : '' }}>
                                     {{ $member->name }} ({{ ucfirst($member->pivot->role) }})
                                 </option>
                             @endforeach
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg shadow-sm transition">
                        Assign
                    </button>
                </div>
            </form>
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
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('procurement.offers.show', $offer) }}" class="text-gray-500 hover:text-gray-700" title="View Details">
                                            <i data-feather="eye" class="w-4 h-4"></i>
                                        </a>
                                        @if(in_array($offer->status, ['pending', 'negotiating']))
                                            @if($offer->status === 'pending')
                                                <button onclick="openNegotiateModal('{{ route('procurement.offers.submit-negotiation', $offer) }}')" 
                                                        class="text-indigo-600 hover:text-indigo-900 bg-indigo-50 hover:bg-indigo-100 p-1.5 rounded-lg transition" title="Negotiate">
                                                    <i data-feather="message-circle" class="w-4 h-4"></i>
                                                </button>
                                            @endif
                                            <form action="{{ route('procurement.offers.reject', $offer) }}" method="POST" class="inline" onsubmit="return confirm('Reject this offer?')">
                                                @csrf
                                                <button type="submit" class="text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-1.5 rounded-lg transition" title="Reject">
                                                    <i data-feather="x" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
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
    @endif

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6 relative">
            <button onclick="document.getElementById('rejectModal').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Reject Requisition</h3>
            <form action="{{ route('procurement.pr.reject', $purchaseRequisition) }}" method="POST" onsubmit="return handlePrFormSubmit(this)">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason for Rejection</label>
                        <textarea name="approval_notes" required rows="3" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                    </div>
                    <button type="submit" class="w-full py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 font-medium">Confirm Rejection</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Negotiate Modal (Shared) -->
    <div id="negotiateModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-md mx-4 p-6 relative">
            <button onclick="closeNegotiateModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                <i data-feather="x" class="w-5 h-5"></i>
            </button>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Invite to Negotiate</h3>
            <p class="text-sm text-gray-500 mb-4">Send a message to the vendor explaining what needs to be revised.</p>
            
            <form id="negotiateForm" action="" method="POST" onsubmit="return handlePrFormSubmit(this)">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Negotiation Message</label>
                        <textarea name="message" rows="3" required placeholder="e.g. Can you lower the price by 5%?" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500"></textarea>
                    </div>
                    <div class="flex gap-3">
                         <button type="button" onclick="closeNegotiateModal()" class="flex-1 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300 font-medium">Cancel</button>
                         <button type="submit" class="flex-1 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">Send Invitation</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@push('scripts')
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
</script>
@endpush
@endsection
```