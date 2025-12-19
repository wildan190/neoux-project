@extends('layouts.app', [
    'title' => 'Request Details',
    'breadcrumbs' => [
        ['name' => 'Procurement', 'url' => route('procurement.pr.index')],
        ['name' => $purchaseRequisition->pr_number ?: 'Request #' . $purchaseRequisition->id, 'url' => '#']
    ]
])

@section('content')
    <div class="mb-6 flex justify-end">
        <a href="{{ url()->previous() }}"
            class="bg-white py-2 px-4 border border-gray-300 rounded-xl shadow-sm text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:hover:bg-gray-700 transition-all">
            Back
        </a>
    </div>

    {{-- Approval Actions Section --}}
    @php
        $currentUser = Auth::user();
        $userRole = $currentUser->companies->find($purchaseRequisition->company_id)?->pivot->role ?? 'staff';
        $isApprover = $purchaseRequisition->approver_id === $currentUser->id;
        $isCreator = $purchaseRequisition->user_id === $currentUser->id; // Renamed from isOwner for clarity
        $isCompanyOwner = $purchaseRequisition->company->user_id === $currentUser->id; // Actual Company Owner
        $isAdmin = $userRole === 'admin';
        $isManager = $userRole === 'manager';
    @endphp

    @if($purchaseRequisition->approval_status === 'pending' && ($isApprover || $isAdmin || $isCompanyOwner))
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border-l-4 border-yellow-500 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Approval Required</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">This requisition is waiting for your approval.</p>
            <form action="{{ route('procurement.pr.approve', $purchaseRequisition) }}" method="POST" class="inline-block">
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
            <form action="{{ route('procurement.pr.submit-approval', $purchaseRequisition) }}" method="POST" class="max-w-md">
                @csrf
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Select Approver</label>
                        <select name="approver_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                             {{-- Ideally pass users from controller, but for now we rely on simple query or assumption. 
                                  Wait, we don't have users list here. Controller needs to pass it.
                                  Let's check if we can get logic. For now, we will add a TODO to fix controller? 
                                  No, we must fix it. 
                                  Actually, we can use $purchaseRequisition->company->members (if eager loaded).
                             --}}
                             @foreach($purchaseRequisition->company->members as $member)
                                 @if(in_array($member->pivot->role, ['admin', 'manager', 'approver']))
                                     <option value="{{ $member->id }}">{{ $member->name }} ({{ ucfirst($member->pivot->role) }})</option>
                                 @endif
                             @endforeach
                             {{-- Add Owner --}}
                             @if($purchaseRequisition->company->user)
                                <option value="{{ $purchaseRequisition->company->user_id }}" {{ $purchaseRequisition->company->user_id === $purchaseRequisition->user_id ? 'selected' : '' }}>
                                    {{ $purchaseRequisition->company->user->name }} (Owner)
                                </option>
                             @endif
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-lg shadow-sm transition">
                        Submit
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Assignment Section (Admin/Manager) --}}
    @if($isAdmin || $isManager)
         <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl border border-gray-100 dark:border-gray-700 p-6 mb-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Task Assignment</h3>
             <form action="{{ route('procurement.pr.assign', $purchaseRequisition) }}" method="POST" class="max-w-md">
                @csrf
                <div class="flex gap-4 items-end">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Assign To</label>
                        <select name="assigned_to" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-primary-500 focus:ring-primary-500">
                             <option value="">Select Member</option>
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
                
                {{-- Offers Section (PR Creator Only) --}}
                @php
                    $offersCount = $purchaseRequisition->offers()->count();
                @endphp
                @if($offersCount > 0)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Tender Offers</dt>
                        <dd class="mt-1">
                            <div class="flex items-center gap-4">
                                <a href="{{ route('procurement.offers.index', $purchaseRequisition) }}" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-bold rounded-lg transition shadow-sm">
                                    <i data-feather="file-text" class="w-4 h-4"></i>
                                    View All Offers
                                    <span class="px-2 py-0.5 bg-white/20 rounded-full text-xs">{{ $offersCount }}</span>
                                </a>
                                
                                @if($purchaseRequisition->winningOffer)
                                    <div class="flex items-center gap-2 text-sm text-green-600 dark:text-green-400">
                                        <i data-feather="award" class="w-4 h-4"></i>
                                        <span class="font-semibold">Winner: {{ $purchaseRequisition->winningOffer->company->name }}</span>
                                    </div>
                                @else
                                    <span class="px-3 py-1 text-xs font-bold rounded-full 
                                        @if($purchaseRequisition->tender_status === 'open') bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400
                                        @else bg-gray-100 text-gray-700 dark:bg-gray-900/30 dark:text-gray-400 @endif">
                                        Tender: {{ ucfirst($purchaseRequisition->tender_status) }}
                                    </span>
                                @endif
                            </div>
                        </dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

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
            <form action="{{ route('procurement.pr.reject', $purchaseRequisition) }}" method="POST">
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
@endsection
```