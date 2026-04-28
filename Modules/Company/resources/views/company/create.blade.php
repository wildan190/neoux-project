@extends('layouts.app', [
    'title' => 'Register New Entity',
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Workspaces', 'url' => route('companies.index')],
        ['name' => 'Registration', 'url' => null],
    ]
])

@section('content')
<div class="max-w-7xl mx-auto space-y-12 pb-24">
    {{-- Header --}}
    <div class="mb-12">
        <div class="flex items-center gap-3 mb-4">
            <span class="px-3 py-1 bg-primary-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">REGISTRATION</span>
            <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Global Marketplace Identity</span>
        </div>
        <h1 class="text-5xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-6">
            Establish <span class="text-primary-600">Company Node</span>
        </h1>
        <p class="text-gray-500 font-bold lowercase text-sm">Deploy a verified enterprise identity to initiate procurement and trading operations.</p>
    </div>

    <form action="{{ route('companies.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="space-y-12">
            {{-- Primary Identity --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 md:p-8 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-12 text-gray-50/50 dark:text-gray-900/50 pointer-events-none">
                    <i data-feather="briefcase" class="w-48 h-48"></i>
                </div>
                
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-12 relative z-10">Primary Enterprise Identity</h3>
                
                <div class="space-y-10 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Official Legal Name</label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner placeholder:text-gray-500"
                                placeholder="e.g. PT. Global Solusi">
                            @error('name') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="npwp" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Registration Terminal ID</label>
                            <input type="text" name="npwp" id="npwp" required value="{{ old('npwp') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner placeholder:text-gray-500"
                                placeholder="Tax ID (NPWP) or Business ID (NIB)">
                            @error('npwp') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <label for="category" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Workgroup Category</label>
                            <select name="category" id="category" required
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner">
                                <option value="" disabled selected>SELECT SECTOR</option>
                                <option value="buyer">BUYER (PURCHASING ENTITY)</option>
                                <option value="vendor">VENDOR / SUPPLIER</option>
                            </select>
                            @error('category') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="logo" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Corporate Identifier (Logo)</label>
                            <div class="relative" x-data="{ fileName: '' }">
                                <input type="file" name="logo" id="logo" accept="image/*"
                                    @change="fileName = $event.target.files[0]?.name"
                                    class="w-full h-[68px] opacity-0 absolute inset-0 cursor-pointer z-20">
                                <div class="w-full bg-gray-50 dark:bg-gray-900 rounded-xl px-4 py-3 flex items-center gap-4 text-[11px] font-black uppercase tracking-tight shadow-inner transition-colors"
                                    :class="fileName ? 'text-primary-600 bg-primary-50 dark:bg-primary-900/20 ring-1 ring-primary-500' : 'text-gray-400'">
                                    <span x-show="!fileName"><i data-feather="upload-cloud" class="w-5 h-5"></i></span>
                                    <span x-show="fileName" x-cloak><i data-feather="check-circle" class="w-5 h-5"></i></span>
                                    <span x-text="fileName || 'Upload Corporate Logo'"></span>
                                </div>
                            </div>
                            @error('logo') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div>
                            <label for="business_category" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Business Sector</label>
                            <input type="text" name="business_category" id="business_category" required value="{{ old('business_category') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner placeholder:text-gray-500"
                                placeholder="e.g. IT, Retail, Manufacturing">
                            @error('business_category') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="country" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Operating Country</label>
                            <select name="country" id="country" required
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner">
                                <option value="" disabled selected>SELECT COUNTRY</option>
                                <option value="Indonesia" {{ old('country') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                                <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>Singapore</option>
                                <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                                <option value="Other" {{ old('country') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('country') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="tag" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Enterprise Tag</label>
                            <input type="text" name="tag" id="tag" required value="{{ old('tag') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner placeholder:text-gray-500"
                                placeholder="e.g. B2B, B2C, Distributor">
                            @error('tag') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                        <div>
                            <label for="email" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Communication Email</label>
                            <input type="email" name="email" id="email" value="{{ old('email') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner placeholder:text-gray-500"
                                placeholder="signals@entity.dev">
                            @error('email') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label for="phone" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Direct Operational Line</label>
                            <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner placeholder:text-gray-500"
                                placeholder="+XX XXXX XXXX">
                            @error('phone') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="website" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Official Web Interface</label>
                            <input type="url" name="website" id="website" value="{{ old('website') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner placeholder:text-gray-500"
                                placeholder="https://entity-node.dev">
                            @error('website') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Enterprise Mission Statement</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner leading-loose placeholder:text-gray-500"
                            placeholder="Briefly describe your company's core business and services...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Operational Assets --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                {{-- Locations --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 md:p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-10">Operations Centers</h3>
                    <div id="location-container" class="space-y-6">
                        <div class="location-input relative">
                            <input type="text" name="locations[]" 
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[10px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner placeholder:text-gray-500"
                                placeholder="Headquarters full address (Street, City, Province, Postal Code)">
                        </div>
                    </div>
                    <button type="button" onclick="addLocation()" class="mt-8 flex items-center gap-3 text-[9px] font-black text-primary-600 uppercase tracking-[0.2em] group">
                        <span class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center group-hover:bg-primary-600 group-hover:text-white transition-all shadow-sm">
                            <i data-feather="plus" class="w-4 h-4"></i>
                        </span>
                        Establish Additional Node
                    </button>
                </div>

                {{-- Verification Artifacts --}}
                <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-800 p-6 md:p-8 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-10">Verification Artifacts</h3>
                    <div class="relative group" x-data="{ fileCount: 0 }">
                        <input type="file" name="documents[]" multiple id="documents"
                            @change="fileCount = $event.target.files.length"
                            class="w-full h-40 opacity-0 absolute inset-0 cursor-pointer z-20">
                        <div class="w-full h-40 bg-gray-50 dark:bg-gray-900 rounded-xl border-2 border-dashed flex flex-col items-center justify-center p-6 transition-all shadow-inner"
                            :class="fileCount > 0 ? 'border-primary-500 bg-primary-50/50 dark:bg-primary-900/10' : 'border-gray-100 dark:border-gray-800 group-hover:border-primary-300'">
                            
                            <span :class="fileCount > 0 ? 'text-primary-500' : 'text-gray-200'" class="mb-4 inline-block">
                                <i data-feather="file-text" class="w-10 h-10"></i>
                            </span>
                            
                            <p class="text-[10px] font-black uppercase tracking-widest" :class="fileCount > 0 ? 'text-primary-600' : 'text-gray-400'">
                                <span x-text="fileCount > 0 ? fileCount + ' Files Attached' : 'Upload Corporate Documentation'"></span>
                            </p>
                            <p class="text-[8px] font-bold text-gray-300 uppercase tracking-widest mt-2" x-show="fileCount === 0">MULTIPLE FILES PERMITTED (PDF, JPG, PNG)</p>
                            <p class="text-[8px] font-bold text-primary-400 uppercase tracking-widest mt-2" x-show="fileCount > 0" x-cloak>Click to change selection</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Data Synchronization --}}
            <div class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl border border-amber-100 dark:border-amber-800/30 p-6 md:p-8 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-12 text-amber-500/10 pointer-events-none">
                    <span class="inline-block"><i data-feather="database" class="w-48 h-48"></i></span>
                </div>
                
                <div class="flex items-center gap-3 mb-10 relative z-10">
                    <h3 class="text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase tracking-[0.3em]">Historical PO Sync</h3>
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400 rounded text-[8px] font-black uppercase tracking-widest">OPTIONAL</span>
                </div>
                
                <div class="relative group z-10" x-data="{ fileName: '' }">
                    <input type="file" name="historical_po" id="historical_po" accept=".xlsx,.xls,.csv"
                        @change="fileName = $event.target.files[0]?.name"
                        class="w-full h-24 opacity-0 absolute inset-0 cursor-pointer z-20">
                    <div class="w-full h-24 bg-white/50 dark:bg-gray-900/50 rounded-xl border border-amber-200 dark:border-amber-800/50 flex flex-col items-center justify-center p-4 transition-all shadow-inner backdrop-blur-sm"
                        :class="fileName ? 'border-amber-500 bg-amber-100/50 dark:bg-amber-900/30 ring-1 ring-amber-500/50' : 'hover:border-amber-400'">
                        
                        <div class="flex items-center gap-3">
                            <span :class="fileName ? 'text-amber-600' : 'text-amber-400'">
                                <span x-show="!fileName"><i data-feather="upload-cloud" class="w-5 h-5"></i></span>
                                <span x-show="fileName" x-cloak><i data-feather="check-circle" class="w-5 h-5"></i></span>
                            </span>
                            
                            <p class="text-[11px] font-black uppercase tracking-widest" :class="fileName ? 'text-amber-700 dark:text-amber-400' : 'text-amber-600/70 dark:text-amber-500/70'">
                                <span x-text="fileName || 'Upload Historical PO Excel'"></span>
                            </p>
                        </div>
                    </div>
                </div>
                @error('historical_po') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase relative z-10">{{ $message }}</p> @enderror
            </div>

            {{-- Policy Acknowledgment --}}
            <div class="bg-gray-900 rounded-2xl p-6 md:p-8 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary-600/20 rounded-full blur-[80px] pointer-events-none"></div>
                
                <div class="flex gap-10 relative z-10">
                    <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-primary-400 shrink-0">
                        <i data-feather="shield" class="w-8 h-8"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-widest mb-3">Verification Protocol</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase leading-loose tracking-tight max-w-2xl">
                            Deploying a new entity node initiates a global verification sequence. Administrative review is required before full marketplace synchronization is established. Ensure all legal artifacts are accurate to accelerate verification.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Execution Controls --}}
            <div class="flex items-center justify-between gap-8 pt-8">
                <a href="{{ route('companies.index') }}" class="px-6 py-4 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 text-[11px] font-black text-gray-400 uppercase tracking-widest rounded-xl hover:bg-gray-50 transition-all">
                    Discard Initiation
                </a>
                <button type="submit" class="py-4 px-6 flex-1 bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-xl shadow-xl shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-[0.98]">
                    Establish Global Identity
                </button>
            </div>
        </div>
    </form>
</div>

<script>
    function addLocation() {
        const container = document.getElementById('location-container');
        const div = document.createElement('div');
        div.className = 'location-input relative flex items-center gap-4';
        div.innerHTML = `
            <input type="text" name="locations[]" 
                class="flex-1 bg-gray-50 dark:bg-gray-900 border-transparent rounded-xl px-4 py-3 text-[10px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner placeholder:text-gray-500"
                placeholder="Branch or warehouse full address">
            <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 bg-red-50 text-red-500 rounded-lg flex items-center justify-center shadow-sm">
                <i data-feather="x" class="w-4 h-4"></i>
            </button>
        `;
        container.appendChild(div);
        feather.replace();
    }
</script>
@endsection

@push('scripts')
{{-- Scripts are handled by layout --}}
@endpush
