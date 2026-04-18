@extends('layouts.app', [
    'title' => 'Admin Node Deployment',
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Admin Nodes', 'url' => route('admin.admins.index')],
        ['name' => 'Deployment', 'url' => null],
    ]
])

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-12">
        <div class="flex items-center gap-3 mb-1">
            <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">DEPLOYMENT</span>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">System Controller Node</span>
        </div>
        <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-4">
            Provision <span class="text-primary-600">Admin Privileges</span>
        </h1>
        <p class="text-gray-500 font-medium lowercase">Initialize a new root-level administrative node on the platform network.</p>
    </div>

    <form action="{{ route('admin.admins.store') }}" method="POST">
        @csrf
        
        <div class="space-y-10">
            {{-- Administrative Identity --}}
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 p-10 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 text-gray-50/50 dark:text-gray-900/50 pointer-events-none">
                    <i data-feather="key" class="w-32 h-32"></i>
                </div>
                
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-10 relative z-10">Root Credentials</h3>
                
                <div class="space-y-8 relative z-10">
                    <div>
                        <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Operator Name</label>
                        <input type="text" name="name" id="name" required value="{{ old('name') }}"
                            class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl p-5 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                            placeholder="OPERATOR IDENTIFIER">
                        @error('name') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">System Email</label>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl p-5 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                                placeholder="name@system.local">
                            @error('email') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Root Passphrase</label>
                            <input type="password" name="password" id="password" required
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl p-5 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                                placeholder="SECRET ACCESS KEY">
                            @error('password') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Privilege Warning --}}
            <div class="bg-red-50 dark:bg-red-900/10 rounded-[2.5rem] p-10 border border-red-100 dark:border-red-900/30">
                <div class="flex gap-6">
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-red-600 shadow-sm shrink-0">
                        <i data-feather="alert-octagon" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-red-700 dark:text-red-400 uppercase tracking-widest mb-2">High Privilege Access</h4>
                        <p class="text-[10px] font-bold text-red-600/70 uppercase leading-relaxed">
                            Deploying an admin node grants absolute authority over the platform ecosystem, including financial audit logs, company verification terminals, and system-wide configurations. Ensure operator identity is verified.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Final Controls --}}
            <div class="flex items-center justify-between gap-6 pt-6">
                <a href="{{ route('admin.admins.index') }}" class="h-16 px-10 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 text-[11px] font-black text-gray-400 uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                    Discard Draft
                </a>
                <button type="submit" class="h-16 flex-1 bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-gray-900/20 hover:bg-black transition-all active:scale-[0.98]">
                    Establish Root Access
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
