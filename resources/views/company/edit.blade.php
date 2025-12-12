@extends('layouts.app', [
    'title' => 'Edit Company',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Companies', 'url' => route('companies.index')],
        ['name' => 'Edit', 'url' => '#']
    ]
])

@section('content')
    <div class="space-y-6">
        {{-- Header with illustration --}}
        <div class="bg-gradient-to-r from-primary-500 to-secondary-500 rounded-2xl p-8 text-white shadow-xl">
            <div class="flex items-center gap-6">
                <div class="w-20 h-20 bg-white/20 backdrop-blur rounded-2xl flex items-center justify-center overflow-hidden">
                    @if($company->logo)
                        <img src="{{ asset('storage/' . $company->logo) }}" class="h-full w-full object-cover">
                    @else
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="text-3xl font-bold mb-2">Edit Company</h1>
                    <p class="text-white/80">Perbarui informasi perusahaan <strong>{{ $company->name }}</strong></p>
                </div>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h3 class="font-semibold text-red-800 dark:text-red-200 mb-1">Terdapat kesalahan pada form</h3>
                        <ul class="text-sm text-red-700 dark:text-red-300 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Step 1: Basic Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center text-sm font-bold">1</div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Informasi Dasar</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nama dan tipe perusahaan</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    {{-- Logo Upload --}}
                    <div class="flex flex-col sm:flex-row items-start gap-6 mb-6 pb-6 border-b border-gray-100 dark:border-gray-700">
                        <div class="flex-shrink-0">
                            <div id="logo-preview" class="h-24 w-24 rounded-2xl bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-300 dark:border-gray-500 transition-all hover:border-primary-400">
                                @if($company->logo)
                                    <img src="{{ asset('storage/' . $company->logo) }}" class="h-full w-full object-cover rounded-2xl">
                                @else
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                @endif
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Logo Perusahaan</label>
                            <label for="logo" class="inline-flex items-center gap-2 cursor-pointer bg-white dark:bg-gray-700 py-2.5 px-4 border border-gray-300 dark:border-gray-600 rounded-lg shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span>Ganti Logo</span>
                                <input id="logo" name="logo" type="file" class="sr-only" accept="image/*" onchange="previewLogo(this)">
                            </label>
                            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Format: JPG, PNG, GIF. Maksimal 2MB
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nama Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $company->name) }}" 
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" 
                                placeholder="PT. Contoh Indonesia" required>
                        </div>

                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tipe Perusahaan <span class="text-red-500">*</span>
                            </label>
                            <select name="category" id="category" 
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" required>
                                <option value="buyer" {{ old('category', $company->category) == 'buyer' ? 'selected' : '' }}>🛒 Buyer (Pembeli)</option>
                                <option value="supplier" {{ old('category', $company->category) == 'supplier' ? 'selected' : '' }}>📦 Supplier (Pemasok)</option>
                                <option value="vendor" {{ old('category', $company->category) == 'vendor' ? 'selected' : '' }}>🏪 Vendor (Penjual)</option>
                            </select>
                        </div>

                        <div>
                            <label for="business_category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Kategori Bisnis <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="business_category" id="business_category" value="{{ old('business_category', $company->business_category) }}"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" 
                                placeholder="e.g. Konstruksi, Teknologi, F&B" required>
                        </div>

                        <div>
                            <label for="npwp" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                NPWP
                            </label>
                            <input type="text" name="npwp" id="npwp" value="{{ old('npwp', $company->npwp) }}"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" 
                                placeholder="00.000.000.0-000.000">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 2: Contact Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center text-sm font-bold">2</div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Kontak & Lokasi</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Informasi kontak dan alamat perusahaan</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email Perusahaan
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <input type="email" name="email" id="email" value="{{ old('email', $company->email) }}"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 pl-10 pr-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" 
                                    placeholder="info@perusahaan.co.id">
                            </div>
                        </div>

                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nomor Telepon
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                </div>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $company->phone) }}"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 pl-10 pr-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" 
                                    placeholder="+62 21 1234567">
                            </div>
                        </div>

                        <div>
                            <label for="website" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Website
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                </div>
                                <input type="url" name="website" id="website" value="{{ old('website', $company->website) }}"
                                    class="w-full rounded-lg border border-gray-300 dark:border-gray-600 pl-10 pr-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" 
                                    placeholder="https://www.perusahaan.co.id">
                            </div>
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Negara
                            </label>
                            <input type="text" name="country" id="country" value="{{ old('country', $company->country ?? 'Indonesia') }}"
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition">
                        </div>

                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Alamat Lengkap
                            </label>
                            <textarea name="address" id="address" rows="3" 
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition resize-none"
                                placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan, Kota, Kode Pos">{{ old('address', $company->address) }}</textarea>
                        </div>
                    </div>

                    {{-- Operation Locations --}}
                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lokasi Operasional</label>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Tambahkan lokasi cabang atau warehouse</p>
                            </div>
                            <button type="button" onclick="addLocation()" class="inline-flex items-center gap-1.5 text-sm text-primary-600 hover:text-primary-500 font-medium transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                Tambah Lokasi
                            </button>
                        </div>
                        <div id="locations-container" class="space-y-3">
                            @forelse($company->locations as $location)
                                <div class="flex gap-2 items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <input type="text" name="locations[]" value="{{ $location->address }}" class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2.5 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" 
                                        placeholder="Alamat lokasi operasional">
                                    <button type="button" onclick="this.parentElement.remove()" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </div>
                            @empty
                                <div class="flex gap-2 items-center">
                                    <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    </div>
                                    <input type="text" name="locations[]" class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2.5 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" 
                                        placeholder="Alamat lokasi operasional">
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Step 3: Additional Info --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-primary-500 text-white rounded-full flex items-center justify-center text-sm font-bold">3</div>
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">Detail Tambahan</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Deskripsi, tags, dan dokumen pendukung</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="tag" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tags</label>
                        <input type="text" name="tag" id="tag" value="{{ old('tag', $company->tag) }}"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" 
                            placeholder="Konstruksi, Material, Bahan Bangunan">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Pisahkan dengan koma untuk memudahkan pencarian</p>
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi Perusahaan</label>
                        <textarea name="description" id="description" rows="4" 
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-3 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition resize-none"
                            placeholder="Jelaskan secara singkat tentang perusahaan Anda...">{{ old('description', $company->description) }}</textarea>
                    </div>

                    {{-- Document Upload --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dokumen Perusahaan (Tambah Baru)</label>
                        <div id="drop-zone" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-primary-400 hover:bg-primary-50/50 dark:hover:bg-primary-900/10 transition-all cursor-pointer bg-gray-50 dark:bg-gray-700/30">
                            <input type="file" name="documents[]" id="documents" multiple class="hidden">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Klik untuk upload atau drag & drop</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">PDF, JPG, PNG - Max 2MB per file</p>
                        </div>
                        <div id="file-list" class="mt-4 space-y-2"></div>
                    </div>
                </div>
            </div>

            {{-- Submit Section --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">Perubahan akan disimpan</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Pastikan semua data sudah benar sebelum menyimpan</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('companies.show', $company) }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                            Batal
                        </a>
                        <button type="submit" class="px-8 py-3 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white rounded-xl font-bold shadow-lg shadow-primary-500/30 transition-all transform hover:scale-105 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const dt = new DataTransfer();
        const dropZone = document.getElementById('drop-zone');
        const fileInput = document.getElementById('documents');
        const fileList = document.getElementById('file-list');

        function previewLogo(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logo-preview').innerHTML = '<img src="'+e.target.result+'" class="h-full w-full object-cover rounded-2xl">';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function addLocation() {
            const container = document.getElementById('locations-container');
            const div = document.createElement('div');
            div.className = 'flex gap-2 items-center';
            div.innerHTML = `
                <div class="flex-shrink-0 w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <input type="text" name="locations[]" class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600 px-4 py-2.5 shadow-sm focus:border-primary-500 focus:ring-2 focus:ring-primary-500/20 dark:bg-gray-700 dark:text-white transition" placeholder="Alamat lokasi operasional">
                <button type="button" onclick="this.parentElement.remove()" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            `;
            container.appendChild(div);
        }

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900/20');
            handleFiles(e.dataTransfer.files);
        });

        fileInput.addEventListener('change', function() {
            handleFiles(this.files);
        });

        function handleFiles(files) {
            Array.from(files).forEach(file => {
                let exists = false;
                for (let i = 0; i < dt.items.length; i++) {
                    if (dt.items[i].getAsFile().name === file.name && dt.items[i].getAsFile().size === file.size) {
                        exists = true;
                        break;
                    }
                }
                if (!exists) {
                    dt.items.add(file);
                }
            });

            updateFileInput();
            renderFileList();
        }

        function updateFileInput() {
            fileInput.files = dt.files;
        }

        function removeFile(index) {
            dt.items.remove(index);
            updateFileInput();
            renderFileList();
        }

        function renderFileList() {
            fileList.innerHTML = '';
            Array.from(dt.files).forEach((file, index) => {
                const div = document.createElement('div');
                div.className = 'flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600';
                div.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">${file.name}</p>
                            <p class="text-xs text-gray-500">${(file.size / 1024 / 1024).toFixed(2)} MB</p>
                        </div>
                    </div>
                    <button type="button" onclick="removeFile(${index})" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                `;
                fileList.appendChild(div);
            });
        }
    </script>
@endsection
