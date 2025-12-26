@extends('layouts.app', [
    'title' => 'Settings',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Settings', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-screen-2xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Settings</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage your account, security, and notification preferences.</p>
        </div>
    </div>

    @if(session('success') || session('status'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm animate-fadeIn">
            <div class="flex items-center">
                <i data-feather="check-circle" class="w-5 h-5 mr-3"></i>
                <span class="font-medium">
                    {{ session('success') ?? (session('status') === 'password-updated' ? 'Password updated successfully!' : (session('status') === 'profile-information-updated' ? 'Account information updated successfully!' : session('status'))) }}
                </span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
        {{-- Sidebar Navigation --}}
        <div class="md:col-span-1">
            <nav class="space-y-2 sticky top-8">
                <button onclick="switchTab('account')" data-tab="account" class="tab-button w-full flex items-center px-5 py-3.5 text-sm font-bold bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-2xl shadow-sm hover:shadow-md transition-all border-l-4 border-transparent">
                    <i data-feather="user" class="w-4 h-4 mr-3"></i>
                    Account Information
                </button>
                <button onclick="switchTab('security')" data-tab="security" class="tab-button w-full flex items-center px-5 py-3.5 text-sm font-bold bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-2xl shadow-sm hover:shadow-md transition-all border-l-4 border-transparent">
                    <i data-feather="lock" class="w-4 h-4 mr-3"></i>
                    Password & Security
                </button>
                <button onclick="switchTab('notifications')" data-tab="notifications" class="tab-button w-full flex items-center px-5 py-3.5 text-sm font-bold bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 rounded-2xl shadow-sm hover:shadow-md transition-all border-l-4 border-transparent">
                    <i data-feather="bell" class="w-4 h-4 mr-3"></i>
                    Notifications
                </button>
                
                <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('profile.show') }}" class="flex items-center px-5 py-3 text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-white dark:hover:bg-gray-800 rounded-2xl transition-all group">
                        <i data-feather="edit-3" class="w-4 h-4 mr-3 group-hover:rotate-12 transition-transform"></i>
                        Edit Profile Details
                        <i data-feather="arrow-right" class="w-3 h-3 ml-auto opacity-0 group-hover:opacity-100 transition-all"></i>
                    </a>
                </div>
            </nav>
        </div>

        {{-- Main Settings Content --}}
        <div class="md:col-span-3 space-y-6">
            
            {{-- Account Section --}}
            <div id="account-content" class="tab-content bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <i data-feather="user" class="w-5 h-5 mr-3 text-primary-500"></i>
                        Account Information
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update your basic account details and email address.</p>
                </div>

                <div class="p-8">
                    <form action="{{ route('user-profile-information.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                                <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white" required>
                                @error('name', 'updateProfileInformation')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="email" class="block text-sm font-bold text-gray-700 dark:text-gray-300">Email Address</label>
                                    @if(auth()->user()->hasVerifiedEmail())
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                            <i data-feather="check" class="w-3 h-3 mr-1"></i> VERIFIED
                                        </span>
                                    @endif
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white" required>
                                @error('email', 'updateProfileInformation')
                                    <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            @if(! auth()->user()->hasVerifiedEmail())
                                <button type="button" onclick="document.getElementById('send-verification').submit()" class="text-sm font-bold text-primary-600 hover:text-primary-700 flex items-center transition-colors">
                                    <i data-feather="mail" class="w-4 h-4 mr-2"></i>
                                    Resend verification email
                                </button>
                            @else
                                <div></div>
                            @endif

                            <button type="submit" class="inline-flex items-center px-8 py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transform hover:-translate-y-0.5">
                                <i data-feather="save" class="w-5 h-5 mr-2"></i>
                                Save Changes
                            </button>
                        </div>
                    </form>
                    
                    <form id="send-verification" method="POST" action="{{ route('verification.send') }}" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>

            {{-- Security Section --}}
            <div id="security-content" class="tab-content bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <i data-feather="lock" class="w-5 h-5 mr-3 text-primary-500"></i>
                        Security Settings
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Enhance your account security with password updates and 2FA.</p>
                </div>

                <div class="p-8">
                    {{-- Password Update --}}
                    <div class="mb-10">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-6 flex items-center uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500 mr-2"></span>
                            Change Password
                        </h4>
                        <form action="{{ route('user-password.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="current_password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                                    <input type="password" name="current_password" id="current_password" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white" required>
                                    @error('current_password', 'updatePassword')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 text-sm">New Password</label>
                                    <input type="password" name="password" id="password" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white" required>
                                    @error('password', 'updatePassword')
                                        <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2 text-sm">Confirm New Password</label>
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white" required>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="submit" class="inline-flex items-center px-8 py-3 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transform hover:-translate-y-0.5">
                                    Update Password
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Two Factor Authentication --}}
                    <div class="pt-10 border-t border-gray-100 dark:border-gray-700">
                        <h4 class="text-sm font-bold text-gray-900 dark:text-white mb-6 flex items-center uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-primary-500 mr-2"></span>
                            Two-Factor Authentication
                        </h4>
                        
                        @if(! auth()->user()->two_factor_secret)
                            <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl p-6 border border-gray-100 dark:border-gray-700">
                                <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed mb-6">
                                    Add an extra layer of security to your account. When enabled, you'll be prompted for a secure, random token during authentication.
                                </p>
                                <form action="{{ route('two-factor.enable') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-900 dark:text-white font-bold rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm">
                                        <i data-feather="shield" class="w-4 h-4 mr-2 text-primary-500"></i>
                                        Enable Two-Factor Authentication
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="space-y-6">
                                <div class="flex items-center gap-3 text-green-600 dark:text-green-400 font-bold text-sm bg-green-50 dark:bg-green-900/20 p-4 rounded-xl border border-green-100 dark:border-green-800/30">
                                    <i data-feather="check-circle" class="w-5 h-5"></i>
                                    Two-Factor Authentication is currently enabled.
                                </div>

                                @if(session('status') == 'two-factor-authentication-enabled' || session('status') == 'recovery-codes-generated')
                                    @if(session('status') == 'two-factor-authentication-enabled')
                                        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-primary-100 dark:border-primary-900/30 p-6 shadow-sm">
                                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-6">
                                                To finish enabling 2FA, scan the following QR code using your phone's authenticator application (Google Authenticator, Authy, etc).
                                            </p>
                                            <div class="flex flex-col md:flex-row gap-8 items-start">
                                                <div class="bg-white p-3 rounded-2xl shadow-inner border border-gray-100">
                                                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                                                </div>
                                                <div class="flex-1">
                                                    <form action="{{ route('two-factor.confirm') }}" method="POST">
                                                        @csrf
                                                        <label class="block text-sm font-bold mb-2">Enter Verification Code</label>
                                                        <input type="text" name="code" class="block w-full max-w-xs rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900 focus:bg-white focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all" placeholder="000000" required>
                                                        <button type="submit" class="mt-4 inline-flex items-center px-6 py-2.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all">
                                                            Confirm Secret
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <div class="bg-gray-50 dark:bg-gray-900 rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
                                        <p class="text-gray-900 dark:text-white font-bold text-sm mb-4">Recovery Codes</p>
                                        <p class="text-gray-600 dark:text-gray-400 text-xs mb-4">
                                            Store these codes in a secure password manager. They can be used to recover access to your account if your device is lost.
                                        </p>
                                        <div class="grid grid-cols-2 gap-4 font-mono text-xs p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700">
                                            @foreach(json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                                                <div class="text-gray-500 dark:text-gray-400">{{ $code }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="flex flex-wrap gap-4 pt-4">
                                    @if(session('status') != 'two-factor-authentication-enabled')
                                        <form action="{{ route('two-factor.regenerate-recovery-codes') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-bold rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all text-sm">
                                                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                                                Regenerate Recovery Codes
                                            </button>
                                        </form>
                                    @endif

                                    <form action="{{ route('two-factor.disable') }}" method="POST" onsubmit="return confirm('Disable Two-Factor Authentication?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-5 py-2.5 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 font-bold rounded-xl border border-red-100 dark:border-red-900/30 hover:bg-red-100 dark:hover:bg-red-900/30 transition-all text-sm">
                                            <i data-feather="trash-2" class="w-4 h-4 mr-2"></i>
                                            Disable 2FA
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Notifications Section --}}
            <div id="notifications-content" class="tab-content bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden hidden">
                <div class="px-8 py-6 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white flex items-center">
                        <i data-feather="bell" class="w-5 h-5 mr-3 text-primary-500"></i>
                        Notification Preferences
                    </h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Choose which activities you'd like to stay informed about.</p>
                </div>

                <form action="{{ route('settings.notifications.update') }}" method="POST" class="p-8">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($availableSettings as $key => $label)
                            <div class="group flex items-center justify-between p-5 rounded-2xl hover:bg-gray-50 dark:hover:bg-gray-900/50 transition-all border border-gray-100 dark:border-gray-700 shadow-sm hover:shadow-md">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ $label }}</span>
                                    <span class="text-[11px] text-gray-400 dark:text-gray-500 mt-1">Alerts for this category</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="settings[{{ $key }}]" value="1" class="sr-only peer" {{ ($settings[$key] ?? true) ? 'checked' : '' }}>
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all dark:border-gray-600 peer-checked:bg-primary-600"></div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-10 flex justify-end">
                        <button type="submit" class="inline-flex items-center px-10 py-3.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transform hover:-translate-y-0.5">
                            <i data-feather="save" class="w-5 h-5 mr-2"></i>
                            Save Notification Settings
                        </button>
                    </div>
                </form>
            </div>

            {{-- Info Card --}}
            <div class="bg-gradient-to-br from-indigo-500 to-primary-600 rounded-3xl p-8 text-white shadow-xl shadow-primary-500/20 relative overflow-hidden group">
                <div class="relative z-10 flex flex-col md:flex-row items-center gap-6">
                    <div class="w-20 h-20 rounded-2xl bg-white/20 flex items-center justify-center shrink-0 backdrop-blur-md">
                        <i data-feather="shield" class="w-10 h-10 text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold mb-2">Account Security is our Priority</h3>
                        <p class="text-indigo-100/90 text-sm leading-relaxed max-w-xl">We use industry-standard encryption and security practices to protect your data. Keep your password strong and enable Two-Factor Authentication for maximum security.</p>
                    </div>
                </div>
                <i data-feather="zap" class="absolute -bottom-12 -right-12 w-64 h-64 text-white/5 transform rotate-12 group-hover:scale-110 transition-transform duration-700"></i>
            </div>
        </div>
    </div>
