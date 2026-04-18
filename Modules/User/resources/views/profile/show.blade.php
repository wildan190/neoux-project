@extends('layouts.app', [
    'title' => 'Personal Profile',
    'breadcrumbs' => [
        ['name' => 'Account', 'url' => url('/')],
        ['name' => 'Profile', 'url' => null],
    ]
])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 pb-20">
    
    {{-- PROFILE HEADER --}}
    <div class="flex flex-col md:flex-row items-center md:items-end gap-6 md:gap-8 bg-white dark:bg-gray-900 p-6 md:p-8 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-primary-500/5 rounded-full blur-3xl pointer-events-none transition-opacity group-hover:opacity-100"></div>
        
        <div class="relative">
            <div class="w-24 h-24 md:w-32 md:h-32 rounded-2xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 p-1.5 shadow-xl transition-all duration-500 hover:rotate-2">
                @if($user->userDetail && $user->userDetail->profile_photo)
                    <img src="{{ asset('storage/' . $user->userDetail->profile_photo) }}" class="w-full h-full object-cover rounded-xl shadow-inner">
                @else
                    <div class="w-full h-full bg-primary-600 flex items-center justify-center text-3xl font-black text-white rounded-xl shadow-lg shadow-primary-600/20">
                        {{ strtoupper(substr($user->name ?: $user->email, 0, 1)) }}
                    </div>
                @endif
            </div>
            
            {{-- Update Photo Trigger --}}
            <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="absolute -bottom-2 -right-2">
                @csrf
                <label class="w-9 h-9 bg-white dark:bg-gray-800 text-gray-400 dark:text-gray-500 hover:text-primary-600 dark:hover:text-primary-500 rounded-xl flex items-center justify-center shadow-lg border border-gray-100 dark:border-gray-700 cursor-pointer transition-all hover:scale-110">
                    <i data-feather="camera" class="w-4 h-4"></i>
                    <input type="file" name="profile_photo" onchange="this.form.submit()" class="hidden">
                </label>
            </form>
        </div>

        <div class="flex-1 text-center md:text-left space-y-2">
            <div class="flex flex-wrap justify-center md:justify-start items-center gap-3">
                <span class="px-2.5 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-400 rounded-lg text-[9px] font-bold uppercase tracking-widest border border-primary-200/50 dark:border-primary-800/30">
                    Verified User
                </span>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $user->created_at->format('F Y') }}</span>
            </div>
            <h1 class="text-2xl md:text-3xl font-black text-gray-900 dark:text-white uppercase tracking-tight">{{ $user->name ?: 'UNNAMED OPERATOR' }}</h1>
            <p class="text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest flex items-center justify-center md:justify-start gap-2">
                <i data-feather="mail" class="w-3.5 h-3.5"></i>
                {{ $user->email }}
            </p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('settings.index') }}" class="h-11 px-6 flex items-center bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700 text-[10px] font-bold text-gray-600 dark:text-gray-400 uppercase tracking-widest rounded-xl hover:bg-white dark:hover:bg-gray-700 transition-all active:scale-95">
                Edit Settings
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 md:gap-8">
        
        {{-- IDENTITY DOSSIER --}}
        <div class="lg:col-span-8 space-y-6 md:space-y-8">
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 md:p-10 shadow-sm relative overflow-hidden">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-gray-50 dark:border-gray-800">
                    <div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest">Identity Dossier</h3>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Official Verification Records</p>
                    </div>
                </div>

                <form action="{{ route('profile.details.update') }}" method="POST" class="space-y-8">
                    @csrf @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Official Identification / NIK</label>
                            <input type="text" name="id_number" value="{{ old('id_number', $user->userDetail->id_number ?? '') }}"
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white placeholder-gray-400"
                                placeholder="32xxxxxxxxxxxxxx">
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Tax Identification / NPWP</label>
                            <input type="text" name="tax_id" value="{{ old('tax_id', $user->userDetail->tax_id ?? '') }}"
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white placeholder-gray-400"
                                placeholder="00.000.000.0-000.000">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone', $user->userDetail->phone ?? '') }}"
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white placeholder-gray-400"
                                placeholder="+62 812-xxxx-xxxx">
                        </div>
                        <div class="space-y-3">
                            <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Date of Birth</label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $user->userDetail->date_of_birth ?? '') }}"
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-3.5 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white">
                        </div>
                    </div>

                    <div class="space-y-3">
                        <label class="block text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest ml-1">Full Residency Address</label>
                        <textarea name="address" rows="3"
                            class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white placeholder-gray-400 leading-relaxed">{{ old('address', $user->userDetail->address ?? '') }}</textarea>
                    </div>

                    <div class="pt-6 flex justify-end">
                        <button type="submit" class="h-14 px-10 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg transition-all hover:scale-[1.02] active:scale-[0.98]">
                            Sync Identity Updates
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- SIDEBAR STATS --}}
        <div class="lg:col-span-4 space-y-6 md:space-y-8">
            {{-- PLATFORM SUMMARY --}}
            <div class="bg-gray-900 dark:bg-black rounded-2xl p-8 text-white relative overflow-hidden shadow-xl">
                {{-- Decorative Glow --}}
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-primary-600/30 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative z-10 space-y-8">
                    <div>
                        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Platform Statistics</h3>
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Network Uptime</p>
                                <p class="text-2xl font-black tabular-nums">{{ $user->created_at->diffInDays(now()) }}D</p>
                            </div>
                            <div>
                                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mb-1.5">Entity Count</p>
                                <p class="text-2xl font-black tabular-nums">{{ $user->companies->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-white/5 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Health Status</span>
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-tight">Active</span>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Security Level</span>
                            <span class="text-[10px] font-bold text-primary-400 uppercase tracking-tight">Enterprise</span>
                        </div>
                    </div>

                    <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest leading-loose">
                        Authorized node access secured since {{ $user->created_at->format('M Y') }}.
                    </p>
                </div>
            </div>

            {{-- SECURITY QUICK ACTIONS --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 shadow-sm group">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-10 h-10 bg-orange-50 dark:bg-orange-900/20 rounded-xl flex items-center justify-center text-orange-600 dark:text-orange-400">
                        <i data-feather="shield" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-gray-900 dark:text-white uppercase tracking-widest leading-none">Security Key</h3>
                        <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1.5">Rotate Passphrase</p>
                    </div>
                </div>
                
                <p class="text-[9px] font-bold text-gray-500 dark:text-gray-400 uppercase leading-relaxed mb-6">
                    Rotate your security credentials regularly to maintain peak system integrity.
                </p>

                <a href="{{ route('settings.index') }}" class="w-full flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-xl border border-gray-100 dark:border-gray-700 hover:border-primary-500 transition-all group-hover:bg-white dark:group-hover:bg-gray-700">
                    <span class="text-[9px] font-black text-gray-400 group-hover:text-primary-600 uppercase tracking-widest">Update Protocol</span>
                    <i data-feather="chevron-right" class="w-4 h-4 text-gray-300 group-hover:text-primary-600"></i>
                </a>
            </div>
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
