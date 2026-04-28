@extends('layouts.guest', [
    'title' => 'Register Account'
])

@section('content')
<div class="min-h-screen flex">
    {{-- Left Side: Form Panel --}}
    <div class="w-full lg:w-[60%] flex items-center justify-center p-8 md:p-12 lg:p-20 bg-white dark:bg-gray-900 overflow-y-auto">
        <div class="w-full max-w-xl">
            {{-- Brand / Node Identifier (Mobile Only) --}}
            <div class="lg:hidden text-center mb-10">
                <div class="w-20 h-20 bg-gray-900 dark:bg-white/10 rounded-[2rem] flex items-center justify-center mx-auto mb-6 shadow-2xl">
                    <i data-feather="user-plus" class="w-10 h-10 text-primary-500"></i>
                </div>
            </div>

            <div class="mb-12">
                <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-4">
                    Join <br> <span class="text-primary-600">Huntr.id</span>
                </h1>
                <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] leading-relaxed">
                    Start your digital procurement journey and connect your business with our global network.
                </p>
            </div>

            {{-- Form --}}
            <form action="{{ route('register') }}" method="POST" class="space-y-8">
                @csrf
                
                <div class="space-y-6">
                    <div class="group">
                        <label for="name" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">Full Name</label>
                        <div class="relative">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-600 transition-colors">
                                <i data-feather="user" class="w-5 h-5"></i>
                            </div>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                class="w-full bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent rounded-[2rem] pl-16 pr-8 py-5 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500 transition-all text-gray-900 dark:text-white shadow-inner placeholder:text-gray-400 dark:placeholder:text-gray-500"
                                placeholder="Your full name as per identity">
                        </div>
                        @error('name') <p class="mt-4 text-[10px] font-bold text-red-500 uppercase tracking-widest pl-6">{{ $message }}</p> @enderror
                    </div>

                    <div class="group">
                        <label for="email" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">Business Email</label>
                        <div class="relative">
                            <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-primary-600 transition-colors">
                                <i data-feather="mail" class="w-5 h-5"></i>
                            </div>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                class="w-full bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent rounded-[2rem] pl-16 pr-8 py-5 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500 transition-all text-gray-900 dark:text-white shadow-inner placeholder:text-gray-400 dark:placeholder:text-gray-500"
                                placeholder="name@company.com">
                        </div>
                        @error('email') <p class="mt-4 text-[10px] font-bold text-red-500 uppercase tracking-widest pl-6">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="group">
                            <label for="password" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">Password</label>
                            <input type="password" name="password" id="password" required
                                class="w-full bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent rounded-[2rem] px-8 py-5 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500 transition-all text-gray-900 dark:text-white shadow-inner placeholder:text-gray-400 dark:placeholder:text-gray-500"
                                placeholder="Min. 8 characters">
                            @error('password') <p class="mt-4 text-[10px] font-bold text-red-500 uppercase tracking-widest pl-6">{{ $message }}</p> @enderror
                        </div>
                        <div class="group">
                            <label for="password_confirmation" class="block text-[10px] font-black text-gray-400 dark:text-gray-500 uppercase tracking-[0.2em] mb-4">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                class="w-full bg-gray-50 dark:bg-gray-800/50 border-2 border-transparent rounded-[2rem] px-8 py-5 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:bg-white dark:focus:bg-gray-800 focus:border-primary-500 transition-all text-gray-900 dark:text-white shadow-inner"
                                placeholder="Repeat password">
                        </div>
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full h-20 bg-gray-900 dark:bg-white dark:text-gray-900 text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-[2rem] shadow-2xl shadow-gray-900/20 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-4 group">
                        <span>Create Account Now</span>
                        <i data-feather="check-circle" class="w-5 h-5 group-hover:scale-110 transition-transform"></i>
                    </button>
                    <p class="text-center mt-8 text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest leading-relaxed px-6">
                        By registering, you agree to our <a href="#" class="text-primary-600 hover:underline">Terms & Conditions</a> and <a href="#" class="text-primary-600 hover:underline">Privacy Policy</a> of Huntr.id.
                    </p>
                </div>
            </form>

            {{-- Footer Links --}}
            <div class="mt-12 pt-10 border-t border-gray-50 dark:border-gray-800/50 text-center">
                <p class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest mb-4">Already have an account?</p>
                <a href="{{ route('login') }}" class="h-14 px-12 inline-flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-[10px] font-black text-primary-600 dark:text-primary-400 uppercase tracking-widest rounded-2xl hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-all shadow-sm">Sign In Now</a>
            </div>
        </div>
    </div>

    {{-- Right Side: Visual Panel --}}
    <div class="hidden lg:flex w-[40%] bg-gray-900 relative overflow-hidden">
        {{-- Background Image --}}
        <img src="{{ asset('assets/img/auth-bg.png') }}" class="absolute inset-0 w-full h-full object-cover opacity-60 mix-blend-luminosity grayscale">
        
        {{-- Gradient Overlay --}}
        <div class="absolute inset-0 bg-gradient-to-tr from-gray-900 via-gray-900/60 to-primary-600/20"></div>

        {{-- Content Overlay --}}
        <div class="relative z-10 w-full flex flex-col justify-between p-20">
            <div class="w-20 h-20 bg-white/10 backdrop-blur-xl rounded-[2rem] flex items-center justify-center shadow-2xl border border-white/10">
                <i data-feather="user-plus" class="w-10 h-10 text-white"></i>
            </div>

            <div class="max-w-md">
                <p class="text-primary-500 text-xs font-black uppercase tracking-[0.3em] mb-4">Register & Scale</p>
                <h2 class="text-5xl font-black text-white uppercase tracking-tighter leading-none mb-8">
                    Elevate <br> 
                    <span class="text-white/60">Your Business</span> <br>
                    Logic
                </h2>
                <div class="h-1.5 w-24 bg-primary-500 rounded-full mb-8"></div>
                <p class="text-gray-400 font-bold leading-relaxed">
                    Access analytical dashboards, automated inventory management, and global payment integrations in one click.
                </p>
            </div>

            <div class="flex gap-12">
                <div>
                    <p class="text-4xl font-black text-white leading-none">5k+</p>
                    <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] mt-3">Nodes Active</p>
                </div>
                <div>
                    <p class="text-4xl font-black text-white leading-none">Instant</p>
                    <p class="text-[9px] font-black text-gray-500 uppercase tracking-[0.2em] mt-3">Onboarding</p>
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
