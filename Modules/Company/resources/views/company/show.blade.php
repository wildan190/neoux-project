@extends('layouts.app', [
    'title' => 'Entity Profile: ' . $company->name,
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Workspaces', 'url' => route('companies.index')],
        ['name' => 'Entity Node', 'url' => null],
    ]
])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 pb-20">
    
    {{-- ENTITY HEADER --}}
    <div class="flex flex-col md:flex-row items-center md:items-end gap-6 md:gap-10 bg-white dark:bg-gray-900 p-8 md:p-10 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary-500/5 rounded-full blur-3xl pointer-events-none transition-opacity group-hover:opacity-100"></div>
        
        {{-- Logo Cluster --}}
        <div class="relative shrink-0">
            <div class="w-32 h-32 md:w-40 md:h-40 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 p-6 flex items-center justify-center shadow-inner group-hover:border-primary-500/30 transition-all">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" class="w-full h-full object-contain filter dark:brightness-90">
                @else
                    <i data-feather="briefcase" class="w-12 h-12 text-gray-300 dark:text-gray-600"></i>
                @endif
            </div>
            @if($company->status === 'approved')
                <div class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 rounded-xl border-4 border-white dark:border-gray-900 flex items-center justify-center text-white shadow-lg">
                    <i data-feather="check" class="w-5 h-5"></i>
                </div>
            @endif
        </div>

        <div class="flex-1 text-center md:text-left">
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-3 mb-4">
                <span class="px-3 py-1 bg-gray-900 dark:bg-gray-800 text-white dark:text-gray-300 rounded-lg text-[9px] font-black uppercase tracking-widest border border-gray-800 dark:border-gray-700">
                    {{ strtoupper($company->category) }} NODE
                </span>
                <div class="h-1.5 w-1.5 rounded-full bg-gray-300 dark:bg-gray-700"></div>
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ $company->tag ?: 'GENERAL_ENTITY' }}</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-6 group-hover:text-primary-600 transition-colors">
                {{ $company->name }}
            </h1>
            <div class="flex flex-wrap items-center justify-center md:justify-start gap-x-8 gap-y-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                        <i data-feather="hash" class="w-3.5 h-3.5"></i>
                    </div>
                    <span class="text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest">ID: {{ $company->registration_number }}</span>
                </div>
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                        <i data-feather="map-pin" class="w-3.5 h-3.5"></i>
                    </div>
                    <span class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest truncate max-w-[250px]">{{ $company->address ?: 'NO PRIMARY ADDRESS' }}</span>
                </div>
            </div>
        </div>

        <div class="flex gap-4 self-center md:self-end">
            @if($company->status !== 'pending')
                <a href="{{ route('companies.edit', $company) }}" class="h-14 px-8 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-all active:scale-95 shadow-sm">
                    Configure Node
                </a>
            @endif
            <a href="{{ route('company.dashboard', ['selected_company_id' => $company->id]) }}" class="h-14 px-10 flex items-center bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                Enter Workspace
            </a>
        </div>
    </div>

    @if($company->status === 'pending')
        <div class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl p-6 md:p-8 border border-amber-100 dark:border-amber-900/30">
            <div class="flex flex-col md:flex-row gap-6 items-center">
                <div class="w-12 h-12 bg-white dark:bg-amber-900/40 rounded-xl flex items-center justify-center text-amber-600 shadow-sm shrink-0">
                    <i data-feather="loader" class="w-6 h-6 animate-spin"></i>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h4 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest mb-1">Audit Synchronization Pending</h4>
                    <p class="text-[10px] font-bold text-gray-500 dark:text-amber-500/70 uppercase leading-relaxed tracking-tight">
                        Administrative verification in progress. Access to corporate parameters is restricted until verification handshake is complete.
                    </p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- LEFT COLUMN: DOSSIER --}}
        <div class="lg:col-span-12 xl:col-span-8 space-y-8">
            
            {{-- SUMMARY CARD --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 md:p-10 shadow-sm">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-50 dark:border-gray-800">
                    <div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Corporate Mission</h3>
                    </div>
                </div>
                <p class="text-sm font-bold text-gray-500 dark:text-gray-400 leading-[1.8] tracking-tight uppercase">
                    {{ $company->description ?: 'No corporate mission statement provided by the entity operator. Establishing a clear enterprise identity improves network trust and tender win rates.' }}
                </p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12 pt-12 border-t border-gray-50 dark:border-gray-800">
                    <div class="space-y-2">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Primary Sector</p>
                        <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $company->business_category ?: $company->category }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Node Creation</p>
                        <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $company->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="space-y-2">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Compliance Region</p>
                        <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $company->country ?: 'NOT SET' }}</p>
                    </div>
                </div>
            </div>

            {{-- CONTACT & COMPLIANCE GRID --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Communication Matrix --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8">Communication Matrix</h3>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-primary-600 transition-colors shadow-inner">
                                <i data-feather="mail" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Official Email</p>
                                <p class="text-xs font-black text-gray-900 dark:text-white lowercase tracking-tight truncate">{{ $company->email ?: 'NO_EMAIL_RECORDED' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-primary-600 transition-colors shadow-inner">
                                <i data-feather="globe" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Web Interface</p>
                                <p class="text-xs font-black text-gray-900 dark:text-white lowercase tracking-tight truncate">{{ $company->website ?: 'NO_WEB_INTERFACE' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-primary-600 transition-colors shadow-inner">
                                <i data-feather="phone" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Operational Line</p>
                                <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight truncate">{{ $company->phone ?: 'NO_PHONE_RECORDED' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Compliance Dossier --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8">Compliance Dossier</h3>
                    <div class="space-y-6">
                        <div class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-primary-600 transition-colors shadow-inner">
                                <i data-feather="file" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">NPWP Identification</p>
                                <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight truncate">{{ $company->npwp ?: 'UNREGISTERED_ENTITY' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-primary-600 transition-colors shadow-inner">
                                <i data-feather="award" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Verification Status</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $company->status === 'approved' ? 'bg-emerald-500' : 'bg-amber-500' }}"></span>
                                    <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $company->status }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center gap-4 group">
                            <div class="w-10 h-10 rounded-xl bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-primary-600 transition-colors shadow-inner">
                                <i data-feather="clock" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mb-1">Approval Protocol</p>
                                <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight truncate">{{ $company->approved_at ? $company->approved_at->format('M d, Y') : 'PENDING_APPROVAL' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ARTIFACT TERMINAL --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-8 md:px-10 py-8 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center">
                    <div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Verification Artifacts</h3>
                    </div>
                    <span class="px-3 py-1 bg-gray-900 dark:bg-gray-800 text-white rounded-lg text-[9px] font-black uppercase tracking-widest">{{ $company->documents->count() }} Files Active</span>
                </div>
                <div class="p-8 md:p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($company->documents as $doc)
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="flex items-center gap-4 p-5 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-transparent hover:border-primary-500/20 hover:bg-white dark:hover:bg-gray-800 transition-all group shadow-sm active:scale-[0.99]">
                                <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-primary-600 shadow-sm transition-all shrink-0">
                                    <i data-feather="file-text" class="w-5 h-5"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight truncate mb-1 group-hover:text-primary-600 transition-colors">{{ basename($doc->file_path) }}</p>
                                    <div class="flex items-center gap-2">
                                        <span class="text-[8px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">{{ strtoupper($doc->file_type) }} ARTIFACT</span>
                                        <div class="w-0.5 h-2 bg-gray-200 dark:bg-gray-700"></div>
                                        <span class="text-[8px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">SECURE_SYNC</span>
                                    </div>
                                </div>
                                <i data-feather="external-link" class="w-3.5 h-3.5 text-gray-300 group-hover:text-primary-400 transition-colors"></i>
                            </a>
                        @empty
                            <div class="col-span-full py-20 text-center bg-gray-50/50 dark:bg-gray-800/20 rounded-xl border border-dashed border-gray-100 dark:border-gray-800">
                                <div class="w-16 h-16 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-gray-200 dark:text-gray-700 mx-auto mb-6">
                                    <i data-feather="upload" class="w-8 h-8"></i>
                                </div>
                                <p class="text-[10px] font-black text-gray-300 dark:text-gray-600 uppercase tracking-[0.2em]">Zero verification artifacts uploaded</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: PERFORMANCE --}}
        <div class="lg:col-span-12 xl:col-span-4 space-y-8">
            <div class="bg-gray-900 dark:bg-black rounded-2xl p-10 text-white relative overflow-hidden shadow-xl group">
                <div class="absolute bottom-0 right-0 -mb-16 -mr-16 w-64 h-64 bg-primary-600/20 rounded-full blur-3xl pointer-events-none transition-opacity group-hover:opacity-40"></div>
                
                <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-12 relative z-10">Operational Pulse</h3>
                
                <div class="space-y-8 relative z-10">
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">Submissions</p>
                            <p class="text-3xl font-black text-white leading-none tabular-nums tracking-tighter">{{ $stats['offers_submitted'] }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">Conversions</p>
                            <p class="text-3xl font-black text-emerald-500 leading-none tabular-nums tracking-tighter">{{ $stats['offers_won'] }}</p>
                        </div>
                    </div>
                    <div class="h-px bg-white/5"></div>
                    <div class="grid grid-cols-2 gap-8">
                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">Requisitions</p>
                            <p class="text-3xl font-black text-white leading-none tabular-nums tracking-tighter">{{ $stats['total_requests'] }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-3">In_Negotiation</p>
                            <p class="text-3xl font-black text-primary-400 leading-none tabular-nums tracking-tighter">{{ $stats['active_requests'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-white/5 relative z-10">
                    <div class="flex items-center gap-3">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Network integrity verified</span>
                    </div>
                </div>
            </div>

            {{-- Operational Centers --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 md:p-10 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-50 dark:border-gray-800">
                    <div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Operational Nodes</h3>
                    </div>
                    <div class="w-8 h-8 rounded-lg bg-gray-50 dark:bg-gray-800 flex items-center justify-center text-gray-400">
                        <i data-feather="map" class="w-4 h-4"></i>
                    </div>
                </div>
                <div class="space-y-4">
                    @forelse($company->locations as $location)
                        <div class="flex items-start gap-4 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl group transition-all">
                            <i data-feather="map-pin" class="w-3.5 h-3.5 text-gray-300 group-hover:text-primary-600 transition-colors mt-0.5"></i>
                            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-tight leading-relaxed">{{ $location->address }}</p>
                        </div>
                    @empty
                        <div class="py-12 text-center">
                            <p class="text-[9px] font-black text-gray-300 dark:text-gray-700 uppercase tracking-widest">Zero regional nodes established</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
