@extends('layouts.app', [
    'title' => $company->name,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Companies', 'url' => route('companies.index')],
        ['name' => $company->name, 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6 md:space-y-8">
    
    {{-- Hero Header --}}
    <div class="relative rounded-2xl md:rounded-3xl overflow-hidden bg-white dark:bg-gray-800 shadow-xl mx-4 md:mx-0">
        {{-- Cover Image --}}
        <div class="h-64 md:h-96 w-full relative">
            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=2000&q=80" 
                 alt="Company Cover" 
                 class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
            
            {{-- Top Actions --}}
            <div class="absolute top-4 right-4 md:top-6 md:right-6 flex gap-2 md:gap-3 z-10">
                <a href="{{ route('companies.index') }}" class="px-3 py-1.5 md:px-4 md:py-2 bg-white/10 backdrop-blur-md text-white rounded-lg md:rounded-xl hover:bg-white/20 transition-all font-medium text-xs md:text-sm flex items-center gap-2 border border-white/10">
                    <i data-feather="arrow-left" class="w-3 h-3 md:w-4 md:h-4"></i>
                    Back
                </a>
                @if($company->status !== 'pending')
                    <a href="{{ route('companies.edit', $company) }}" class="px-3 py-1.5 md:px-4 md:py-2 bg-primary-600/90 backdrop-blur-md text-white rounded-lg md:rounded-xl hover:bg-primary-600 transition-all font-medium text-xs md:text-sm flex items-center gap-2 shadow-lg shadow-primary-500/20 border border-transparent">
                        <i data-feather="edit-2" class="w-3 h-3 md:w-4 md:h-4"></i>
                        Edit
                    </a>
                @else
                    <div class="px-3 py-1.5 md:px-4 md:py-2 bg-gray-500/50 backdrop-blur-md text-white/80 rounded-lg md:rounded-xl cursor-not-allowed flex items-center gap-2 border border-white/10" title="Cannot edit while status is pending">
                        <i data-feather="lock" class="w-3 h-3 md:w-4 md:h-4"></i>
                        Locked
                    </div>
                @endif
            </div>
        </div>

        {{-- Profile Info Overlay --}}
        <div class="absolute bottom-0 left-0 w-full p-4 md:p-10 bg-gradient-to-t from-gray-900/95 to-transparent">
            <div class="flex flex-col md:flex-row items-start md:items-end gap-4 md:gap-8">
                {{-- Logo --}}
                <div class="w-24 h-24 md:w-36 md:h-36 rounded-xl md:rounded-2xl bg-white dark:bg-gray-800 p-1 md:p-1.5 shadow-2xl ring-2 md:ring-4 ring-white/10 dark:ring-gray-700/50 shrink-0 -mb-8 md:mb-0 relative z-10">
                    <div class="w-full h-full rounded-lg md:rounded-xl bg-gray-50 dark:bg-gray-700 flex items-center justify-center overflow-hidden">
                        @if($company->logo)
                            <img src="{{ Storage::url($company->logo) }}" alt="{{ $company->name }}" class="w-full h-full object-cover">
                        @else
                            <span class="text-3xl md:text-5xl font-bold text-primary-600 dark:text-primary-400">{{ substr($company->name, 0, 1) }}</span>
                        @endif
                    </div>
                </div>
                
                {{-- Text Info --}}
                <div class="flex-1 mb-0 md:mb-2 w-full">
                    <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 mb-2 md:mb-3 mt-8 md:mt-0">
                        <h1 class="text-2xl md:text-4xl font-bold text-white shadow-sm tracking-tight line-clamp-1">{{ $company->name }}</h1>
                        <span class="inline-flex px-2 py-0.5 md:px-3 md:py-1 text-xs font-bold uppercase tracking-wider rounded-full backdrop-blur-md border border-white/20 self-start
                            {{ $company->status === 'approved' ? 'bg-green-500/80 text-white' : 
                               ($company->status === 'pending' ? 'bg-yellow-500/80 text-white' : 'bg-red-500/80 text-white') }}">
                            {{ ucfirst($company->status) }}
                        </span>
                    </div>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-white/90 text-sm md:text-lg font-medium">
                        <span>{{ $company->business_category }}</span>
                        <span class="hidden md:inline w-1.5 h-1.5 rounded-full bg-white/50"></span>
                        <span class="capitalize">{{ $company->category }}</span>
                        @if($company->country)
                            <span class="hidden md:inline w-1.5 h-1.5 rounded-full bg-white/50"></span>
                            <span class="flex items-center gap-1"><i data-feather="map-pin" class="w-3 h-3 md:w-4 md:h-4"></i> {{ $company->country }}</span>
                        @endif
                    </div>
                    
                    @if($company->tag)
                    <div class="mt-3 md:mt-4 flex flex-wrap gap-2">
                        @foreach(explode(',', $company->tag) as $tag)
                            <span class="px-2 py-1 md:px-3 bg-white/10 backdrop-blur-md text-white text-[10px] md:text-xs font-medium rounded-md md:rounded-lg border border-white/10 hover:bg-white/20 transition-colors cursor-default">{{ trim($tag) }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="px-4 md:px-0 pb-12">
        <!-- History Track (Jejak Riwayat) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            {{-- Offers Submitted --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border-l-4 border-blue-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Penawaran Diajukan</p>
                    <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <i data-feather="send" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['offers_submitted'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Total offers sent</p>
            </div>

            {{-- Offers Won --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border-l-4 border-green-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Penawaran Dimenangkan</p>
                    <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-green-600 dark:text-green-400">
                        <i data-feather="award" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['offers_won'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Offers accepted</p>
            </div>

            {{-- Total Requests --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border-l-4 border-purple-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Permintaan</p>
                    <div class="w-8 h-8 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <i data-feather="shopping-cart" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_requests'] }}</p>
                <p class="text-xs text-gray-500 mt-1">All PRs created</p>
            </div>

            {{-- Active Requests --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-5 border-l-4 border-orange-500">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Permintaan Aktif</p>
                    <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center text-orange-600 dark:text-orange-400">
                        <i data-feather="activity" class="w-4 h-4"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active_requests'] }}</p>
                <p class="text-xs text-gray-500 mt-1">Open / Pending</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        
        {{-- Left Column --}}
        <div class="lg:col-span-2 space-y-8">
            
            {{-- Description --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-8 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <div class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <i data-feather="info" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                    </div>
                    About Company
                </h3>
                <div class="prose dark:prose-invert max-w-none text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ $company->description ?? 'No description provided.' }}
                </div>
            </div>

            {{-- Locations --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-8 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <div class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <i data-feather="map" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                    </div>
                    Operation Locations
                </h3>
                @if($company->locations->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($company->locations as $location)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800 transition-colors">
                            <i data-feather="map-pin" class="w-5 h-5 text-primary-500 mt-1 shrink-0"></i>
                            <span class="text-gray-700 dark:text-gray-200 font-medium">{{ $location->address }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                        <p class="text-gray-500 dark:text-gray-400 italic">No locations added yet.</p>
                    </div>
                @endif
            </div>

            {{-- Documents --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-8 border border-gray-100 dark:border-gray-700">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                    <div class="p-2 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                        <i data-feather="file-text" class="w-5 h-5 text-primary-600 dark:text-primary-400"></i>
                    </div>
                    Legal Documents
                </h3>
                @if($company->documents->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($company->documents as $doc)
                        <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/10 border border-gray-200 dark:border-gray-700 hover:border-primary-200 dark:hover:border-primary-800 transition-all group shadow-sm hover:shadow-md">
                            <div class="w-12 h-12 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                                <i data-feather="file" class="w-6 h-6 text-primary-600 dark:text-primary-400"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-base font-semibold text-gray-900 dark:text-white truncate">Document #{{ $loop->iteration }}</p>
                                <p class="text-xs font-bold text-primary-600 dark:text-primary-400 uppercase tracking-wide mt-0.5">{{ $doc->file_type ?? 'FILE' }}</p>
                            </div>
                            <i data-feather="external-link" class="w-4 h-4 text-gray-400 group-hover:text-primary-500"></i>
                        </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                        <p class="text-gray-500 dark:text-gray-400 italic">No documents uploaded.</p>
                    </div>
                @endif
            </div>

        </div>

        {{-- Right Column --}}
        <div class="space-y-8">
            
            {{-- Contact Info --}}
            <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl p-6 border border-gray-100 dark:border-gray-700 sticky top-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 pb-4 border-b border-gray-100 dark:border-gray-700">Contact Information</h3>
                <div class="space-y-6">
                    @if($company->email)
                    <div class="flex items-start gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 transition-colors">
                            <i data-feather="mail" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Email Address</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5 break-all">{{ $company->email }}</p>
                        </div>
                    </div>
                    @endif

                    @if($company->phone)
                    <div class="flex items-start gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 transition-colors">
                            <i data-feather="phone" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Phone Number</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5">{{ $company->phone }}</p>
                        </div>
                    </div>
                    @endif

                    @if($company->website)
                    <div class="flex items-start gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 transition-colors">
                            <i data-feather="globe" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Website</p>
                            <a href="{{ $company->website }}" target="_blank" class="text-sm font-semibold text-primary-600 hover:text-primary-500 truncate block max-w-[200px] mt-0.5 underline decoration-primary-200 hover:decoration-primary-500 underline-offset-2 transition-all">{{ $company->website }}</a>
                        </div>
                    </div>
                    @endif

                    @if($company->address)
                    <div class="flex items-start gap-4 group">
                        <div class="w-10 h-10 rounded-xl bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 shrink-0 group-hover:bg-primary-100 dark:group-hover:bg-primary-900/50 transition-colors">
                            <i data-feather="map-pin" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Main Address</p>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white mt-0.5 leading-relaxed">{{ $company->address }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-4">Registration Details</h4>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">NPWP</span>
                            <span class="text-sm font-mono font-medium text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $company->npwp ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Country</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->country ?? '-' }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Registered</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $company->registered_date ? \Carbon\Carbon::parse($company->registered_date)->format('d M Y') : '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
