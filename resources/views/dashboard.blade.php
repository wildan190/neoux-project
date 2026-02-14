@extends('layouts.app', [
    'title' => 'Dashboard',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Dashboard', 'url' => '#']
    ]
])

@section('content')

@php
    $user = auth()->user();
    $hasCompanies = $companies->isNotEmpty();
    $isOwner = $user->ownedCompanies()->exists();
    $isRestricted = $hasCompanies && !$isOwner;
@endphp

@if($companies->isEmpty())
    {{-- No Companies - Show Create Button --}}
    <div class="flex flex-col items-center justify-center min-h-[60vh] p-4">
        <div class="text-center max-w-md w-full bg-white dark:bg-gray-800 rounded-3xl shadow-xl p-8 md:p-12 border border-gray-100 dark:border-gray-700">
            <div class="w-20 h-20 bg-primary-50 dark:bg-primary-900/20 rounded-2xl flex items-center justify-center mx-auto mb-6">
                <i data-feather="briefcase" class="w-10 h-10 text-primary-600 dark:text-primary-400"></i>
            </div>
            <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mb-3 tracking-tight">No Companies Yet</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-8 leading-relaxed">You haven't registered any companies. Create your first company entity to get started with the platform.</p>
            <a href="{{ route('companies.create') }}" class="w-full inline-flex items-center justify-center px-6 py-3.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 group">
                <i data-feather="plus" class="w-5 h-5 mr-2 group-hover:scale-110 transition-transform"></i>
                Create New Company
            </a>
        </div>
    </div>
@else
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Select Company</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Choose a company workspace to continue working.</p>
            </div>
            @if(!$isRestricted)
            <a href="{{ route('companies.create') }}" class="hidden md:inline-flex items-center px-5 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-medium rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm">
                <i data-feather="plus" class="w-4 h-4 mr-2"></i>
                New Company
            </a>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            @foreach($companies as $company)
                <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-black/30 transition-all duration-300 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col h-full transform hover:-translate-y-1">
                    {{-- Cover Image --}}
                    <div class="h-32 md:h-40 w-full relative overflow-hidden bg-gray-100 dark:bg-gray-700">
                        <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=800&q=80" 
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

                    {{-- Company Info --}}
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
                            <h3 class="text-lg md:text-xl font-bold text-gray-900 dark:text-white line-clamp-1 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $company->name }}</h3>
                            <p class="text-xs md:text-sm font-medium text-gray-500 dark:text-gray-400 mt-0.5 md:mt-1">{{ $company->business_category }}</p>
                        </div>

                        <div class="space-y-2 md:space-y-3 mb-5 md:mb-6 flex-1">
                            <div class="flex items-center gap-2 md:gap-3 text-xs md:text-sm text-gray-600 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50 p-2 md:p-2.5 rounded-lg border border-gray-100 dark:border-gray-700/50">
                                <i data-feather="tag" class="w-3 h-3 md:w-4 md:h-4 text-primary-500"></i>
                                <span class="capitalize font-medium">{{ $company->category }}</span>
                            </div>
                        </div>

                        <form action="{{ route('dashboard.select-company', $company->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full flex items-center justify-center bg-primary-600 text-white font-bold py-2.5 md:py-3 rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/20 hover:shadow-primary-500/40 text-sm md:text-base group-hover:translate-y-0.5">
                                <i data-feather="log-in" class="w-4 h-4 md:w-5 md:h-5 mr-2"></i>
                                Login as Company
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach

            @if(!$isRestricted)
            {{-- Add New Company Card --}}
            <a href="{{ route('companies.create') }}" class="group bg-gray-50 dark:bg-gray-800/50 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-700 hover:border-primary-500 dark:hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-all duration-300 flex flex-col items-center justify-center h-full min-h-[300px] md:min-h-[380px] cursor-pointer">
                <div class="w-12 h-12 md:w-16 md:h-16 rounded-full bg-white dark:bg-gray-800 shadow-sm flex items-center justify-center mb-3 md:mb-4 group-hover:scale-110 transition-transform duration-300">
                    <i data-feather="plus" class="w-6 h-6 md:w-8 md:h-8 text-gray-400 group-hover:text-primary-500 transition-colors"></i>
                </div>
                <h3 class="text-base md:text-lg font-bold text-gray-900 dark:text-white group-hover:text-primary-600 transition-colors">Register New Company</h3>
                <p class="text-xs md:text-sm text-gray-500 dark:text-gray-400 mt-1">Add another business entity</p>
            </a>
            @endif
        </div>
    </div>
@endif

@endsection
