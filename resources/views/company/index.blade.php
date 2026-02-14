@extends('layouts.app', [
    'title' => 'My Companies',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Companies', 'url' => '#']
    ]
])

@section('content')

@php
    $user = auth()->user();
    $hasCompanies = $companies->isNotEmpty();
    $isOwner = $user->ownedCompanies()->exists();
    $isRestricted = $hasCompanies && !$isOwner;
@endphp
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 md:mb-8 gap-4">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white tracking-tight">My Companies</h2>
            <p class="text-sm md:text-base text-gray-500 dark:text-gray-400 mt-1">Manage your registered entities and businesses.</p>
        </div>
        @if(!$isRestricted)
        <a href="{{ route('companies.create') }}" class="w-full md:w-auto group relative inline-flex items-center justify-center px-6 py-3 text-sm md:text-base font-medium text-white transition-all duration-200 bg-primary-600 border border-transparent rounded-xl hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50">
            <i data-feather="plus" class="w-5 h-5 mr-2 -ml-1 transition-transform group-hover:rotate-90"></i>
            Register New Company
        </a>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 px-4 py-3 rounded-xl relative mb-8 flex items-center gap-3 shadow-sm" role="alert">
            <i data-feather="check-circle" class="w-5 h-5 text-green-500 shrink-0"></i>
            <span class="block sm:inline font-medium text-sm md:text-base">{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
        @foreach($companies as $index => $company)
        <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-black/30 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full transform hover:-translate-y-1">
            
            {{-- Cover Image --}}
            <div class="h-32 md:h-40 w-full relative overflow-hidden bg-gray-100 dark:bg-gray-700">
                <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80" 
                     alt="Office Cover" 
                     class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                
                {{-- Status Badge --}}
                <div class="absolute top-3 right-3 md:top-4 md:right-4">
                    <span class="px-2 py-0.5 md:px-3 md:py-1 text-[10px] md:text-xs font-bold uppercase tracking-wider rounded-full shadow-sm backdrop-blur-md
                        {{ $company->status === 'approved' ? 'bg-green-500/90 text-white' : 
                           ($company->status === 'pending' ? 'bg-yellow-500/90 text-white' : 'bg-red-500/90 text-white') }}">
                        {{ $company->status }}
                    </span>
                </div>
            </div>

            <div class="p-5 md:p-6 flex-1 flex flex-col relative">
                {{-- Logo (Overlapping) --}}
                <div class="absolute -top-8 left-5 md:-top-10 md:left-6">
                    <div class="w-16 h-16 md:w-20 md:h-20 rounded-xl md:rounded-2xl bg-white dark:bg-gray-800 p-1 shadow-lg ring-1 ring-black/5 dark:ring-white/10">
                        <div class="w-full h-full rounded-lg md:rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                            @if($company->logo)
                                <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                            @else
                                <span class="text-xl md:text-2xl font-bold text-primary-600 dark:text-primary-400">{{ substr($company->name, 0, 1) }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-8 md:mt-10 mb-3 md:mb-4">
                    <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white line-clamp-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                        {{ $company->name }}
                    </h3>
                    <p class="text-xs md:text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5 md:mt-1">{{ $company->business_category }}</p>
                </div>
                
                <div class="space-y-2 md:space-y-3 mb-5 md:mb-6 flex-1">
                    <div class="flex items-center gap-2 md:gap-3 text-xs md:text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-2 md:p-2.5 rounded-lg border border-gray-100 dark:border-gray-700/50">
                        <i data-feather="tag" class="w-3 h-3 md:w-4 md:h-4 text-primary-500"></i>
                        <span class="capitalize font-medium">{{ $company->category }}</span>
                    </div>
                    @if($company->country)
                    <div class="flex items-center gap-2 md:gap-3 text-xs md:text-sm text-gray-600 dark:text-gray-300 px-2 md:px-2.5">
                        <i data-feather="map-pin" class="w-3 h-3 md:w-4 md:h-4 text-gray-400"></i>
                        <span>{{ $company->country }}</span>
                    </div>
                    @endif
                    <div class="flex items-center gap-2 md:gap-3 text-xs md:text-sm text-gray-600 dark:text-gray-300 px-2 md:px-2.5">
                        <i data-feather="file-text" class="w-3 h-3 md:w-4 md:h-4 text-gray-400"></i>
                        <span>{{ $company->documents->count() }} Documents</span>
                    </div>
                </div>

                <a href="{{ route('companies.show', $company) }}" class="block w-full text-center bg-white dark:bg-gray-700 text-gray-900 dark:text-white border-2 border-gray-100 dark:border-gray-600 hover:border-primary-600 dark:hover:border-primary-500 hover:text-primary-600 dark:hover:text-primary-400 font-bold py-2 md:py-2.5 rounded-xl transition-all duration-200 text-sm md:text-base">
                    Manage Company
                </a>
            </div>
        </div>
        @endforeach
        
        @if(!$isRestricted)
        {{-- Add New Card (Empty State) --}}
        <a href="{{ route('companies.create') }}" class="group bg-gray-50 dark:bg-gray-800/50 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-all duration-300 flex flex-col items-center justify-center h-full min-h-[300px] md:min-h-[380px] cursor-pointer">
            <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center mb-3 md:mb-4 group-hover:scale-110 transition-transform duration-300">
                <i data-feather="plus" class="w-6 h-6 md:w-8 md:h-8 text-gray-400 group-hover:text-primary-500 transition-colors"></i>
            </div>
            <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">Register New Company</h3>
            <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-1">Add another business entity</p>
        </a>
        @endif
    </div>

    @if($companies->isEmpty())
    <div class="text-center py-20">
        <div class="w-24 h-24 bg-primary-50 dark:bg-primary-900/20 rounded-full flex items-center justify-center mx-auto mb-6 animate-pulse">
            <i data-feather="briefcase" class="w-10 h-10 text-primary-500"></i>
        </div>
        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No companies found</h3>
        <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-8">You haven't registered any companies yet. Get started by creating your first business entity to manage your operations.</p>
        <a href="{{ route('companies.create') }}" class="inline-flex items-center justify-center px-8 py-3 text-base font-bold text-white transition-all duration-200 bg-primary-600 border border-transparent rounded-xl hover:bg-primary-700 shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 hover:-translate-y-1">
            <i data-feather="plus" class="w-5 h-5 mr-2"></i>
            Create First Company
        </a>
    </div>
    @endif
@endsection
