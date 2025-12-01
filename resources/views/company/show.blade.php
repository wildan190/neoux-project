@extends('layouts.app', [
    'title' => $company->name,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Companies', 'url' => route('companies.index')],
        ['name' => $company->name, 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    {{-- Header --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
        <div class="flex flex-col md:flex-row items-center gap-6">
            <div class="w-24 h-24 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center overflow-hidden border-2 border-gray-200 dark:border-gray-600 shrink-0">
                @if($company->logo)
                    <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                @else
                    <i data-feather="briefcase" class="w-10 h-10 text-gray-400"></i>
                @endif
            </div>
            <div class="flex-1 text-center md:text-left">
                <div class="flex flex-col md:flex-row items-center gap-4 mb-2">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $company->name }}</h1>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $company->status === 'approved' ? 'bg-green-100 text-green-800' : 
                           ($company->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($company->status) }}
                    </span>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-lg">{{ $company->business_category }} • <span class="capitalize">{{ $company->category }}</span></p>
                @if($company->tag)
                <div class="mt-2 flex flex-wrap gap-2 justify-center md:justify-start">
                    @foreach(explode(',', $company->tag) as $tag)
                        <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded-md">{{ trim($tag) }}</span>
                    @endforeach
                </div>
                @endif
            </div>
            <div class="flex gap-3">
                <a href="{{ route('companies.index') }}" class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Back
                </a>
                {{-- Add Edit Button Here if needed --}}
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Description --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-feather="info" class="w-5 h-5 text-indigo-500"></i>
                    Description
                </h3>
                <p class="text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ $company->description ?? 'No description provided.' }}
                </p>
            </div>

            {{-- Locations --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-feather="map" class="w-5 h-5 text-indigo-500"></i>
                    Operation Locations
                </h3>
                @if($company->locations->count() > 0)
                    <div class="space-y-3">
                        @foreach($company->locations as $location)
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <i data-feather="map-pin" class="w-5 h-5 text-gray-400 mt-0.5"></i>
                            <span class="text-gray-700 dark:text-gray-200">{{ $location->address }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 italic">No locations added.</p>
                @endif
            </div>

            {{-- Documents --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-feather="file-text" class="w-5 h-5 text-indigo-500"></i>
                    Documents
                </h3>
                @if($company->documents->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($company->documents as $doc)
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-900/20 border border-gray-100 dark:border-gray-700 transition-colors group">
                            <div class="w-10 h-10 rounded-lg bg-white dark:bg-gray-700 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform">
                                <i data-feather="file" class="w-5 h-5 text-indigo-500"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">Document #{{ $loop->iteration }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">{{ $doc->file_type ?? 'FILE' }}</p>
                            </div>
                            <i data-feather="external-link" class="w-4 h-4 text-gray-400 group-hover:text-indigo-500"></i>
                        </a>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 dark:text-gray-400 italic">No documents uploaded.</p>
                @endif
            </div>

        </div>

        {{-- Right Column --}}
        <div class="space-y-6">
            
            {{-- Contact Info --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Contact Information</h3>
                <div class="space-y-4">
                    @if($company->email)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <i data-feather="mail" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Email</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->email }}</p>
                        </div>
                    </div>
                    @endif

                    @if($company->phone)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <i data-feather="phone" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Phone</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->phone }}</p>
                        </div>
                    </div>
                    @endif

                    @if($company->website)
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                            <i data-feather="globe" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Website</p>
                            <a href="{{ $company->website }}" target="_blank" class="text-sm font-medium text-indigo-600 hover:text-indigo-500 truncate block max-w-[200px]">{{ $company->website }}</a>
                        </div>
                    </div>
                    @endif

                    @if($company->address)
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center text-indigo-600 dark:text-indigo-400 shrink-0">
                            <i data-feather="map-pin" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Main Address</p>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->address }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Additional Info --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Details</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">NPWP</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->npwp ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Country</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->country ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Registered Date</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->registered_date ? \Carbon\Carbon::parse($company->registered_date)->format('d M Y') : '-' }}</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
