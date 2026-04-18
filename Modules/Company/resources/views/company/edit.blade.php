@extends('layouts.app', [
    'title' => 'Update Entity: ' . $company->name,
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'Workspaces', 'url' => route('companies.index')],
        ['name' => 'Entity Profile', 'url' => route('companies.show', $company)],
        ['name' => 'Configuration', 'url' => null],
    ]
])

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 pb-20">
    
    {{-- CONFIGURATION HEADER --}}
    <div class="bg-white dark:bg-gray-900 p-8 md:p-12 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-primary-500/5 rounded-full blur-3xl pointer-events-none transition-opacity group-hover:opacity-100"></div>
        
        <div class="relative z-10 max-w-2xl text-center md:text-left mx-auto md:mx-0">
            <div class="flex items-center justify-center md:justify-start gap-3 mb-6">
                <span class="px-3 py-1 bg-gray-900 dark:bg-gray-800 text-white dark:text-gray-300 rounded-lg text-[9px] font-black uppercase tracking-widest border border-gray-800 dark:border-gray-700">
                    Configuration Terminal
                </span>
                <div class="h-px w-8 bg-gray-200 dark:bg-gray-800"></div>
                <span class="text-[10px] font-bold text-gray-400 dark:text-gray-500 uppercase tracking-widest">Authorized Access</span>
            </div>
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white uppercase tracking-tighter leading-none mb-6">
                Refine <span class="text-primary-600">Entity Node</span>
            </h1>
            <p class="text-sm md:text-base font-bold text-gray-500 dark:text-gray-400 leading-relaxed uppercase">
                Adjust corporate identity, communication protocols, and operational compliance parameters for <span class="text-gray-900 dark:text-white">{{ $company->name }}</span>.
            </p>
        </div>
    </div>

    @if($company->status === 'pending')
        <div class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl p-8 border border-amber-100 dark:border-amber-900/30">
            <div class="flex flex-col md:flex-row gap-8 items-center">
                <div class="w-16 h-16 bg-white dark:bg-amber-900/40 rounded-2xl flex items-center justify-center text-amber-600 shadow-sm shrink-0">
                    <i data-feather="lock" class="w-8 h-8"></i>
                </div>
                <div class="flex-1 text-center md:text-left">
                    <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-2">Protocol Access Restricted</h4>
                    <p class="text-[11px] font-bold text-gray-500 dark:text-amber-500/70 uppercase leading-loose tracking-tight max-w-2xl">
                        Profile updates are currently locked while the entity is in a pending audit phase. Verification handshake must be completed before further identity modifications are allowed.
                    </p>
                </div>
                <div class="shrink-0">
                    <a href="{{ route('companies.show', $company) }}" class="h-12 px-8 flex items-center bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-lg transition-all hover:scale-105 active:scale-95">
                        Return to Dossier
                    </a>
                </div>
            </div>
        </div>
    @else
        <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf @method('PUT')
            
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
                
                {{-- CLUSTER 1: CORPORATE IDENTITY --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 md:p-10 shadow-sm h-full">
                    <div class="flex items-center gap-4 mb-10 pb-4 border-b border-gray-50 dark:border-gray-800">
                        <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                            <i data-feather="briefcase" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Identity Cluster</h3>
                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Core corporate naming and sector</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Official Legal Name</label>
                            <input type="text" name="name" required value="{{ old('name', $company->name) }}"
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white placeholder-gray-400">
                        </div>

                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Operational Tier</label>
                                <select name="category" required class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white">
                                    <option value="buyer" {{ $company->category === 'buyer' ? 'selected' : '' }}>BUYER_NODE</option>
                                    <option value="vendor" {{ $company->category === 'vendor' ? 'selected' : '' }}>VENDOR_NODE</option>
                                    <option value="supplier" {{ $company->category === 'supplier' ? 'selected' : '' }}>SUPPLIER_NODE</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Corporate Tag</label>
                                <input type="text" name="tag" value="{{ old('tag', $company->tag) }}" placeholder="e.g. LOGISTICS_HUB"
                                    class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Legal Sector (Business Category)</label>
                            <input type="text" name="business_category" required value="{{ old('business_category', $company->business_category) }}" placeholder="e.g. MANUFACTURING_EXCELLENCE"
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Corporate Mission</label>
                            <textarea name="description" rows="4"
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white uppercase leading-relaxed h-32">{{ old('description', $company->description) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- CLUSTER 2: COMMUNICATION NODE --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 md:p-10 shadow-sm h-full">
                    <div class="flex items-center gap-4 mb-10 pb-4 border-b border-gray-50 dark:border-gray-800">
                        <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                            <i data-feather="globe" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Communication Cluster</h3>
                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Operational signal and web presence</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Corporate Identifier (Logo)</label>
                            <div class="flex items-center gap-6 p-4 bg-gray-50 dark:bg-gray-800/50 rounded-xl border border-dashed border-gray-200 dark:border-gray-700">
                                @if($company->logo)
                                    <div class="w-20 h-20 rounded-lg bg-white dark:bg-gray-900 p-3 shadow-sm border border-gray-100 dark:border-gray-800 shrink-0">
                                        <img src="{{ asset('storage/' . $company->logo) }}" class="w-full h-full object-contain">
                                    </div>
                                @endif
                                <div class="relative flex-1">
                                    <input type="file" name="logo" accept="image/*" class="w-full h-12 opacity-0 absolute inset-0 cursor-pointer z-20">
                                    <div class="w-full h-12 bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 flex items-center justify-center gap-3 text-[10px] font-black text-gray-500 uppercase tracking-widest shadow-sm">
                                        <i data-feather="upload-cloud" class="w-4 h-4"></i>
                                        <span>Update Branding</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Official Web Interface</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                    <i data-feather="link-2" class="w-4 h-4"></i>
                                </div>
                                <input type="url" name="website" value="{{ old('website', $company->website) }}" placeholder="https://entity-node.dev"
                                    class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl pl-12 pr-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Communication Email</label>
                                <input type="email" name="email" value="{{ old('email', $company->email) }}" placeholder="signals@entity.dev"
                                    class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Direct Operational Line</label>
                                <input type="text" name="phone" value="{{ old('phone', $company->phone) }}" placeholder="+XX XXXX XXXX"
                                    class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CLUSTER 3: COMPLIANCE DOSSIER --}}
                <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 p-8 md:p-10 shadow-sm">
                    <div class="flex items-center gap-4 mb-10 pb-4 border-b border-gray-50 dark:border-gray-800">
                        <div class="w-10 h-10 bg-gray-50 dark:bg-gray-800 rounded-xl flex items-center justify-center text-gray-400">
                            <i data-feather="shield" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-[11px] font-black text-gray-900 dark:text-white uppercase tracking-widest">Compliance Dossier</h3>
                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-widest mt-1">Taxation and registration parameters</p>
                        </div>
                    </div>

                    <div class="space-y-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Registration Terminal ID</label>
                                <input type="text" name="registration_number" required value="{{ old('registration_number', $company->registration_number) }}"
                                    class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white uppercase tracking-widest">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">NPWP Identification</label>
                                <input type="text" name="npwp" value="{{ old('npwp', $company->npwp) }}" placeholder="XX.XXX.XXX.X-XXX.XXX"
                                    class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white uppercase tracking-widest">
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Compliance Region (Country)</label>
                            <input type="text" name="country" required value="{{ old('country', $company->country) }}" placeholder="e.g. INDONESIA"
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white uppercase tracking-widest">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3 ml-1">Primary Operational Address</label>
                            <input type="text" name="address" value="{{ old('address', $company->address) }}" placeholder="HEADQUARTERS PHYSICAL LOCATION"
                                class="w-full bg-gray-50 dark:bg-gray-800 border-gray-100 dark:border-gray-700 rounded-xl px-5 py-4 text-sm font-bold focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all text-gray-900 dark:text-white uppercase tracking-widest">
                        </div>
                    </div>
                </div>

                {{-- CLUSTER 4: OPERATIONAL NODES & ARTIFACTS --}}
                <div class="bg-gray-900 dark:bg-black rounded-2xl p-8 md:p-10 shadow-xl relative overflow-hidden text-white">
                    <div class="absolute bottom-0 right-0 -mb-20 -mr-20 w-64 h-64 bg-primary-600/10 rounded-full blur-3xl pointer-events-none"></div>
                    
                    <div class="flex items-center gap-4 mb-10 pb-4 border-b border-white/5 relative z-10">
                        <div class="w-10 h-10 bg-white/5 rounded-xl flex items-center justify-center text-primary-400">
                            <i data-feather="map" class="w-5 h-5"></i>
                        </div>
                        <div>
                            <h3 class="text-[11px] font-black text-white uppercase tracking-widest">Operations Cluster</h3>
                            <p class="text-[8px] font-bold text-gray-500 uppercase tracking-widest mt-1">Multi-regional distribution nodes</p>
                        </div>
                    </div>

                    <div class="space-y-8 relative z-10">
                        <div id="location-container" class="space-y-4">
                            @foreach($company->locations as $location)
                                <div class="location-input flex items-center gap-3">
                                    <input type="text" name="locations[]" value="{{ $location->address }}"
                                        class="flex-1 bg-white/5 border-white/10 rounded-xl px-4 py-3 text-[10px] font-black uppercase tracking-tight focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-white placeholder-gray-600">
                                    <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 bg-red-500/10 text-red-400 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                        <i data-feather="x" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        
                        <button type="button" onclick="addLocation()" class="h-12 w-full flex items-center justify-center gap-3 bg-white/5 border border-white/10 rounded-xl text-[9px] font-black tracking-widest uppercase hover:bg-white/10 transition-all active:scale-95">
                            <i data-feather="plus" class="w-4 h-4"></i>
                            Establish Regional Node
                        </button>

                        <div class="pt-6 border-t border-white/5">
                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Append Verification Artifacts</label>
                            <div class="relative group cursor-pointer">
                                <input type="file" name="documents[]" multiple class="w-full h-14 opacity-0 absolute inset-0 cursor-pointer z-20">
                                <div class="w-full h-14 bg-primary-600 text-white rounded-xl flex items-center justify-center gap-3 text-[10px] font-black uppercase tracking-widest shadow-lg shadow-primary-600/20 group-hover:bg-primary-500 transition-all">
                                    <i data-feather="file-plus" class="w-4 h-4"></i>
                                    <span>Upload COMPLIANCE_DOCS</span>
                                </div>
                            </div>
                            <p class="mt-3 text-[8px] font-bold text-gray-500 uppercase tracking-widest text-center">Supported: PDF, DOC, IMAGE (Max 10MB/File)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- AUDIT PROTOCOL NOTICE --}}
            <div class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl p-8 border border-amber-100 dark:border-amber-900/30">
                <div class="flex flex-col md:flex-row gap-8 items-center">
                    <div class="w-16 h-16 bg-white dark:bg-amber-900/40 rounded-2xl flex items-center justify-center text-amber-600 shadow-sm shrink-0">
                        <i data-feather="refresh-cw" class="w-8 h-8 animate-spin-slow"></i>
                    </div>
                    <div class="flex-1 text-center md:text-left">
                        <h4 class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-widest mb-2 text-amber-600">Audit Reset Protocol</h4>
                        <p class="text-[10px] font-bold text-gray-500 dark:text-amber-500/70 uppercase leading-loose tracking-tight max-w-2xl">
                            Commitment to verification: Modifying core entity parameters will automatically trigger a <span class="text-amber-600 font-black">PENDING_AUDIT</span> state. The entity node will require administrative re-approval for full network restoration.
                        </p>
                    </div>
                </div>
            </div>

            {{-- EXECUTION CONTROLS --}}
            <div class="flex items-center justify-between gap-6 pt-8">
                <a href="{{ route('companies.show', $company) }}" class="h-16 px-10 flex items-center bg-white dark:bg-gray-800 border border-gray-100 dark:border-gray-800 text-[10px] font-black text-gray-500 dark:text-gray-400 uppercase tracking-widest rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition-all shadow-sm">
                    Discard Changes
                </a>
                <button type="submit" class="h-16 flex-1 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-[10px] font-black uppercase tracking-widest rounded-xl shadow-xl transition-all hover:scale-[1.01] active:scale-[0.99]">
                    Commit Entity Updates
                </button>
            </div>
        </form>
    @endif
</div>

<script>
    function addLocation() {
        const container = document.getElementById('location-container');
        const div = document.createElement('div');
        div.className = 'location-input flex items-center gap-3';
        div.innerHTML = `
            <input type="text" name="locations[]" 
                class="flex-1 bg-white/5 border-white/10 rounded-xl px-4 py-3 text-[10px] font-black uppercase tracking-tight focus:ring-4 focus:ring-primary-500/20 focus:border-primary-500 transition-all text-white placeholder-gray-600"
                placeholder="ENTER PHYSICAL ADDRESS">
            <button type="button" onclick="this.parentElement.remove()" class="w-10 h-10 bg-red-500/10 text-red-400 rounded-lg flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
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

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
