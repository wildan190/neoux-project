@extends('layouts.app', [
    'title' => 'Profile',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Profile', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    
    {{-- Tab Navigation --}}
    <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl overflow-hidden">
        <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="flex -mb-px" role="tablist">
                <button 
                    type="button"
                    class="tab-button flex-1 py-4 px-6 text-center font-medium text-sm border-b-2 transition-colors"
                    data-tab="profile-settings"
                    onclick="switchTab('profile-settings')"
                >
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile Settings
                </button>
                <button 
                    type="button"
                    class="tab-button flex-1 py-4 px-6 text-center font-medium text-sm border-b-2 transition-colors"
                    data-tab="account-settings"
                    onclick="switchTab('account-settings')"
                >
                    <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Account Settings
                </button>
            </nav>
        </div>

        {{-- Tab Content: Profile Settings --}}
        <div id="profile-settings-content" class="tab-content p-6">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Personal Information</h3>
            
            {{-- Success Messages --}}
            @if (session('status') === 'profile-details-updated')
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg dark:bg-green-900 dark:text-green-300">
                    Profile details updated successfully!
                </div>
            @endif
            @if (session('status') === 'profile-photo-updated')
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg dark:bg-green-900 dark:text-green-300">
                    Profile photo updated successfully!
                </div>
            @endif
            @if (session('status') === 'profile-photo-deleted')
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg dark:bg-green-900 dark:text-green-300">
                    Profile photo deleted successfully!
                </div>
            @endif

            {{-- Profile Photo Upload --}}
            <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Profile Photo</label>
                <div class="flex items-center gap-4">
                    <div class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                        @if($user->userDetail?->profile_photo)
                            <img src="{{ $user->userDetail->profile_photo_url }}" alt="Profile Photo" class="w-full h-full object-cover" id="profile-photo-preview">
                        @else
                            <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20" id="profile-photo-placeholder">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        @endif
                    </div>
                    <div class="flex-1">
                        <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photo-upload-form">
                            @csrf
                            <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="hidden" onchange="previewAndUploadPhoto(event)">
                            <div class="flex gap-2">
                                <button type="button" onclick="document.getElementById('profile_photo').click()" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors text-sm">
                                    Upload Photo
                                </button>
                                @if($user->userDetail?->profile_photo)
                                    <form action="{{ route('profile.photo.delete') }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete your profile photo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition-colors text-sm">
                                            Delete Photo
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </form>
                        @error('profile_photo')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">JPG, PNG or GIF (MAX. 2MB)</p>
                    </div>
                </div>
            </div>

            {{-- User Details Form --}}
            <form action="{{ route('profile.details.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- ID Number (KTP) --}}
                    <div>
                        <label for="id_number" class="block text-sm font-medium text-gray-700 dark:text-gray-300">ID Number (KTP)</label>
                        <input 
                            type="text" 
                            name="id_number" 
                            id="id_number" 
                            value="{{ old('id_number', $user->userDetail?->id_number) }}" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                        @error('id_number')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tax ID --}}
                    <div>
                        <label for="tax_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tax ID (NPWP)</label>
                        <input 
                            type="text" 
                            name="tax_id" 
                            id="tax_id" 
                            value="{{ old('tax_id', $user->userDetail?->tax_id) }}" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                        @error('tax_id')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</label>
                        <input 
                            type="text" 
                            name="phone" 
                            id="phone" 
                            value="{{ old('phone', $user->userDetail?->phone) }}" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                        @error('phone')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label for="gender" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Gender</label>
                        <select 
                            name="gender" 
                            id="gender" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                            <option value="">Select Gender</option>  
                            <option value="male" {{ old('gender', $user->userDetail?->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->userDetail?->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->userDetail?->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date of Birth --}}
                    <div>
                        <label for="date_of_birth" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Date of Birth</label>
                        <input 
                            type="date" 
                            name="date_of_birth" 
                            id="date_of_birth" 
                            value="{{ old('date_of_birth', $user->userDetail?->date_of_birth?->format('Y-m-d')) }}" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        >
                        @error('date_of_birth')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Registered Date (Read-only) --}}
                    <div>
                        <label for="registered_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Registered Since</label>
                        <input 
                            type="text" 
                            value="{{ ($user->userDetail?->registered_date ?? $user->created_at)->format('F d, Y') }}" 
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm bg-gray-100 dark:bg-gray-900 dark:border-gray-600 dark:text-white cursor-not-allowed"
                            disabled
                        >
                    </div>
                </div>

                {{-- Address --}}
                <div class="mt-6">
                    <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Address</label>
                    <textarea 
                        name="address" 
                        id="address" 
                        rows="3" 
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                    >{{ old('address', $user->userDetail?->address) }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Bio --}}
                <div class="mt-6">
                    <label for="bio" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bio</label>
                    <textarea 
                        name="bio" 
                        id="bio" 
                        rows="4" 
                        maxlength="500"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                        placeholder="Tell us about yourself..."
                    >{{ old('bio', $user->userDetail?->bio) }}</textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maximum 500 characters</p>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-lg hover:bg-primary-700 transition-colors">
                        Save Profile Details
                    </button>
                </div>
            </form>
        </div>

        {{-- Tab Content: Account Settings --}}
        <div id="account-settings-content" class="tab-content p-6 hidden">
            
            {{-- Update Profile Information --}}
            <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Account Information</h3>
                <form action="{{ route('user-profile-information.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <input type="text" name="name" id="name" value="{{ old('name', auth()->user()->name) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
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
                            <input type="email" name="email" id="email" value="{{ old('email', auth()->user()->email) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            @error('email', 'updateProfileInformation')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex justify-between items-center">
                        @if(! auth()->user()->hasVerifiedEmail())
                            <div class="flex items-center gap-4">
                                <button form="send-verification" class="text-sm text-primary-600 hover:text-primary-900 underline">
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

                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
                
                <form id="send-verification" method="POST" action="{{ route('verification.send') }}">
                    @csrf
                </form>
            </div>

            {{-- Update Password --}}
            <div class="mb-8 pb-8 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Update Password</h3>
                <form action="{{ route('user-password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Current Password</label>
                            <input type="password" name="current_password" id="current_password" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            @error('current_password', 'updatePassword')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">New Password</label>
                            <input type="password" name="password" id="password" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            @error('password', 'updatePassword')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- Two Factor Authentication --}}
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Two Factor Authentication</h3>
                
                @if(! auth()->user()->two_factor_secret)
                    {{-- Enable 2FA --}}
                    <p class="text-gray-600 dark:text-gray-300 mb-4">
                        Add additional security to your account using two factor authentication.
                    </p>
                    <form action="{{ route('two-factor.enable') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
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
                                    <input type="text" name="code" class="rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" placeholder="Code" required>
                                    <button type="submit" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
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
    </div>

