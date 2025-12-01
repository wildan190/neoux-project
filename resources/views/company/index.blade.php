@extends('layouts.app', [
    'title' => 'My Companies',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Companies', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">My Companies</h2>
        <a href="{{ route('companies.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 shadow-md transition-all flex items-center gap-2">
            <i data-feather="plus" class="w-4 h-4"></i>
            <span>Add Company</span>
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($companies as $company)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 dark:border-gray-700">
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-gray-600">
                            @if($company->logo)
                                <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                            @else
                                <i data-feather="briefcase" class="w-8 h-8 text-gray-400"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white line-clamp-1">{{ $company->name }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $company->business_category }}</p>
                        </div>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
                        {{ $company->status === 'approved' ? 'bg-green-100 text-green-800' : 
                           ($company->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($company->status) }}
                    </span>
                </div>
                
                <div class="space-y-2 mb-6">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <i data-feather="tag" class="w-4 h-4"></i>
                        <span class="capitalize">{{ $company->category }}</span>
                    </div>
                    @if($company->country)
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <i data-feather="map-pin" class="w-4 h-4"></i>
                        <span>{{ $company->country }}</span>
                    </div>
                    @endif
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <i data-feather="file-text" class="w-4 h-4"></i>
                        <span>{{ $company->documents->count() }} Documents</span>
                    </div>
                </div>

                <a href="{{ route('companies.show', $company) }}" class="block w-full text-center bg-gray-50 dark:bg-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 text-indigo-600 dark:text-indigo-400 font-medium py-2 rounded-lg transition-colors">
                    View Details
                </a>
            </div>
        </div>
        @endforeach
    </div>

    @if($companies->isEmpty())
    <div class="text-center py-12">
        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
            <i data-feather="briefcase" class="w-8 h-8 text-gray-400"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">No companies found</h3>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Get started by creating your first company.</p>
    </div>
    @endif
</div>
@endsection