</div>

<script>
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active state from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('bg-primary-50', 'dark:bg-primary-900/20', 'text-primary-600', 'dark:text-primary-400', 'border-primary-600', 'dark:border-primary-400', 'shadow-md');
            button.classList.add('bg-white', 'dark:bg-gray-800', 'text-gray-600', 'dark:text-gray-400', 'border-transparent');
        });
        
        // Show selected tab content
        const selectedContent = document.getElementById(tabName + '-content');
        if (selectedContent) {
            selectedContent.classList.remove('hidden');
            
            // Add active state to selected tab button
            const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
            if (activeButton) {
                activeButton.classList.remove('bg-white', 'dark:bg-gray-800', 'text-gray-600', 'dark:text-gray-400', 'border-transparent');
                activeButton.classList.add('bg-primary-50', 'dark:bg-primary-900/20', 'text-primary-600', 'dark:text-primary-400', 'border-primary-600', 'dark:border-primary-400', 'shadow-md');
            }

            // Update URL hash
            history.pushState(null, null, '#' + tabName);
        }
    }
    
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash.replace('#', '');
        
        // Check for common error bags to auto-switch
        const showSecurity = @json($errors->hasBag('updatePassword') || session('status') == 'password-updated' || session('status') == 'two-factor-authentication-enabled' || session('status') == 'recovery-codes-generated');
        const showAccount = @json($errors->hasBag('updateProfileInformation') || session('status') == 'profile-information-updated' || session('status') == 'verification-link-sent');

        if (showSecurity) {
            switchTab('security');
        } else if (showAccount) {
            switchTab('account');
        } else if (hash && ['account', 'security', 'notifications'].includes(hash)) {
            switchTab(hash);
        } else {
            switchTab('account');
        }
    });
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out forwards;
    }
    .tab-content {
        animation: fadeIn 0.3s ease-out forwards;
    }
</style>
@endsection
