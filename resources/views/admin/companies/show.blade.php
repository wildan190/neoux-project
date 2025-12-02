@extends('admin.layouts.app', [
    'title' => 'Company Details',
    'breadcrumbs' => [
        ['label' => 'Companies', 'url' => route('admin.companies.index')],
        ['label' => $company->name]
    ]
])

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.companies.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400">
            <i data-feather="arrow-left" class="w-4 h-4 inline"></i> Back to Companies
        </a>
    </div>

    <!-- Company Header -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-start space-x-4">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}"
                        class="w-20 h-20 rounded-lg object-cover">
                @else
                    <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                        <i data-feather="briefcase" class="w-10 h-10 text-indigo-600 dark:text-indigo-400"></i>
                    </div>
                @endif
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $company->name }}</h2>
                    <p class="text-gray-600 dark:text-gray-400">{{ $company->email }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <i data-feather="user" class="w-4 h-4 inline"></i> Owner:
                        {{ $company->user->name ?: $company->user->email }}
                    </p>
                </div>
            </div>
            <span class="px-4 py-2 rounded-lg text-sm font-semibold
                @if($company->status === 'active') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                @elseif($company->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                @endif">
                {{ ucfirst($company->status) }}
            </span>
        </div>
    </div>

    <!-- Company Information -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Basic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i data-feather="info" class="w-5 h-5 mr-2"></i>
                Basic Information
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Business Category</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $company->business_category }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Company Category</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        <span
                            class="px-2 py-1 bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded text-xs">
                            {{ ucfirst($company->category) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">NPWP</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $company->npwp ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Registered Date</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $company->registered_date ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Country</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $company->country ?: '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i data-feather="phone" class="w-5 h-5 mr-2"></i>
                Contact Information
            </h3>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $company->email ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $company->phone ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Website</p>
                    <p class="font-medium text-gray-900 dark:text-white">
                        @if($company->website)
                            <a href="{{ $company->website }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">
                                {{ $company->website }}
                            </a>
                        @else
                            -
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $company->address ?: '-' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tag</p>
                    <p class="font-medium text-gray-900 dark:text-white">{{ $company->tag ?: '-' }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Description -->
    @if($company->description)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i data-feather="file-text" class="w-5 h-5 mr-2"></i>
                Description
            </h3>
            <p class="text-gray-700 dark:text-gray-300">{{ $company->description }}</p>
        </div>
    @endif

    <!-- Company Locations -->
    @if($company->locations->count() > 0)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
                <i data-feather="map-pin" class="w-5 h-5 mr-2"></i>
                Locations ({{ $company->locations->count() }})
            </h3>
            <div class="space-y-2">
                @foreach($company->locations as $location)
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-gray-900 dark:text-white">{{ $location->address }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Company Documents -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
            <i data-feather="folder" class="w-5 h-5 mr-2"></i>
            Documents ({{ $company->documents->count() }})
        </h3>
        @if($company->documents->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($company->documents as $document)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <i data-feather="file" class="w-8 h-8 text-indigo-600 dark:text-indigo-400"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ basename($document->file_path) }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Uploaded: {{ $document->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <a href="{{ asset('storage/' . $document->file_path) }}" target="_blank"
                                class="inline-flex items-center px-3 py-2 bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-200 rounded-lg hover:bg-indigo-200 dark:hover:bg-indigo-800 transition text-sm">
                                <i data-feather="eye" class="w-4 h-4 mr-1"></i>
                                View Document
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                No documents uploaded yet.
            </p>
        @endif
    </div>

<!-- Audit Trail -->
@if($company->status !== 'pending')
<div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
        <i data-feather="user-check" class="w-5 h-5 mr-2"></i>
        Audit Trail
    </h3>
    <div class="space-y-3">
        @if($company->status === 'active' && $company->approvedBy)
            <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">Approved By</p>
                        <p class="text-lg font-semibold text-green-900 dark:text-green-100">{{ $company->approvedBy->name }}</p>
                        <p class="text-sm text-green-700 dark:text-green-300">{{ $company->approvedBy->email }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-green-600 dark:text-green-400">{{ $company->approved_at->format('M d, Y') }}</p>
                        <p class="text-xs text-green-500 dark:text-green-500">{{ $company->approved_at->format('h:i A') }}</p>
                    </div>
                </div>
            </div>
        @endif
        
        @if($company->status === 'declined' && $company->declinedBy)
            <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">Declined By</p>
                        <p class="text-lg font-semibold text-red-900 dark:text-red-100">{{ $company->declinedBy->name }}</p>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ $company->declinedBy->email }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $company->declined_at->format('M d, Y') }}</p>
                        <p class="text-xs text-red-500 dark:text-red-500">{{ $company->declined_at->format('h:i A') }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endif

<!-- Recent Activity -->
@if($company->activities->count() > 0)
<div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 flex items-center">
        <i data-feather="activity" class="w-5 h-5 mr-2"></i>
        Recent Activity
    </h3>
    <div class="space-y-3">
        @foreach($company->activities as $activity)
            <div class="flex items-start space-x-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg transition">
                <div class="flex-shrink-0">
                    @if($activity->action === 'approved')
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                            <i data-feather="check" class="w-5 h-5 text-green-600 dark:text-green-400"></i>
                        </div>
                    @elseif($activity->action === 'declined')
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                            <i data-feather="x" class="w-5 h-5 text-red-600 dark:text-red-400"></i>
                        </div>
                    @else
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                            <i data-feather="info" class="w-5 h-5 text-blue-600 dark:text-blue-400"></i>
                        </div>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $activity->description }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        by {{ $activity->admin->name }} · {{ $activity->created_at->diffForHumans() }}
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif

    <!-- Action Buttons -->
    @if($company->status === 'pending')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Review Actions</h3>
            <div class="flex space-x-4">
                <form action="{{ route('admin.companies.approve', $company) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" onclick="return confirm('Are you sure you want to approve this company?')"
                        class="w-full px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center">
                        <i data-feather="check-circle" class="w-5 h-5 mr-2"></i>
                        Approve Company
                    </button>
                </form>
                <form action="{{ route('admin.companies.decline', $company) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" onclick="return confirm('Are you sure you want to decline this company?')"
                        class="w-full px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center justify-center">
                        <i data-feather="x-circle" class="w-5 h-5 mr-2"></i>
                        Decline Company
                    </button>
                </form>
            </div>
        </div>
    @elseif($company->status === 'active')
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center text-green-600 dark:text-green-400">
                <i data-feather="check-circle" class="w-6 h-6 mr-2"></i>
                <span class="font-semibold">This company has been approved</span>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-6">
            <div class="flex items-center text-red-600 dark:text-red-400">
                <i data-feather="x-circle" class="w-6 h-6 mr-2"></i>
                <span class="font-semibold">This company has been declined</span>
            </div>
        </div>
    @endif
@endsection