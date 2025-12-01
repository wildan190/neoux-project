@extends('layouts.app', [
    'title' => 'Profile',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Profile', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- Update Profile Information --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Profile Information</h3>
        <form action="{{ route('user-profile-information.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    @error('name', 'updateProfileInformation')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        @if(auth()->user()->hasVerifiedEmail())
                            <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full dark:bg-green-900 dark:text-green-300">
                                Verified
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full dark:bg-red-900 dark:text-red-300">
                                Unverified
                            </span>
                        @endif
                    </div>
                    <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    @error('email', 'updateProfileInformation')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-between items-center">
                @if(! auth()->user()->hasVerifiedEmail())
                    <div class="flex items-center gap-4">
                        <button form="send-verification" class="text-sm text-indigo-600 hover:text-indigo-900 underline">
                            Click here to re-send the verification email.
                        </button>
                        @if (session('status') === 'verification-link-sent')
                            <p class="text-sm font-medium text-green-600 dark:text-green-400">
                                A new verification link has been sent to your email address.
                            </p>
                        @endif
                    </div>
                @else
                    <div></div>
                @endif

                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
        
        <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
            @csrf
        </form>
    </div>

    {{-- Update Password --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Update Password</h3>
        <form action="{{ route('user-password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                    <input type="password" name="current_password" id="current_password" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    @error('current_password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    @error('password', 'updatePassword')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    Update Password
                </button>
            </div>
        </form>
    </div>

    {{-- Two Factor Authentication --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Two Factor Authentication</h3>
        
        @if(! auth()->user()->two_factor_secret)
            {{-- Enable 2FA --}}
            <p class="text-gray-600 dark:text-gray-300 mb-4">
                Add additional security to your account using two factor authentication.
            </p>
            <form action="{{ route('two-factor.enable') }}" method="POST">
                @csrf
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    Enable Two-Factor Authentication
                </button>
            </form>
        @else
            {{-- 2FA Enabled --}}
            <p class="text-gray-600 dark:text-gray-300 mb-4">
                You have enabled two factor authentication.
            </p>

            @if(session('status') == 'two-factor-authentication-enabled' || session('status') == 'recovery-codes-generated')
                @if(session('status') == 'two-factor-authentication-enabled')
                    <div class="mb-4">
                        <p class="text-gray-600 dark:text-gray-300 mb-2">
                            Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application.
                        </p>
                        <div class="bg-white p-4 inline-block rounded-lg">
                            {!! auth()->user()->twoFactorQrCodeSvg() !!}
                        </div>
                    </div>

                    <div class="mb-4">
                        <p class="text-gray-600 dark:text-gray-300 mb-2">
                            Please confirm access to your account by entering the authentication code provided by your authenticator application.
                        </p>
                        <form action="{{ route('two-factor.confirm') }}" method="POST" class="flex gap-2">
                            @csrf
                            <input type="text" name="code" class="rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Code" required>
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                                Confirm
                            </button>
                        </form>
                    </div>
                @endif
                
                <div class="mb-4">
                     <p class="text-gray-600 dark:text-gray-300 mb-2">
                        Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.
                    </p>
                    <div class="bg-gray-100 dark:bg-gray-900 p-4 rounded-lg">
                        @foreach(json_decode(decrypt(auth()->user()->two_factor_recovery_codes), true) as $code)
                            <div class="text-gray-600 dark:text-gray-300 font-mono">{{ $code }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex gap-4">
                @if(session('status') != 'two-factor-authentication-enabled')
                    <form action="{{ route('two-factor.regenerate-recovery-codes') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition-colors">
                            Regenerate Recovery Codes
                        </button>
                    </form>
                @endif

                <form action="{{ route('two-factor.disable') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors">
                        Disable Two-Factor Authentication
                    </button>
                </form>
            </div>
        @endif
    </div>

</div>
@endsection
