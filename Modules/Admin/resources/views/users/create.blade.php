@extends('layouts.app', [
    'title' => 'Provision User',
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'System Users', 'url' => route('admin.users.index')],
        ['name' => 'Provisioning', 'url' => null],
    ]
])

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-12">
        <div class="flex items-center gap-3 mb-1">
            <span class="px-3 py-1 bg-indigo-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">PROVISIONING</span>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">New System Account</span>
        </div>
        <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-4">
            Create <span class="text-primary-600">User Node</span>
        </h1>
        <p class="text-gray-500 font-medium lowercase">Initialize a new secure account on the platform network.</p>
    </div>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf
        
        <div class="space-y-10">
            {{-- Primary Credentials --}}
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 p-10 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-10">Account Credentials</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <div>
                        <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Email Address</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-primary-500 transition-colors pointer-events-none">
                                <i data-feather="mail" class="w-5 h-5"></i>
                            </div>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl pl-16 pr-6 py-5 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                                placeholder="name@domain.com">
                        </div>
                        @error('email') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Security Passphrase</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-primary-500 transition-colors pointer-events-none">
                                <i data-feather="shield" class="w-5 h-5"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl pl-16 pr-6 py-5 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                                placeholder="MINIMUM 8 CHARACTERS">
                        </div>
                        @error('password') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Policy Notice --}}
            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-[2.5rem] p-10 border border-transparent">
                <div class="flex gap-6">
                    <div class="w-12 h-12 bg-white dark:bg-gray-800 rounded-2xl flex items-center justify-center text-primary-500 shadow-sm shrink-0">
                        <i data-feather="info" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <h4 class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-widest mb-2">Automated Activation</h4>
                        <p class="text-[10px] font-bold text-gray-500 uppercase leading-relaxed">
                            Upon creation, the user will be required to complete their full identification profile during their first session initiation. Access will be initially restricted until verification is complete.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Final Controls --}}
            <div class="flex items-center justify-between gap-6 pt-6">
                <a href="{{ route('admin.users.index') }}" class="h-16 px-10 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 text-[11px] font-black text-gray-400 uppercase tracking-widest rounded-2xl hover:bg-gray-50 transition-all">
                    Discard Draft
                </a>
                <button type="submit" class="h-16 flex-1 bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-[0.98]">
                    Establish New Access
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
