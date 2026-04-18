@extends('layouts.app', [
    'title' => 'Select Workspace',
    'hide_sidebar' => true,
    'hide_header' => true
])

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50 dark:bg-gray-900 overflow-hidden relative">
    {{-- Abstract background patterns --}}
    <div class="absolute top-0 right-0 -mr-32 -mt-32 w-96 h-96 bg-primary-600/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 -ml-32 -mb-32 w-96 h-96 bg-indigo-600/5 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="w-full max-w-5xl relative z-10">
        {{-- Header --}}
        <div class="text-center mb-16">
            <div class="w-20 h-20 bg-gray-900 rounded-[2rem] flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-gray-900/20">
                <i data-feather="box" class="w-10 h-10 text-primary-500"></i>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-4">
                Global <span class="text-primary-600">Access Point</span>
            </h1>
            <p class="text-xs font-black text-gray-400 uppercase tracking-[0.2em] leading-none">Initialize Enterprise Workspace</p>
        </div>

        {{-- Company Selection Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($companies as $company)
                <form action="{{ route('dashboard.select-company', $company->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full text-left bg-white dark:bg-gray-800 rounded-[3rem] p-10 border border-gray-100 dark:border-gray-800 shadow-sm transition-all duration-500 hover:shadow-2xl hover:shadow-primary-600/10 hover:border-primary-500 group relative overflow-hidden h-full flex flex-col">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-primary-50 dark:bg-primary-900/10 rounded-full blur-3xl group-hover:scale-150 transition-transform duration-700"></div>
                        
                        <div class="mb-10 relative z-10">
                            <div class="w-20 h-20 rounded-[1.75rem] bg-gray-50 dark:bg-gray-900 flex items-center justify-center p-3 shadow-inner group-hover:scale-105 transition-transform duration-500">
                                @if($company->logo)
                                    <img src="{{ asset('storage/' . $company->logo) }}" class="w-full h-full object-contain">
                                @else
                                    <i data-feather="briefcase" class="w-8 h-8 text-gray-300"></i>
                                @endif
                            </div>
                        </div>

                        <div class="mb-10 flex-1 relative z-10">
                            <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2 group-hover:text-primary-600 transition-colors">{{ $company->name }}</h3>
                            <div class="flex items-center gap-3">
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-500 text-[8px] font-black rounded uppercase tracking-widest">{{ strtoupper($company->category) }} Node</span>
                                <div class="w-1 h-1 rounded-full bg-gray-200"></div>
                                <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $company->registration_number }}</span>
                            </div>
                        </div>

                        <div class="pt-8 border-t border-gray-50 dark:border-gray-700 mt-auto relative z-10 flex items-center justify-between">
                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] group-hover:text-primary-600 transition-colors">Enter Node</span>
                            <div class="w-10 h-10 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center text-gray-200 group-hover:bg-primary-600 group-hover:text-white transition-all">
                                <i data-feather="arrow-right" class="w-5 h-5"></i>
                            </div>
                        </div>
                    </button>
                </form>
            @endforeach

            {{-- Create New Option --}}
            <a href="{{ route('companies.create') }}" class="flex flex-col items-center justify-center p-12 bg-gray-50/50 dark:bg-gray-900/30 rounded-[3rem] border-2 border-dashed border-gray-100 dark:border-gray-800 hover:border-primary-300 hover:bg-white dark:hover:bg-gray-800 transition-all duration-500 group h-full">
                <div class="w-20 h-20 rounded-[2rem] bg-white dark:bg-gray-800 flex items-center justify-center mb-8 border border-gray-100 dark:border-gray-800 text-gray-200 group-hover:text-primary-600 group-hover:border-primary-500 transition-all shadow-sm">
                    <i data-feather="plus" class="w-10 h-10"></i>
                </div>
                <h3 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.3em] mb-2">Establish New Identity</h3>
                <p class="text-[9px] font-bold text-gray-300 uppercase tracking-widest leading-relaxed text-center px-6">Deploy a fresh corporate node to the marketplace network.</p>
            </a>
        </div>

        {{-- Footer --}}
        <div class="mt-20 flex flex-col items-center gap-6 opacity-40">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.3em]">Network Synchronized</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-[10px] font-black text-red-600 uppercase tracking-widest hover:text-red-700 transition-colors">Terminate Session</button>
            </form>
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