</div>

<script>
    // Tab switching functionality
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Remove active state from all tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-primary-600', 'text-primary-600', 'dark:border-primary-400', 'dark:text-primary-400');
            button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400');
        });
        
        // Show selected tab content
        document.getElementById(tabName + '-content').classList.remove('hidden');
        
        // Add active state to selected tab button
        const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
        activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400');
        activeButton.classList.add('border-primary-600', 'text-primary-600', 'dark:border-primary-400', 'dark:text-primary-400');
    }
    
    // Initialize: Show Profile Settings tab by default
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('profile-settings');
    });

    // Profile photo preview and auto-upload
    function previewAndUploadPhoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Update or create preview image
                const preview = document.getElementById('profile-photo-preview');
                const placeholder = document.getElementById('profile-photo-placeholder');
                
                if (preview) {
                    preview.src = e.target.result;
                } else if (placeholder) {
                    // Replace placeholder with image
                    placeholder.parentElement.innerHTML = `<img src="${e.target.result}" alt="Profile Photo" class="w-full h-full object-cover" id="profile-photo-preview">`;
                }
            }
            reader.readAsDataURL(file);
            
            // Auto-submit the form
            document.getElementById('photo-upload-form').submit();
        }
    }
</script>

<style>
    .tab-button {
        transition: all 0.3s ease;
    }
    
    .tab-content {
        animation: fadeIn 0.3s ease-in;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endsection
