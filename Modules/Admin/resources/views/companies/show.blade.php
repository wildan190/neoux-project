@extends('layouts.app', [
    'title' => 'Entity Review: ' . $company->name,
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Companies', 'url' => route('admin.companies.index')],
        ['name' => 'Audit', 'url' => null],
    ]
])

@section('content')
<div class="max-w-5xl mx-auto space-y-10">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div class="flex items-center gap-8">
            <div class="w-24 h-24 rounded-[2rem] bg-gray-50 dark:bg-gray-900 border border-gray-100 dark:border-gray-800 flex items-center justify-center p-4 shadow-xl">
                @if($company->logo)
                    <img src="{{ asset('storage/' . $company->logo) }}" class="w-full h-full object-contain">
                @else
                    <i data-feather="briefcase" class="w-10 h-10 text-gray-300"></i>
                @endif
            </div>
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">ENTITY AUDIT</span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">ID: {{ $company->id }}</span>
                </div>
                <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-2">{{ $company->name }}</h1>
                <div class="flex items-center gap-4">
                    <span class="text-[11px] font-black text-primary-600 uppercase tracking-[0.2em] leading-none">{{ $company->category }} SECTOR</span>
                    <div class="w-1.5 h-1.5 rounded-full bg-gray-200"></div>
                    <span class="text-[11px] font-bold text-gray-500 uppercase tracking-widest leading-none">Registered {{ $company->created_at->format('M d, Y') }}</span>
                </div>
            </div>
        </div>

        @if($company->status === 'pending')
            <div class="flex gap-4">
                <form action="{{ route('admin.companies.decline', $company) }}" method="POST">
                    @csrf
                    <button type="submit" class="h-14 px-8 bg-white border border-red-200 text-red-600 text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-red-50 transition-all">
                        Decline Entry
                    </button>
                </form>
                <form action="{{ route('admin.companies.approve', $company) }}" method="POST">
                    @csrf
                    <button type="submit" class="h-14 px-10 bg-emerald-600 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-emerald-600/20 hover:bg-emerald-700 transition-all">
                        Verify & Activate
                    </button>
                </form>
            </div>
        @else
            <div class="bg-gray-50 dark:bg-gray-900/50 px-8 py-4 rounded-2xl border border-transparent flex items-center gap-4">
                <span class="w-2 h-2 rounded-full bg-{{ $company->status === 'active' ? 'emerald' : 'red' }}-500"></span>
                <span class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Audit Terminal: {{ strtoupper($company->status) }}</span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        {{-- Left: Details --}}
        <div class="lg:col-span-2 space-y-10">
            {{-- Profile --}}
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 p-10 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-10">Corporate Dossier</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Legal Identifier</p>
                        <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $company->registration_number }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Primary Industry</p>
                        <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $company->category }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Operations Center</p>
                        <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $company->address ?: 'NOT SPECIFIED' }}</p>
                    </div>
                    <div>
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-2">Registered Entity Owner</p>
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full bg-primary-100 flex items-center justify-center text-[10px] font-black text-primary-600 shadow-inner">
                                {{ substr($company->owner->name ?? 'U', 0, 1) }}
                            </div>
                            <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $company->owner->name ?? 'UNKNOWN' }}</p>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-12 border-t border-gray-50 dark:border-gray-700">
                    <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-4">Enterprise Summary</p>
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 leading-relaxed uppercase tracking-tight">
                        {{ $company->description ?: 'NO DETAILED DESCRIPTION PROVIDED BY THE CORPORATE ENTITY.' }}
                    </p>
                </div>
            </div>

            {{-- Documents --}}
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="px-10 py-8 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Technical Documents</h3>
                    <span class="px-3 py-1 bg-gray-50 dark:bg-gray-900 rounded-lg text-[9px] font-black text-gray-400 uppercase tracking-widest">{{ $company->documents->count() }} Artifacts</span>
                </div>
                <div class="p-4 md:p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($company->documents as $doc)
                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="flex items-center gap-5 p-5 bg-gray-50 dark:bg-gray-900/50 rounded-2xl border border-transparent hover:border-primary-300 hover:bg-white dark:hover:bg-gray-900 transition-all group shadow-inner">
                                <div class="w-12 h-12 rounded-xl bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 group-hover:text-primary-600 shadow-sm transition-all">
                                    <i data-feather="file-text" class="w-6 h-6"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight truncate">{{ basename($doc->file_path) }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $doc->file_type }} Artifact</p>
                                </div>
                                <i data-feather="external-link" class="w-4 h-4 text-gray-200 group-hover:text-primary-400 transition-colors"></i>
                            </a>
                        @empty
                            <div class="col-span-full py-12 text-center bg-gray-50/50 dark:bg-gray-900/20 rounded-3xl border border-dashed border-gray-100 dark:border-gray-800">
                                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">No documentation uploaded</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: Status Audit --}}
        <div class="space-y-8">
            <div class="bg-gray-900 rounded-[3rem] p-10 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-12 -mt-12 w-32 h-32 bg-primary-600/20 rounded-full blur-[40px]"></div>
                
                <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-10 relative z-10">Review Audit Trail</h3>
                
                <div class="space-y-8 relative z-10">
                    <div class="border-l-2 border-white/5 pl-6 pb-2">
                        <p class="text-[9px] font-black text-gray-500 uppercase tracking-widest mb-2 leading-none">Submission Node</p>
                        <p class="text-xs font-black text-white uppercase tracking-tight leading-none mb-1">Entity created in system</p>
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest leading-none">{{ $company->created_at->format('M d, H:i') }}</p>
                    </div>

                    @if($company->status !== 'pending')
                    <div class="border-l-2 border-emerald-500/50 pl-6 pb-2">
                        <p class="text-[9px] font-black text-emerald-500/50 uppercase tracking-widest mb-2 leading-none">Verification Terminal</p>
                        <p class="text-xs font-black text-white uppercase tracking-tight leading-none mb-1">Status set to {{ strtoupper($company->status) }}</p>
                        <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest leading-none">
                            {{ $company->approved_at ? $company->approved_at->format('M d, H:i') : ($company->declined_at ? $company->declined_at->format('M d, H:i') : 'N/A') }}
                        </p>
                    </div>
                    @else
                    <div class="border-l-2 border-yellow-500/50 pl-6 pb-2">
                        <p class="text-[9px] font-black text-yellow-500/50 uppercase tracking-widest mb-2 leading-none">Pending Analysis</p>
                        <p class="text-xs font-black text-white/40 uppercase tracking-tight leading-none mb-1">Awaiting administrative decision</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">Security Disclosure</h3>
                <p class="text-[10px] font-bold text-gray-500 uppercase leading-relaxed mb-10">
                    Verification of corporate documentation is required to ensure platform integrity and cross-sector transparency. Approval grants the entity full access to the tender ecosystem.
                </p>
                <div class="p-6 bg-red-50 dark:bg-red-900/10 rounded-2xl border border-red-100 dark:border-red-900/30">
                    <div class="flex gap-4">
                        <i data-feather="shield-off" class="w-5 h-5 text-red-600 shrink-0"></i>
                        <p class="text-[9px] font-black text-red-700 dark:text-red-400 uppercase leading-tight tracking-widest">High vulnerability: Awaiting identification review</p>
                    </div>
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
