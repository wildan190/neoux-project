@extends('layouts.app', [
    'title' => 'Establish Workspace Access',
    'hide_sidebar' => true,
    'hide_header' => true
])

@section('content')
<div class="min-h-screen flex items-center justify-center p-6 bg-gray-50 dark:bg-gray-900">
    <div class="w-full max-w-2xl">
        {{-- Header --}}
        <div class="text-center mb-12">
            <div class="w-24 h-24 bg-primary-600 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 shadow-2xl shadow-primary-600/20">
                <i data-feather="key" class="w-12 h-12 text-white"></i>
            </div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-4">
                Establish <span class="text-primary-600">Secure Access</span>
            </h1>
            <p class="text-sm font-bold text-gray-400 uppercase tracking-widest leading-none">Complete your identification to join the workspace</p>
        </div>

        {{-- Invitation Info --}}
        <div class="bg-gray-900 rounded-[3rem] p-12 text-white shadow-2xl mb-10 relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary-600/10 rounded-full blur-[80px] pointer-events-none"></div>
            
            <div class="flex flex-col md:flex-row items-center gap-10 relative z-10">
                <div class="w-20 h-20 rounded-3xl bg-white/10 flex items-center justify-center p-4 backdrop-blur-md shrink-0">
                    @if($invitation->company->logo)
                        <img src="{{ asset('storage/' . $invitation->company->logo) }}" class="w-full h-full object-contain">
                    @else
                        <i data-feather="briefcase" class="w-8 h-8 text-primary-400"></i>
                    @endif
                </div>
                <div class="text-center md:text-left">
                    <p class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-2 leading-none">Pending Invitation</p>
                    <h2 class="text-2xl font-black text-white uppercase tracking-tight mb-4 leading-none">{{ $invitation->company->name }}</h2>
                    <div class="flex items-center justify-center md:justify-start gap-4">
                        <span class="px-3 py-1 bg-primary-600 text-[9px] font-black rounded-lg uppercase tracking-widest text-white">Role: {{ strtoupper($invitation->role) }}</span>
                        <div class="w-1.5 h-1.5 rounded-full bg-white/20"></div>
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $invitation->email }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Completion Form --}}
        <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] p-12 border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden">
            <div class="absolute bottom-0 right-0 p-12 text-gray-50/50 dark:text-gray-900/50 pointer-events-none">
                <i data-feather="user-plus" class="w-32 h-32"></i>
            </div>
            
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-12">Initialize Personal Node</h3>

            <form action="{{ route('invitation.accept.process') }}" method="POST" class="space-y-8 relative z-10">
                @csrf
                <input type="hidden" name="token" value="{{ $invitation->token }}">
                
                <div class="space-y-6">
                    <div>
                        <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Full Identity Name</label>
                        <div class="relative group">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-primary-500 transition-colors pointer-events-none">
                                <i data-feather="user" class="w-5 h-5"></i>
                            </div>
                            <input type="text" name="name" id="name" required
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-[1.5rem] pl-16 pr-6 py-6 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                                placeholder="OPERATOR NAME">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <label for="password" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Security Passphrase</label>
                            <input type="password" name="password" id="password" required
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-[1.5rem] px-6 py-6 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                                placeholder="MINIMUM 8 CHARS">
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Confirm Passphrase</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-[1.5rem] px-6 py-6 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                                placeholder="REPEAT PHRASE">
                        </div>
                    </div>
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full h-20 bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-[1.75rem] shadow-2xl shadow-primary-600/40 hover:bg-primary-700 transition-all active:scale-[0.98]">
                        Establish Account & Join Workspace
                    </button>
                    <p class="text-center mt-6 text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-relaxed">
                        By establishing this account, you agree to the platform's security protocols and data handling standards for enterprise nodes.
                    </p>
                </div>
            </form>
        </div>

        {{-- Help Footer --}}
        <div class="mt-12 flex gap-4 items-center justify-center opacity-40">
            <i data-feather="help-circle" class="w-4 h-4 text-gray-400"></i>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-[0.2em]">Required help establishing node access?</p>
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
