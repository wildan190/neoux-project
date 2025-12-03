@extends('layouts.app', [
    'title' => 'Dashboard',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Dashboard', 'url' => '#']
    ]
])

@section('content')

@if($companies->isEmpty())
    {{-- No Companies - Show Create Button --}}
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center">
            <div class="mb-6">
                <i data-feather="building" class="w-24 h-24 mx-auto text-gray-400"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Companies Yet</h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Create your first company to get started</p>
            <a href="{{ route('companies.create') }}" class="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors">
                <i data-feather="plus" class="w-5 h-5 mr-2"></i>
                Create Company
            </a>
        </div>
    </div>
@else
    {{-- Company Selection Cards --}}
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Select a Company</h2>
        <p class="text-gray-600 dark:text-gray-400">Choose a company to continue working</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($companies as $company)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden">
                {{-- Company Header --}}
                <div class="h-32 bg-gradient-to-br from-primary-500 to-purple-600 relative">
                    @if($company->logo)
                        <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="absolute bottom-0 left-6 w-20 h-20 rounded-xl border-4 border-white dark:border-gray-800 object-cover">
                    @else
                        <div class="absolute bottom-0 left-6 w-20 h-20 rounded-xl border-4 border-white dark:border-gray-800 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                            <i data-feather="building" class="w-10 h-10 text-gray-500"></i>
                        </div>
                    @endif
                </div>

                {{-- Company Info --}}
                <div class="p-6 pt-8">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $company->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $company->business_category }}</p>

                    <div class="flex items-center gap-2 mb-4">
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if($company->category === 'buyer') bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300
                            @elseif($company->category === 'supplier') bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                            @else bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300
                            @endif">
                            {{ ucfirst($company->category) }}
                        </span>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full
                            @if(in_array($company->status, ['approved', 'active'])) bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300
                            @elseif($company->status === 'pending') bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300
                            @else bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300
                            @endif">
                            {{ ucfirst($company->status) }}
                        </span>
                    </div>

                    <form action="{{ route('dashboard.select-company', $company->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full bg-primary-600 text-white font-semibold py-3 rounded-lg hover:bg-primary-700 transition-colors flex items-center justify-center">
                            <i data-feather="log-in" class="w-5 h-5 mr-2"></i>
                            Login as {{ $company->name }}
                        </button>
                    </form>
                </div>
            </div>
        @endforeach

        {{-- Add New Company Card --}}
        <a href="{{ route('companies.create') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-600 hover:border-primary-500 flex items-center justify-center min-h-[300px] group">
            <div class="text-center">
                <i data-feather="plus-circle" class="w-16 h-16 mx-auto text-gray-400 group-hover:text-primary-500 transition-colors mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400 group-hover:text-primary-500 font-semibold transition-colors">Add New Company</p>
            </div>
        </a>
    </div>
@endif

@endsection
