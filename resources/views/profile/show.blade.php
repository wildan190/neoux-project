@extends('layouts.app', [
    'title' => 'Profile',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Profile', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 dark:text-white tracking-tight">Profile Details</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Manage your personal information and public profile.</p>
        </div>
        <a href="{{ route('settings.index') }}" class="inline-flex items-center px-5 py-2.5 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-bold rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm group">
            <i data-feather="settings" class="w-4 h-4 mr-2 text-primary-500 group-hover:rotate-45 transition-transform"></i>
            Account Settings
        </a>
    </div>

    @if(session('status'))
        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-r-xl shadow-sm animate-fadeIn">
            <div class="flex items-center">
                <i data-feather="check-circle" class="w-5 h-5 mr-3"></i>
                <span class="font-medium">
                    @if(session('status') === 'profile-details-updated') Profile details updated successfully!
                    @elseif(session('status') === 'profile-photo-updated') Profile photo updated successfully!
                    @elseif(session('status') === 'profile-photo-deleted') Profile photo deleted successfully!
                    @else {{ session('status') }} @endif
                </span>
            </div>
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 shadow-xl rounded-3xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="p-8">
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-8 flex items-center">
                <i data-feather="user" class="w-5 h-5 mr-3 text-primary-500"></i>
                Personal Information
            </h3>
            
            {{-- Profile Photo Upload --}}
            <div class="mb-10 pb-10 border-b border-gray-100 dark:border-gray-700">
                <div class="flex flex-col md:flex-row items-center gap-8">
                    <div class="relative group">
                        <div class="w-32 h-32 rounded-3xl overflow-hidden bg-gray-100 dark:bg-gray-700 flex items-center justify-center border-4 border-white dark:border-gray-800 shadow-lg group-hover:shadow-primary-500/10 transition-all">
                            @if($user->userDetail?->profile_photo)
                                <img src="{{ $user->userDetail->profile_photo_url }}" alt="Profile Photo" class="w-full h-full object-cover" id="profile-photo-preview">
                            @else
                                <div class="w-full h-full bg-primary-50 dark:bg-primary-900/20 flex items-center justify-center" id="profile-photo-placeholder">
                                    <span class="text-4xl font-bold text-primary-600 dark:text-primary-400">{{ substr($user->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        <button type="button" onclick="document.getElementById('profile_photo').click()" class="absolute -bottom-2 -right-2 w-10 h-10 bg-primary-600 text-white rounded-xl shadow-lg flex items-center justify-center hover:bg-primary-700 hover:scale-110 transition-all">
                            <i data-feather="camera" class="w-5 h-5"></i>
                        </button>
                    </div>
                    
                    <div class="flex-1 text-center md:text-left">
                        <h4 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h4>
                        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">{{ $user->email }}</p>
                        
                        <div class="mt-4 flex flex-wrap justify-center md:justify-start gap-3">
                            <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" id="photo-upload-form" class="hidden">
                                @csrf
                                <input type="file" name="profile_photo" id="profile_photo" accept="image/*" onchange="previewAndUploadPhoto(event)">
                            </form>
                            
                            <button type="button" onclick="document.getElementById('profile_photo').click()" class="text-sm font-bold text-primary-600 hover:underline">
                                Change Photo
                            </button>
                            
                            @if($user->userDetail?->profile_photo)
                                <span class="text-gray-300">|</span>
                                <form action="{{ route('profile.photo.delete') }}" method="POST" class="inline" onsubmit="return confirm('Delete profile photo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-bold text-red-600 hover:underline">
                                        Remove Photo
                                    </button>
                                </form>
                            @endif
                        </div>
                        @error('profile_photo')
                            <p class="mt-2 text-sm text-red-600 dark:text-red-400 font-medium">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- User Details Form --}}
            <form action="{{ route('profile.details.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- ID Number (KTP) --}}
                    <div>
                        <label for="id_number" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">ID Number (KTP)</label>
                        <input type="text" name="id_number" id="id_number" value="{{ old('id_number', $user->userDetail?->id_number) }}" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white font-medium" placeholder="E.g. 3201234567890001">
                        @error('id_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tax ID --}}
                    <div>
                        <label for="tax_id" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Tax ID (NPWP)</label>
                        <input type="text" name="tax_id" id="tax_id" value="{{ old('tax_id', $user->userDetail?->tax_id) }}" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white font-medium" placeholder="E.g. 01.234.567.8.901.000">
                        @error('tax_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->userDetail?->phone) }}" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white font-medium" placeholder="E.g. +62 812 3456 7890">
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gender --}}
                    <div>
                        <label for="gender" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Gender</label>
                        <select name="gender" id="gender" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white font-medium">
                            <option value="">Select Gender</option>  
                            <option value="male" {{ old('gender', $user->userDetail?->gender) === 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', $user->userDetail?->gender) === 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', $user->userDetail?->gender) === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('gender')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date of Birth --}}
                    <div>
                        <label for="date_of_birth" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Date of Birth</label>
                        <input type="date" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $user->userDetail?->date_of_birth?->format('Y-m-d')) }}" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white font-medium">
                        @error('date_of_birth')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Registered Date (Read-only) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Member Since</label>
                        <div class="px-4 py-3 bg-gray-100 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 text-gray-500 dark:text-gray-400 font-medium">
                            {{ ($user->userDetail?->registered_date ?? $user->created_at)->format('F d, Y') }}
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                    {{-- Address --}}
                    <div>
                        <label for="address" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Full Address</label>
                        <textarea name="address" id="address" rows="4" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white font-medium" placeholder="E.g. Jln. Kebon Jeruk No. 123, Jakarta Barat">{{ old('address', $user->userDetail?->address) }}</textarea>
                        @error('address')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Bio --}}
                    <div>
                        <label for="bio" class="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Brief Bio</label>
                        <textarea name="bio" id="bio" rows="4" maxlength="500" class="block w-full rounded-xl border border-gray-200 dark:border-gray-700 px-4 py-3 bg-gray-50 dark:bg-gray-900/50 focus:bg-white dark:focus:bg-gray-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all dark:text-white font-medium" placeholder="Tell us a little bit about yourself...">{{ old('bio', $user->userDetail?->bio) }}</textarea>
                        <div class="mt-2 flex justify-between">
                            <p class="text-[11px] text-gray-400">Max 500 characters</p>
                            @error('bio')
                                <p class="text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-10 pt-8 border-t border-gray-100 dark:border-gray-700 flex justify-end">
                    <button type="submit" class="inline-flex items-center px-10 py-3.5 bg-primary-600 text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-500/30 hover:shadow-primary-500/50 transform hover:-translate-y-0.5">
                        <i data-feather="save" class="w-5 h-5 mr-3"></i>
                        Save Profile Details
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Profile photo preview and auto-upload
    function previewAndUploadPhoto(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('profile-photo-preview');
                const placeholder = document.getElementById('profile-photo-placeholder');
                
                if (preview) {
                    preview.src = e.target.result;
                } else if (placeholder) {
                    placeholder.parentElement.innerHTML = `<img src="${e.target.result}" alt="Profile Photo" class="w-full h-full object-cover" id="profile-photo-preview">`;
                }
            }
            reader.readAsDataURL(file);
            document.getElementById('photo-upload-form').submit();
        }
    }
</script>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 0.4s ease-out forwards;
    }
</style>
@endsection
