@extends('layouts.app', [
    'title' => 'Register New Entity',
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Workspaces', 'url' => route('companies.index')],
        ['name' => 'Registration', 'url' => null],
    ]
])

@section('content')
<div class="max-w-4xl mx-auto space-y-12 pb-24">
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
            <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] border border-gray-100 dark:border-gray-800 p-12 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-12 text-gray-50/50 dark:text-gray-900/50 pointer-events-none">
                    <i data-feather="briefcase" class="w-48 h-48"></i>
                </div>
                
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-12 relative z-10">Primary Enterprise Identity</h3>
                
                <div class="space-y-10 relative z-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Official Legal Name</label>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-[1.5rem] p-6 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                                placeholder="ENTER REGISTERED COMPANY NAME">
                            @error('name') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="registration_number" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Registration Terminal ID</label>
                            <input type="text" name="registration_number" id="registration_number" required value="{{ old('registration_number') }}"
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-[1.5rem] p-6 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                                placeholder="BRN / BUSINESS REG NO">
                            @error('registration_number') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                        <div>
                            <label for="category" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Workgroup Category</label>
                            <select name="category" id="category" required
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-[1.5rem] p-6 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner">
                                <option value="" disabled selected>SELECT SECTOR</option>
                                <option value="buyer">BUYER (PURCHASING ENTITY)</option>
                                <option value="vendor">VENDOR / SUPPLIER</option>
                                <option value="logistics">LOGISTICS PROVIDER</option>
                                <option value="other">OTHER PROFESSIONAL ENTITY</option>
                            </select>
                            @error('category') <p class="mt-2 text-[10px] font-bold text-red-500 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="logo" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Corporate Identifier (Logo)</label>
                            <div class="relative">
                                <input type="file" name="logo" id="logo" accept="image/*"
                                    class="w-full h-[68px] opacity-0 absolute inset-0 cursor-pointer z-20">
                                <div class="w-full bg-gray-50 dark:bg-gray-900 rounded-[1.5rem] p-6 flex items-center gap-4 text-[11px] font-black text-gray-400 uppercase tracking-tight shadow-inner">
                                    <i data-feather="upload-cloud" class="w-5 h-5"></i>
                                    <span>Upload HQ Visual</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="description" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Enterprise Mission Statement</label>
                        <textarea name="description" id="description" rows="4"
                            class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-[2rem] p-8 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner leading-loose"
                            placeholder="DESCRIBE CORPORATE ASSETS AND MARKET OPERATIONS...">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Operational Assets --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                {{-- Locations --}}
                <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] border border-gray-100 dark:border-gray-800 p-12 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-10">Operations Centers</h3>
                    <div id="location-container" class="space-y-6">
                        <div class="location-input relative">
                            <input type="text" name="locations[]" 
                                class="w-full bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl p-5 text-[10px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                                placeholder="PRIMARY PHYSICAL ADDRESS">
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
                <div class="bg-white dark:bg-gray-800 rounded-[3.5rem] border border-gray-100 dark:border-gray-800 p-12 shadow-sm">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-10">Verification Artifacts</h3>
                    <div class="relative group">
                        <input type="file" name="documents[]" multiple id="documents"
                            class="w-full h-40 opacity-0 absolute inset-0 cursor-pointer z-20">
                        <div class="w-full h-40 bg-gray-50 dark:bg-gray-900 rounded-[2rem] border-2 border-dashed border-gray-100 dark:border-gray-800 flex flex-col items-center justify-center p-8 transition-all group-hover:border-primary-300 shadow-inner">
                            <i data-feather="file-text" class="w-10 h-10 text-gray-200 mb-4"></i>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Upload Corporate Documentation</p>
                            <p class="text-[8px] font-bold text-gray-300 uppercase tracking-widest mt-2">MULTIPLE FILES PERMITTED (PDF, JPG, PNG)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Policy Acknowledgment --}}
            <div class="bg-gray-900 rounded-[3rem] p-12 text-white shadow-2xl relative overflow-hidden">
                <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary-600/20 rounded-full blur-[80px] pointer-events-none"></div>
                
                <div class="flex gap-10 relative z-10">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center text-primary-400 shrink-0">
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
                <a href="{{ route('companies.index') }}" class="h-20 px-12 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 text-[11px] font-black text-gray-400 uppercase tracking-widest rounded-[1.75rem] hover:bg-gray-50 transition-all">
                    Discard Initiation
                </a>
                <button type="submit" class="h-20 flex-1 bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-[1.75rem] shadow-2xl shadow-primary-600/40 hover:bg-primary-700 transition-all active:scale-[0.98]">
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
                class="flex-1 bg-gray-50 dark:bg-gray-900 border-transparent rounded-2xl p-5 text-[10px] font-black uppercase tracking-tight focus:ring-primary-500 focus:bg-white transition-all shadow-inner"
                placeholder="SECONDARY OFFICE / WAREHOUSE">
            <button type="button" onclick="this.parentElement.remove()" class="w-12 h-12 bg-red-50 text-red-500 rounded-xl flex items-center justify-center shadow-sm">
                <i data-feather="x" class="w-4 h-4"></i>
            </button>
        `;
        container.appendChild(div);
        feather.replace();
    }
</script>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
