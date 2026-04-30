<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Onboarding - Company Registration | Huntr.id</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            gold: 'rgb(245, 193, 66)',
                            orange: 'rgb(199, 95, 52)',
                            accent: 'rgb(245, 193, 66)',
                        }
                    },
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
        .step-active { background: rgb(245, 193, 66); color: #000; box-shadow: 0 0 25px rgba(245, 193, 66, 0.3); }
        .gradient-brand { background: linear-gradient(135deg, rgb(245, 193, 66) 0%, rgb(199, 95, 52) 100%); }
        .gradient-text { background: linear-gradient(135deg, rgb(245, 193, 66) 0%, rgb(199, 95, 52) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .step-transition { transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        input:focus { outline: none; border-color: rgb(245, 193, 66); box-shadow: 0 0 0 4px rgba(245, 193, 66, 0.1); }
        .shimmer { background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.05) 50%, rgba(255,255,255,0) 100%); background-size: 200% 100%; animation: shimmer 2s infinite; }
        @keyframes shimmer { 0% { background-position: -200% 0; } 100% { background-position: 200% 0; } }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    </style>
</head>
<body class="h-full text-slate-200 antialiased">
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-[10%] -left-[10%] w-[40%] h-[40%] bg-brand-gold/10 blur-[120px] rounded-full"></div>
        <div class="absolute -bottom-[10%] -right-[10%] w-[40%] h-[40%] bg-brand-orange/10 blur-[120px] rounded-full"></div>
    </div>

    <div class="relative min-h-full flex items-center justify-center p-4 md:p-8">
        <div class="w-full max-w-5xl glass rounded-[2rem] md:rounded-[2.5rem] overflow-hidden shadow-2xl flex flex-col lg:flex-row min-h-[600px]">
            {{-- Sidebar Stepper --}}
            <div class="w-full lg:w-72 bg-slate-900/50 p-6 md:p-10 border-b lg:border-b-0 lg:border-r border-white/5 flex flex-row lg:flex-col items-center lg:items-start justify-between lg:justify-start gap-8">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 md:w-10 md:h-10 gradient-brand rounded-lg md:rounded-xl flex items-center justify-center shadow-lg shadow-brand-gold/20">
                        <i data-feather="box" class="w-4 h-4 md:w-5 md:h-5 text-black"></i>
                    </div>
                    <span class="text-lg md:text-xl font-bold tracking-tight text-white">Huntr<span class="text-brand-gold">.id</span></span>
                </div>

                <div class="flex lg:flex-col lg:space-y-10 gap-4 lg:gap-0 overflow-x-auto lg:overflow-x-visible pb-2 lg:pb-0 scrollbar-hide">
                    <div class="flex items-center gap-3 md:gap-4 group shrink-0" id="step-nav-1">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full border-2 border-brand-gold/30 flex items-center justify-center text-xs md:text-sm font-bold step-transition" id="step-circle-1">1</div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Step 01</p>
                            <p class="text-xs font-semibold text-white">NPWP Check</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 md:gap-4 group opacity-40 shrink-0" id="step-nav-2">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full border-2 border-white/10 flex items-center justify-center text-xs md:text-sm font-bold step-transition" id="step-circle-2">2</div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Step 02</p>
                            <p class="text-xs font-semibold text-white">Basic Info</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 md:gap-4 group opacity-40 shrink-0" id="step-nav-3">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full border-2 border-white/10 flex items-center justify-center text-xs md:text-sm font-bold step-transition" id="step-circle-3">3</div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Step 03</p>
                            <p class="text-xs font-semibold text-white" id="step-label-3">Data Import</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 md:gap-4 group opacity-40 shrink-0" id="step-nav-4">
                        <div class="w-8 h-8 md:w-10 md:h-10 rounded-full border-2 border-white/10 flex items-center justify-center text-xs md:text-sm font-bold step-transition" id="step-circle-4">4</div>
                        <div class="hidden md:block">
                            <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">Step 04</p>
                            <p class="text-xs font-semibold text-white">Finalize</p>
                        </div>
                    </div>
                </div>

                <div class="hidden lg:block mt-auto pt-20">
                    <div class="p-5 bg-white/5 rounded-2xl border border-white/5">
                        <p class="text-[10px] text-slate-400 leading-relaxed font-medium">Need help? <br> <span class="text-brand-gold">help@huntr.id</span></p>
                    </div>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="flex-1 p-6 md:p-10 lg:p-16 relative flex flex-col h-full overflow-y-auto max-h-[80vh] md:max-h-none">
                <form id="onboardingForm" action="{{ route('onboarding.store') }}" method="POST" enctype="multipart/form-data" class="flex-1">
                    @csrf
                    <input type="hidden" id="company_npwp_hidden" name="npwp" value="{{ old('npwp') }}">
                    
                    {{-- Step 1: NPWP Validation --}}
                    <div id="step-content-1" class="step-transition">
                        <h2 class="text-2xl md:text-3xl font-bold text-white mb-2 leading-tight tracking-tight">Tax Identification</h2>
                        <p class="text-slate-400 mb-8 md:mb-10 text-sm md:text-base font-medium">Validate your company's NPWP for automated setup.</p>

                        <div class="space-y-6">
                            <div class="group">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-3">NPWP Number</label>
                                <div class="relative">
                                    <input type="text" id="npwp_input" placeholder="00.000.000.0-000.000" value="{{ old('npwp') }}"
                                        class="w-full bg-slate-900/50 border border-white/10 rounded-xl md:rounded-2xl px-5 md:px-6 py-3.5 md:py-4 text-white text-base md:text-lg font-bold tracking-widest transition-all placeholder:text-slate-400">
                                    <div id="npwp_loading" class="absolute right-4 top-1/2 -translate-y-1/2 hidden">
                                        <svg class="animate-spin h-5 w-5 text-brand-gold" xmlns="http://www.w3.org/2000/svg" fill="none" viewbox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <p id="npwp_error" class="text-red-400 text-[10px] md:text-xs mt-3 hidden font-bold uppercase tracking-tight"></p>
                                @error('npwp') <p class="text-red-400 text-[10px] md:text-xs mt-3 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>

                            <div id="npwp_success_card" class="hidden p-5 md:p-6 bg-emerald-500/10 border border-emerald-500/20 rounded-2xl space-y-4 shimmer">
                                <div class="flex items-center gap-3 text-emerald-400 mb-2">
                                    <i data-feather="check-circle" class="w-4 h-4"></i>
                                    <span class="text-[9px] md:text-[10px] font-bold uppercase tracking-widest">Verified Tax Record</span>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Entity Name</p>
                                        <p id="res_name" class="text-xs md:text-sm font-bold text-white uppercase"></p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Status</p>
                                        <p id="res_status" class="text-xs md:text-sm font-bold text-white uppercase"></p>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-widest mb-1">Registered Address</p>
                                    <p id="res_address" class="text-[10px] md:text-xs text-slate-300"></p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 md:mt-12 flex justify-end">
                            <button type="button" id="btn-validate-npwp" 
                                class="w-full md:w-auto px-10 py-4 gradient-brand text-black rounded-xl md:rounded-2xl text-xs md:text-sm font-black uppercase tracking-widest transition-all shadow-xl shadow-brand-gold/10 hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                                Validate ID
                            </button>
                            <button type="button" id="btn-to-step-2" 
                                class="hidden w-full md:w-auto px-10 py-4 gradient-brand text-black rounded-xl md:rounded-2xl text-xs md:text-sm font-black uppercase tracking-widest transition-all shadow-xl shadow-brand-gold/10 hover:scale-[1.02] active:scale-[0.98]">
                                Continue <i data-feather="arrow-right" class="inline w-4 h-4 ml-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Step 2: Basic Info --}}
                    <div id="step-content-2" class="hidden step-transition">
                        <h2 class="text-2xl md:text-3xl font-bold text-white mb-2 leading-tight">Company Identity</h2>
                        <p class="text-slate-400 mb-8 md:mb-10 text-sm md:text-base font-medium">Tell us more about your business operations.</p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Legal Company Name</label>
                                <input type="text" name="name" id="company_name_input" value="{{ old('name') }}" required
                                    class="w-full bg-slate-900/50 border border-white/10 rounded-xl md:rounded-2xl px-5 md:px-6 py-3.5 md:py-4 text-white text-sm md:text-base font-semibold transition-all placeholder:text-slate-400">
                                @error('name') <p class="text-red-400 text-[10px] mt-2 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Account Type</label>
                                <div class="relative">
                                    <select name="category" required
                                        class="w-full bg-slate-900/50 border border-white/10 rounded-xl md:rounded-2xl px-5 md:px-6 py-3.5 md:py-4 text-white text-sm md:text-base font-semibold transition-all appearance-none cursor-pointer">
                                        <option value="buyer" {{ old('category') == 'buyer' ? 'selected' : '' }}>Buyer / Procurement</option>
                                        <option value="vendor" {{ old('category') == 'vendor' ? 'selected' : '' }}>Vendor / Sales</option>
                                    </select>
                                    <i data-feather="chevron-down" class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500 pointer-events-none"></i>
                                </div>
                                @error('category') <p class="text-red-400 text-[10px] mt-2 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Business Sector</label>
                                <input type="text" name="business_category" placeholder="e.g. IT, Manufacturing" value="{{ old('business_category') }}" required
                                    class="w-full bg-slate-900/50 border border-white/10 rounded-xl md:rounded-2xl px-5 md:px-6 py-3.5 md:py-4 text-white text-sm md:text-base font-semibold transition-all placeholder:text-slate-400">
                                @error('business_category') <p class="text-red-400 text-[10px] mt-2 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Official Email</label>
                                <input type="email" name="email" value="{{ old('email', Auth::user()->email) }}" required
                                    class="w-full bg-slate-900/50 border border-white/10 rounded-xl md:rounded-2xl px-5 md:px-6 py-3.5 md:py-4 text-white text-sm md:text-base font-semibold transition-all placeholder:text-slate-400">
                                @error('email') <p class="text-red-400 text-[10px] mt-2 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Phone Number</label>
                                <input type="text" name="phone" placeholder="+62..." value="{{ old('phone') }}" required
                                    class="w-full bg-slate-900/50 border border-white/10 rounded-xl md:rounded-2xl px-5 md:px-6 py-3.5 md:py-4 text-white text-sm md:text-base font-semibold transition-all placeholder:text-slate-400">
                                @error('phone') <p class="text-red-400 text-[10px] mt-2 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="mt-10 md:mt-12 flex flex-col-reverse md:flex-row justify-between items-center gap-6">
                            <button type="button" class="text-slate-500 font-bold text-xs uppercase tracking-widest hover:text-white transition-colors btn-back" data-target="1">
                                <i data-feather="arrow-left" class="inline w-3 h-3 mr-2"></i> Go Back
                            </button>
                            <button type="button" class="w-full md:w-auto px-10 py-4 gradient-brand text-black rounded-xl md:rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-brand-gold/10 btn-next" data-target="3">
                                Next Step <i data-feather="arrow-right" class="inline w-4 h-4 ml-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Step 3: Mandatory Data Upload --}}
                    <div id="step-content-3" class="hidden step-transition">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-8 md:mb-10">
                            <div>
                                <h2 class="text-2xl md:text-3xl font-bold text-white mb-2 leading-tight" id="upload-title">Historical Records</h2>
                                <p class="text-slate-400 text-sm md:text-base font-medium" id="upload-subtitle">Mandatory upload to initialize your account.</p>
                            </div>
                            <a href="#" id="template-download-link" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-white/5 hover:bg-white/10 border border-white/10 rounded-2xl text-[10px] font-black text-brand-gold uppercase tracking-widest transition-all shrink-0 shadow-xl shadow-brand-gold/5">
                                <i data-feather="download" class="w-3.5 h-3.5"></i>
                                Download Template
                            </a>
                        </div>

                        <div class="space-y-8">
                            <div class="p-8 md:p-12 border-2 border-dashed border-white/10 rounded-[2rem] bg-slate-900/30 text-center group hover:border-brand-gold/30 transition-all cursor-pointer relative" id="drop-zone">
                                <input type="file" name="historical_data" id="file-input" class="absolute inset-0 opacity-0 cursor-pointer" accept=".xlsx,.xls,.csv" required>
                                <div class="w-20 h-20 gradient-brand rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-brand-gold/20 group-hover:scale-110 transition-transform">
                                    <i data-feather="upload-cloud" class="w-10 h-10 text-black"></i>
                                </div>
                                <p id="drop-text" class="text-lg font-bold text-white mb-2">Click or drag file here</p>
                                <p class="text-xs text-slate-500 uppercase tracking-widest font-black mb-6">Supported: .XLSX, .XLS, .CSV (Max 10MB)</p>
                                
                                <div class="flex flex-col items-center gap-4">
                                    <div id="file-info" class="hidden p-4 bg-white/5 rounded-2xl inline-flex items-center gap-3 border border-brand-gold/20">
                                        <i data-feather="file" class="w-4 h-4 text-brand-gold"></i>
                                        <span id="file-name" class="text-xs font-bold text-white truncate max-w-[200px]">filename.xlsx</span>
                                        <button type="button" id="remove-file" class="p-1 hover:text-red-400 transition-colors">
                                            <i data-feather="x" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 bg-blue-500/5 border border-blue-500/10 rounded-2xl flex gap-4">
                                <div class="w-10 h-10 rounded-xl bg-blue-500/10 flex items-center justify-center shrink-0">
                                    <i data-feather="info" class="w-5 h-5 text-blue-400"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-blue-400 uppercase tracking-widest mb-1">Onboarding Requirement</p>
                                    <p class="text-xs text-slate-400 leading-relaxed" id="upload-instruction">
                                        As a buyer, please upload your historical purchase order records. As a seller, please upload your product catalog. This ensures your dashboard is ready immediately.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 md:mt-12 flex flex-col-reverse md:flex-row justify-between items-center gap-6">
                            <button type="button" class="text-slate-500 font-bold text-xs uppercase tracking-widest hover:text-white transition-colors btn-back" data-target="2">
                                <i data-feather="arrow-left" class="inline w-3 h-3 mr-2"></i> Go Back
                            </button>
                            <button type="button" class="w-full md:w-auto px-10 py-4 gradient-brand text-black rounded-xl md:rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-brand-gold/10 btn-next" data-target="4">
                                Continue To Final Step <i data-feather="arrow-right" class="inline w-4 h-4 ml-2"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Step 4: Address & Detail --}}
                    <div id="step-content-4" class="hidden step-transition">
                        <h2 class="text-2xl md:text-3xl font-bold text-white mb-2 leading-tight">Location Details</h2>
                        <p class="text-slate-400 mb-8 md:mb-10 text-sm md:text-base font-medium">Complete your primary operational address.</p>

                        <div class="space-y-5 md:space-y-6">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Full Address</label>
                                <textarea name="address" rows="3" id="company_address_input" required placeholder="Enter complete address..."
                                    class="w-full bg-slate-900/50 border border-white/10 rounded-xl md:rounded-2xl px-5 md:px-6 py-3.5 md:py-4 text-white text-sm md:text-base font-semibold transition-all resize-none placeholder:text-slate-400">{{ old('address') }}</textarea>
                                @error('address') <p class="text-red-400 text-[10px] mt-2 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Short Description</label>
                                <textarea name="description" rows="2" placeholder="Brief overview of your company..."
                                    class="w-full bg-slate-900/50 border border-white/10 rounded-xl md:rounded-2xl px-5 md:px-6 py-3.5 md:py-4 text-white text-sm md:text-base font-semibold transition-all resize-none placeholder:text-slate-400">{{ old('description') }}</textarea>
                                @error('description') <p class="text-red-400 text-[10px] mt-2 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 md:gap-6">
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Country</label>
                                    <input type="text" name="country" value="Indonesia" readonly
                                        class="w-full bg-slate-900/30 border border-white/5 rounded-xl md:rounded-2xl px-5 md:px-6 py-3.5 md:py-4 text-slate-500 text-sm md:text-base font-semibold cursor-not-allowed">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-[0.2em] mb-2">Website (Optional)</label>
                                    <input type="text" name="website" placeholder="https://..." value="{{ old('website') }}"
                                        class="w-full bg-slate-900/50 border border-white/10 rounded-xl md:rounded-2xl px-5 md:px-6 py-3.5 md:py-4 text-white text-sm md:text-base font-semibold transition-all placeholder:text-slate-400">
                                    @error('website') <p class="text-red-400 text-[10px] mt-2 font-bold uppercase tracking-tight">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-10 md:mt-12 flex flex-col-reverse md:flex-row justify-between items-center gap-6">
                            <button type="button" class="text-slate-500 font-bold text-xs uppercase tracking-widest hover:text-white transition-colors btn-back" data-target="3">
                                <i data-feather="arrow-left" class="inline w-3 h-3 mr-2"></i> Go Back
                            </button>
                            <button type="submit" 
                                class="w-full md:w-auto px-10 py-4 gradient-brand text-black rounded-xl md:rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-xl shadow-brand-gold/10 hover:scale-[1.02] active:scale-[0.98]">
                                Complete Registration <i data-feather="check" class="inline w-4 h-4 ml-2"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="mt-auto pt-6 md:pt-8 border-t border-white/5 flex flex-col md:flex-row items-center justify-between gap-4">
                    <p class="text-[9px] font-bold text-slate-500 uppercase tracking-[0.1em] text-center md:text-left">© 2026 Huntr.id Indonesia. All rights reserved.</p>
                    <div class="flex gap-6">
                        <a href="#" class="text-[9px] font-bold text-slate-500 hover:text-white uppercase transition-colors">Privacy</a>
                        <a href="#" class="text-[9px] font-bold text-slate-500 hover:text-white uppercase transition-colors">Terms</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            feather.replace();

            const form = document.getElementById('onboardingForm');
            const npwpInput = document.getElementById('npwp_input');
            const btnValidateNpwp = document.getElementById('btn-validate-npwp');
            const btnToStep2 = document.getElementById('btn-to-step-2');
            const npwpLoading = document.getElementById('npwp_loading');
            const npwpError = document.getElementById('npwp_error');
            const npwpSuccessCard = document.getElementById('npwp_success_card');
            
            let currentStep = 1;

            // NPWP Masking
            npwpInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value.length > 16) value = value.slice(0, 16);
                
                let formatted = '';
                if (value.length > 0) formatted += value.slice(0, 2);
                if (value.length > 2) formatted += '.' + value.slice(2, 5);
                if (value.length > 5) formatted += '.' + value.slice(5, 8);
                if (value.length > 8) formatted += '.' + value.slice(8, 9);
                if (value.length > 9) formatted += '-' + value.slice(9, 12);
                if (value.length > 12) formatted += '.' + value.slice(12, 15);
                
                e.target.value = formatted;
            });

            // Handle NPWP Validation
            btnValidateNpwp.addEventListener('click', async function() {
                const npwp = npwpInput.value.replace(/[^0-9]/g, '');
                if (!npwp || npwp.length < 15) {
                    npwpError.textContent = 'Please enter a valid NPWP (15-16 digits).';
                    npwpError.classList.remove('hidden');
                    return;
                }

                btnValidateNpwp.disabled = true;
                npwpLoading.classList.remove('hidden');
                npwpError.classList.add('hidden');
                npwpSuccessCard.classList.add('hidden');

                try {
                    const response = await fetch('{{ route("onboarding.validate-npwp") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ npwp: npwp })
                    });

                    const result = await response.json();

                    if (result.success) {
                        document.getElementById('res_name').textContent = result.data.name;
                        document.getElementById('res_status').textContent = result.data.status;
                        document.getElementById('res_address').textContent = result.data.address;
                        
                        document.getElementById('company_name_input').value = result.data.name;
                        document.getElementById('company_address_input').value = result.data.address;
                        document.getElementById('company_npwp_hidden').value = npwp;

                        npwpSuccessCard.classList.remove('hidden');
                        btnValidateNpwp.classList.add('hidden');
                        btnToStep2.classList.remove('hidden');
                        npwpInput.readOnly = true;
                        npwpInput.classList.add('bg-emerald-500/5', 'border-emerald-500/30', 'text-emerald-500');
                    } else {
                        npwpError.textContent = result.message;
                        npwpError.classList.remove('hidden');
                    }
                } catch (error) {
                    npwpError.textContent = 'System error occurred. Please try again.';
                    npwpError.classList.remove('hidden');
                } finally {
                    btnValidateNpwp.disabled = false;
                    npwpLoading.classList.add('hidden');
                }
            });

            btnToStep2.addEventListener('click', () => goToStep(2));

            // Initialize Download Link based on default category (Buyer)
            const initialCategory = document.querySelector('select[name="category"]').value;
            const initialDownloadLink = document.getElementById('template-download-link');
            if (initialCategory === 'buyer') {
                initialDownloadLink.href = '{{ route("procurement.po.export-template") }}';
            } else {
                initialDownloadLink.href = '{{ route("catalogue.download-template") }}';
            }

            // Stepper Navigation
            document.querySelectorAll('.btn-next').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (validateCurrentStep()) {
                        goToStep(this.dataset.target);
                    }
                });
            });

            document.querySelectorAll('.btn-back').forEach(btn => {
                btn.addEventListener('click', function() {
                    goToStep(this.dataset.target);
                });
            });

            const fileInput = document.getElementById('file-input');
            const fileInfo = document.getElementById('file-info');
            const fileName = document.getElementById('file-name');
            const removeFile = document.getElementById('remove-file');
            const dropZone = document.getElementById('drop-zone');

            fileInput.addEventListener('change', function(e) {
                if (this.files && this.files[0]) {
                    fileName.textContent = this.files[0].name;
                    fileInfo.classList.remove('hidden');
                    document.getElementById('drop-text').textContent = 'File selected';
                    dropZone.classList.add('border-emerald-500/30', 'bg-emerald-500/5');
                }
            });

            removeFile.addEventListener('click', function(e) {
                e.stopPropagation();
                fileInput.value = '';
                fileInfo.classList.add('hidden');
                document.getElementById('drop-text').textContent = 'Click or drag file here';
                dropZone.classList.remove('border-emerald-500/30', 'bg-emerald-500/5');
            });

            function validateCurrentStep() {
                if (currentStep === 1) {
                    if (!document.getElementById('company_npwp_hidden').value) {
                        alert('Please validate your NPWP first.');
                        return false;
                    }
                }
                // Basic validation before going to next step
                if (currentStep === 2) {
                    const name = document.getElementById('company_name_input').value;
                    const business = document.querySelector('input[name="business_category"]').value;
                    const phone = document.querySelector('input[name="phone"]').value;
                    
                    if (!name || !business || !phone) {
                        alert('Please fill all required fields.');
                        return false;
                    }
                }
                if (currentStep === 3) {
                    if (!fileInput.files || !fileInput.files[0]) {
                        alert('The data upload is mandatory to proceed.');
                        return false;
                    }
                }
                return true;
            }

            function goToStep(step) {
                step = parseInt(step);

                // Update Upload Step Labels based on category
                if (step === 3) {
                    const category = document.querySelector('select[name="category"]').value;
                    const label = document.getElementById('step-label-3');
                    const title = document.getElementById('upload-title');
                    const subtitle = document.getElementById('upload-subtitle');
                    const instruction = document.getElementById('upload-instruction');
                    const downloadLink = document.getElementById('template-download-link');

                    if (category === 'buyer') {
                        label.textContent = 'PO Records';
                        title.textContent = 'Historical POs';
                        subtitle.textContent = 'Upload your previous purchase order records.';
                        instruction.textContent = 'As a buyer, uploading your historical POs allows us to pre-fill your procurement dashboard and suggest better vendor matches based on your volume.';
                        downloadLink.href = '{{ route("procurement.po.export-template") }}';
                    } else {
                        label.textContent = 'Catalog Upload';
                        title.textContent = 'Product Catalog';
                        subtitle.textContent = 'Upload your current product inventory.';
                        instruction.textContent = 'As a seller, your product catalog is essential. Once uploaded, buyers can find your products in the global marketplace immediately.';
                        downloadLink.href = '{{ route("catalogue.download-template") }}';
                    }
                }
                
                // Hide current content
                const currentEl = document.getElementById(`step-content-${currentStep}`);
                currentEl.classList.add('opacity-0');
                setTimeout(() => {
                    currentEl.classList.add('hidden');
                    const nextEl = document.getElementById(`step-content-${step}`);
                    nextEl.classList.remove('hidden');
                    setTimeout(() => nextEl.classList.remove('opacity-0'), 10);
                }, 300);
                
                // Update UI Circle
                const circles = document.querySelectorAll('[id^="step-circle-"]');
                circles.forEach((circle, index) => {
                    const circleStep = index + 1;
                    if (circleStep < step) {
                        circle.classList.remove('step-active', 'border-brand-gold', 'border-white/10');
                        circle.classList.add('border-emerald-500/50', 'text-emerald-500');
                        circle.innerHTML = '<i data-feather="check" class="w-4 h-4 md:w-5 md:h-5"></i>';
                    } else if (circleStep === step) {
                        circle.classList.remove('border-white/10', 'border-emerald-500/50', 'text-emerald-500');
                        circle.classList.add('step-active', 'border-brand-gold');
                        circle.innerHTML = circleStep;
                    } else {
                        circle.classList.remove('step-active', 'border-brand-gold', 'border-emerald-500/50', 'text-emerald-500');
                        circle.classList.add('border-white/10');
                        circle.innerHTML = circleStep;
                    }
                });
                feather.replace();

                // Update Nav Opacity
                for (let i = 1; i <= 4; i++) {
                    const nav = document.getElementById(`step-nav-${i}`);
                    if (nav) {
                        if (i === step) nav.classList.remove('opacity-40');
                        else nav.classList.add('opacity-40');
                    }
                }

                currentStep = step;
            }
            
            // Initial Active Circle
            document.getElementById('step-circle-1').classList.add('step-active', 'border-brand-gold');
        });
    </script>
</body>
</html>
