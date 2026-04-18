@extends('layouts.app', [
    'title' => 'Global Account Settings',
    'breadcrumbs' => [
        ['name' => 'Account', 'url' => url('/')],
        ['name' => 'Settings', 'url' => null],
    ]
])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 pb-20">
    
    {{-- SETTINGS HEADER --}}
    <div class="bg-white dark:bg-gray-900 p-8 md:p-12 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary-500/5 rounded-full blur-3xl pointer-events-none transition-opacity group-hover:opacity-100"></div>
        
        <div class="relative z-10 max-w-2xl">
            <div class="flex items-center gap-3 mb-6">
                <span class="px-3 py-1 bg-gray-900 dark:bg-gray-800 text-white dark:text-gray-300 rounded-lg text-[9px] font-black uppercase tracking-widest border border-gray-800 dark:border-gray-700">
                    Configuration Node
                </span>
                <div class="h-px w-8 bg-gray-200 dark:bg-gray-800"></div>
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">v2.4.0 Authorized</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-6">
                System <span class="text-primary-600">Preferences</span>
            </h1>
            <p class="text-sm md:text-base font-bold text-gray-500 dark:text-gray-400 leading-relaxed">
                Adjust your personal environment variables, notification web-hooks, and account security protocols for the Huntr.id platform.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- LEFT COLUMN: SECURITY & ACCOUNT --}}
        <div class="lg:col-span-12 xl:col-span-8 space-y-8">
            
            {{-- TWO-FACTOR AUTHENTICATION SECTION --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 md:p-10 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-10 pb-4 border-b border-gray-50 dark:border-gray-800">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 bg-primary-50 dark:bg-primary-900/20 rounded-xl flex items-center justify-center text-primary-600 dark:text-primary-400">
                            <i data-feather="shield" class="w-5 h-5 {{ auth()->user()->two_factor_confirmed_at ? 'animate-pulse' : '' }}"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Two-Factor Authentication</h3>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1.5">Add an extra layer of security to your node</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    @if(!auth()->user()->two_factor_secret)
                        {{-- Disabled State --}}
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 p-6 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                            <div class="flex-1 max-w-xl">
                                <p class="text-xs font-bold text-gray-600 dark:text-gray-400 leading-relaxed">
                                    Two-factor authentication adds an additional layer of security to your account by ensuring that only you can access your account, even if someone else knows your password.
                                </p>
                            </div>
                            <form method="POST" action="/user/two-factor-authentication">
                                @csrf
                                <button type="submit" class="h-12 px-8 bg-primary-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-95 whitespace-nowrap">
                                    Enable Security Layer
                                </button>
                            </form>
                        </div>
                    @else
                        {{-- Pending Confirmation or Enabled State --}}
                        <div class="space-y-8">
                            @if(!auth()->user()->two_factor_confirmed_at)
                                {{-- Step 2: Confirming 2FA --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
                                    <div class="space-y-6">
                                        <div class="p-4 bg-white dark:bg-white rounded-xl shadow-inner inline-block border-8 border-white">
                                            {!! auth()->user()->twoFactorQrCodeSvg() !!}
                                        </div>
                                        <div class="space-y-2">
                                            <p class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-tight">Finish Activation</p>
                                            <p class="text-[10px] font-bold text-gray-500 dark:text-gray-400 uppercase leading-relaxed">
                                                To finish enabling two factor authentication, scan the following QR code using your phone's authenticator application and enter the 6-digit confirmation code below.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        <form method="POST" action="/user/confirmed-two-factor-authentication" class="space-y-4">
                                            @csrf
                                            <div class="space-y-3">
                                                <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Confirmation Code</label>
                                                <input type="text" name="code" required
                                                    class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white placeholder-gray-400"
                                                    placeholder="000000" autofocus autocomplete="one-time-code">
                                            </div>
                                            <button type="submit" class="w-full h-14 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                                                Confirm Activation
                                            </button>
                                        </form>

                                        <form method="POST" action="/user/two-factor-authentication">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-[10px] font-bold text-gray-400 hover:text-red-500 uppercase tracking-widest transition-colors ml-1">
                                                Cancel Activation
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                {{-- Fully Enabled State --}}
                                <div class="flex items-center gap-4 p-4 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800/30">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center text-white">
                                        <i data-feather="check-circle" class="w-4 h-4"></i>
                                    </div>
                                    <p class="text-xs font-black text-emerald-700 dark:text-emerald-400 uppercase tracking-tight">Active Coverage Enabled</p>
                                </div>

                                {{-- Recovery Codes --}}
                                <div class="bg-gray-50 dark:bg-gray-800/50 rounded-xl p-6 space-y-6">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight">Emergency Recovery Codes</p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest mt-1">Store these securely to recover account access</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <form method="POST" action="/user/two-factor-recovery-codes">
                                                @csrf
                                                <button type="submit" class="h-9 px-4 bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest rounded-lg hover:text-primary-600 transition-all active:scale-95">
                                                    Regenerate
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 p-6 rounded-xl font-mono text-[11px] text-gray-600 dark:text-gray-400 shadow-inner">
                                        @foreach (json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                            <div class="flex items-center gap-3">
                                                <span class="w-1.5 h-1.5 rounded-full bg-primary-400 opacity-30"></span>
                                                <span>{{ $code }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="pt-4 flex justify-end">
                                    <form method="POST" action="/user/two-factor-authentication">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="h-12 px-8 border border-red-100 dark:border-red-900/30 text-red-600 dark:text-red-400 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-red-50 dark:hover:bg-red-900/10 transition-all active:scale-95">
                                            Disable Security Layer
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            {{-- ACCOUNT SECURITY SECTION --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 md:p-10 shadow-sm">
                <div class="flex items-center justify-between mb-10 pb-4 border-b border-gray-50 dark:border-gray-800">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Account Passphrase</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1.5">Manage your system access credentials</p>
                    </div>
                </div>

                <form action="{{ route('settings.security.update') }}" method="POST" class="space-y-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Current Password</label>
                            <input type="password" name="current_password" required
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white placeholder-gray-400"
                                placeholder="••••••••">
                            @error('current_password') <p class="text-[9px] font-bold text-red-500 uppercase tracking-widest mt-2 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="hidden md:block"></div> {{-- Empty space for layout --}}
                        
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">New Passphrase</label>
                            <input type="password" name="password" required
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white placeholder-gray-400"
                                placeholder="Min. 8 characters">
                            @error('password') <p class="text-[9px] font-bold text-red-500 uppercase tracking-widest mt-2 ml-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Confirm New Passphrase</label>
                            <input type="password" name="password_confirmation" required
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white placeholder-gray-400"
                                placeholder="Repeat new passphrase">
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="h-14 px-10 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-xl transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Update Security Protocol
                        </button>
                    </div>
                </form>
            </div>

            {{-- NOTIFICATION SETTINGS SECTION --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 md:p-10 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-10 pb-4 border-b border-gray-50 dark:border-gray-800">
                    <div>
                        <h3 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest">Notification Protocols</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1.5">Configure system event web-hooks and push alerts</p>
                    </div>
                </div>

                <form action="{{ route('settings.notifications.update') }}" method="POST" class="space-y-10">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($availableSettings as $key => $description)
                            <label class="flex items-center justify-between p-5 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-transparent hover:border-primary-200 dark:hover:border-primary-900 transition-all group cursor-pointer shadow-sm active:scale-[0.99]">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-lg bg-white dark:bg-gray-800 flex items-center justify-center text-gray-400 dark:text-gray-600 group-hover:text-primary-600 dark:group-hover:text-primary-500 shadow-inner transition-all">
                                        <i data-feather="bell" class="w-4 h-4"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-tight mb-1 truncate">{{ str_replace('_', ' ', $key) }}</p>
                                        <p class="text-[9px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest leading-none truncate">{{ $description }}</p>
                                    </div>
                                </div>
                                
                                <div class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out bg-gray-200 dark:bg-gray-800"
                                     onclick="toggleSwitch(this, 'settings[{{ $key }}]')">
                                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out translate-x-0"></span>
                                    <input type="hidden" name="settings[{{ $key }}]" value="{{ ($settings[$key] ?? false) ? '1' : '0' }}">
                                </div>
                            </label>
                        @endforeach
                    </div>

                    <div class="pt-6 border-t border-gray-50 dark:border-gray-800 flex justify-end">
                        <button type="submit" class="h-14 px-10 bg-primary-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg shadow-primary-600/20 transition-all hover:bg-primary-700 active:scale-[0.98]">
                            Sync Notification Node
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- RIGHT COLUMN: INFO & POLICIES --}}
        <div class="lg:col-span-12 xl:col-span-4 space-y-8">
            <div class="bg-gray-900 dark:bg-black rounded-2xl p-8 text-white relative overflow-hidden shadow-xl">
                <div class="absolute bottom-0 right-0 -mb-16 -mr-16 w-48 h-48 bg-primary-600/20 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col gap-8">
                    <div class="w-14 h-14 bg-white/10 rounded-xl flex items-center justify-center text-primary-400">
                        <i data-feather="lock" class="w-7 h-7"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-[0.2em] mb-4">Security Policy Registry</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase leading-loose tracking-widest">
                            Authorized credentials rotation is mandatory for multi-entity access. Your synchronization pulse is protected by end-to-end platform encryption.
                        </p>
                    </div>
                    <div class="pt-6 border-t border-white/5">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-[9px] font-bold text-emerald-500 uppercase tracking-widest">Protocol Integrity Level: A+</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-6">System Help</h3>
                <div class="space-y-4">
                    <a href="#" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-colors group">
                        <span class="text-[9px] font-bold text-gray-500 dark:text-gray-400 group-hover:text-primary-600 uppercase tracking-widest">Documentation</span>
                        <i data-feather="external-link" class="w-3.5 h-3.5 text-gray-300 group-hover:text-primary-600"></i>
                    </a>
                    <a href="#" class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-primary-50 dark:hover:bg-primary-900/10 transition-colors group">
                        <span class="text-[9px] font-bold text-gray-500 dark:text-gray-400 group-hover:text-primary-600 uppercase tracking-widest">Report Anomaly</span>
                        <i data-feather="alert-triangle" class="w-3.5 h-3.5 text-gray-300 group-hover:text-primary-600"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSwitch(el, inputName) {
        const input = document.querySelector(`input[name="${inputName}"]`);
        const dot = el.querySelector('span');
        const isEnabled = input.value === '1';
        
        if (isEnabled) {
            input.value = '0';
            dot.classList.remove('translate-x-5');
            dot.classList.add('translate-x-0');
            el.classList.remove('bg-primary-600');
            el.classList.add('bg-gray-200', 'dark:bg-gray-800');
        } else {
            input.value = '1';
            dot.classList.remove('translate-x-0');
            dot.classList.add('translate-x-5');
            el.classList.remove('bg-gray-200', 'dark:bg-gray-800');
            el.classList.add('bg-primary-600');
        }
    }

    // Initialize switches on load
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('input[type="hidden"][name^="settings"]').forEach(input => {
            const el = input.parentElement;
            const dot = el.querySelector('span');
            if (input.value === '1') {
                dot.classList.remove('translate-x-0');
                dot.classList.add('translate-x-5');
                el.classList.remove('bg-gray-200', 'dark:bg-gray-800');
                el.classList.add('bg-primary-600');
            } else {
                dot.classList.add('translate-x-0');
                el.classList.add('bg-gray-200', 'dark:bg-gray-800');
            }
        });
    });
</script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
