@extends('layouts.guest', [
    'title' => 'Admin Authentication'
])

@section('content')
<div class="min-h-screen flex">
    {{-- Left Side: Form Panel --}}
    <div class="w-full lg:w-[60%] flex items-center justify-center p-8 md:p-12 lg:p-20 bg-white dark:bg-gray-900 overflow-y-auto">
        <div class="w-full max-w-lg">
            {{-- Brand / Node Identifier (Mobile Only) --}}
            <div class="lg:hidden text-center mb-10">
                <div class="w-20 h-20 bg-gray-900 dark:bg-white/10 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-2xl">
                    <i data-feather="terminal" class="w-10 h-10 text-primary-500"></i>
                </div>
            </div>

            <div class="mb-12">
                <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-4">
                    Administrative <span class="text-primary-600">Terminal</span>
                </h1>
                <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] leading-relaxed">
                    Masuk ke pusat kendali sistem untuk mengelola operasional perusahaan.
                </p>
            </div>

            {{-- Form --}}
            <form action="{{ route('admin.login') }}" method="POST" class="space-y-8">
                @csrf
                
                <div class="space-y-6">
                    <div class="group">
                        <label for="email" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">Operator Identifier</label>
                        <div class="relative">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-600 transition-colors">
                                <i data-feather="mail" class="w-5 h-5"></i>
                            </div>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                class="w-full bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent rounded-[2rem] pl-16 pr-8 py-5 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500 transition-all text-gray-900 dark:text-white shadow-inner"
                                placeholder="name@system.local">
                        </div>
                        @error('email') <p class="mt-4 text-[10px] font-bold text-red-500 uppercase tracking-widest pl-6">{{ $message }}</p> @enderror
                    </div>

                    <div class="group">
                        <label for="password" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">Clearance Key</label>
                        <div class="relative">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-600 transition-colors">
                                <i data-feather="lock" class="w-5 h-5"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                class="w-full bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent rounded-[2rem] pl-16 pr-8 py-5 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500 transition-all text-gray-900 dark:text-white shadow-inner"
                                placeholder="ACCESS PASSPHRASE">
                        </div>
                    </div>

                    <div class="flex items-center justify-between px-2">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <input type="checkbox" name="remember" class="w-6 h-6 rounded-lg border-gray-200 dark:border-gray-700 text-primary-600 focus:ring-primary-500 focus:ring-offset-0 transition-all cursor-pointer">
                            <span class="text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-widest group-hover:text-gray-600 transition-colors">Persist Session</span>
                        </label>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-20 bg-gray-900 dark:bg-white dark:text-gray-900 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[2rem] shadow-2xl shadow-gray-900/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-4 group">
                        <span>Initiate Node Access</span>
                        <i data-feather="arrow-right" class="w-5 h-5 group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </div>
            </form>

            {{-- Footer Links --}}
            <div class="mt-12 text-center">
                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">
                    Authorized Personnel Only. 
                    <a href="{{ url('/') }}" class="text-primary-600 hover:underline ml-2">Back to Home</a>
                </p>
            </div>
        </div>
    </div>

    {{-- Right Side: Visual Panel --}}
    <div class="hidden lg:flex w-[40%] bg-gray-900 relative overflow-hidden">
        {{-- Background Image --}}
        <img src="{{ asset('assets/img/auth-bg.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-luminosity">
        
        {{-- Gradient Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-tr from-gray-900 via-gray-900/60 to-primary-600/20"></div>

        {{-- Content Overlay --}}
        <div class="relative z-10 w-full flex flex-col justify-between p-20">
            <div class="w-20 h-20 bg-white/10 backdrop-blur-xl rounded-[2rem] flex items-center justify-center shadow-2xl border border-white/10">
                <i data-feather="terminal" class="w-10 h-10 text-white"></i>
            </div>

            <div class="max-w-md">
                <h2 class="text-5xl font-black text-white uppercase tracking-tighter leading-none mb-8">
                    Smart <br> 
                    <span class="text-primary-500">Procurement</span> <br>
                    Ecosystem
                </h2>
                <div class="h-1.5 w-24 bg-primary-500 rounded-full mb-8"></div>
                <p class="text-gray-400 font-bold leading-relaxed">
                    Hubungkan bisnis Anda dengan ribuan vendor terverifikasi dalam satu ekosistem digital yang aman dan terintegrasi.
                </p>
            </div>

            <div class="flex gap-12">
                <div>
                    <p class="text-4xl font-black text-white leading-none">99%</p>
                    <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] mt-3">Accuracy rate</p>
                </div>
                <div>
                    <p class="text-4xl font-black text-white leading-none">24/7</p>
                    <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] mt-3">Node uptime</p>
                </div>
            </div>
        </div>

        {{-- Decorative Elements --}}
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[800px] h-[800px] bg-primary-600/10 rounded-full blur-[120px] pointer-events-none"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof feather !== 'undefined') {
            feather.replace();
        }
    });
</script>
@endpush
