@extends('layouts.app', [
    'title' => 'Add New Product',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Catalogue', 'url' => route('catalogue.index')],
        ['name' => 'Add Product', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-[1600px] mx-auto">
    <div class="lg:grid lg:grid-cols-12 gap-8 items-start">
        
        {{-- Left Column: Guidance --}}
        <div class="hidden lg:block lg:col-span-3 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-primary-100 dark:bg-primary-900/20 flex items-center justify-center text-primary-600 mb-6">
                    <i data-feather="info" class="w-6 h-6"></i>
                </div>
                <h3 class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight mb-4">Product Creation Guide</h3>
                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center shrink-0 text-[10px] font-black">1</div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Select a relevant category to help buyers find your product easily.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center shrink-0 text-[10px] font-black">2</div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Use high-quality images. Products with multiple photos get 80% more interest.</p>
                    </div>
                    <div class="flex gap-4">
                        <div class="w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-900 flex items-center justify-center shrink-0 text-[10px] font-black">3</div>
                        <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">Ensure SKU codes are unique within your organization for inventory tracking.</p>
                    </div>
                </div>
            </div>

            <div class="bg-primary-600 rounded-[2.5rem] p-8 text-white relative overflow-hidden group">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-700"></div>
                <h4 class="text-sm font-black uppercase tracking-widest mb-2 relative z-10">Pro Tip</h4>
                <p class="text-xs text-primary-100 leading-relaxed relative z-10">Adding specific technical specifications in the description helps reduce negotiation time.</p>
            </div>
        </div>

        {{-- Middle Column: The Form --}}
        <div class="lg:col-span-6">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-xl overflow-hidden">
                <div class="p-8 md:p-12">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="w-10 h-10 rounded-xl bg-gray-900 dark:bg-gray-700 flex items-center justify-center shadow-lg">
                            <i data-feather="plus" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Create Product</h2>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Digital Asset Management</p>
                        </div>
                    </div>

                    <form action="{{ route('catalogue.store') }}" method="POST" enctype="multipart/form-data" class="space-y-10">
                        @csrf
                        
                        {{-- General Info Section --}}
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 mb-6">
                                <span class="w-8 h-px bg-gray-100 dark:bg-gray-800"></span>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">General Information</span>
                                <span class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="category_id" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Category</label>
                                    <select name="category_id" id="category_id" 
                                        class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5" 
                                        required>
                                        <option value="">-- Select Category --</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('category_id') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="name" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Product Name</label>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Safety Boots Model X" 
                                        class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5" 
                                        required>
                                    @error('name') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="brand" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Brand Name</label>
                                    <input type="text" name="brand" id="brand" value="{{ old('brand') }}" placeholder="e.g. Caterpillar" 
                                        class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5">
                                    @error('brand') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="description" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Description</label>
                                    <textarea name="description" id="description" rows="4" placeholder="Describe your product in detail..."
                                        class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5">{{ old('description') }}</textarea>
                                    @error('description') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Variant Info Section --}}
                        <div class="space-y-6">
                            <div class="flex items-center gap-3 mb-6">
                                <span class="w-8 h-px bg-gray-100 dark:bg-gray-800"></span>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Inventory & Pricing</span>
                                <span class="flex-1 h-px bg-gray-100 dark:bg-gray-800"></span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="sku" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">SKU Reference</label>
                                    <div class="flex gap-3">
                                        <input type="text" name="sku" id="sku" value="{{ old('sku') }}" placeholder="ABC-123-XYZ"
                                            class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5" 
                                            required>
                                        <button type="button" onclick="generateSku()" 
                                            class="px-6 py-3.5 bg-gray-900 text-white rounded-2xl hover:bg-gray-800 dark:bg-gray-700 dark:hover:bg-gray-600 transition-all font-black text-[10px] uppercase tracking-widest shrink-0 shadow-lg shadow-black/10">
                                            Auto-Gen
                                        </button>
                                    </div>
                                    @error('sku') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="unit" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Unit Type</label>
                                    <input type="text" name="unit" id="unit" value="{{ old('unit', 'Pcs') }}" placeholder="e.g. Box, Kg, Pcs"
                                        class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5" 
                                        required>
                                    @error('unit') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="stock" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Initial Stock</label>
                                    <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0" 
                                        class="block w-full rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5" 
                                        required>
                                    @error('stock') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="price" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Base Price (IDR)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                            <span class="text-xs font-black text-gray-400">Rp</span>
                                        </div>
                                        <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" placeholder="0.00"
                                            class="block w-full pl-12 rounded-2xl border-gray-100 dark:border-gray-800 bg-gray-50/50 dark:bg-gray-900/50 shadow-inner focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-5 py-3.5" 
                                            required>
                                    </div>
                                    @error('price') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="images" class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Media Assets</label>
                                    <div class="group relative">
                                        <input type="file" name="images[]" id="images" multiple accept="image/*" 
                                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-3.5 file:px-8 file:rounded-2xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-gray-900 file:text-white hover:file:bg-primary-600 file:transition-all cursor-pointer bg-gray-50/50 dark:bg-gray-900/50 border border-dashed border-gray-200 dark:border-gray-700 rounded-2xl p-2">
                                    </div>
                                    <p class="mt-2 text-[9px] font-bold text-gray-400 uppercase tracking-widest ml-1">Max 5 images. Recommended: 800x800px (JPG/PNG)</p>
                                     @error('images') <span class="text-red-500 text-[10px] font-bold mt-1 block ml-1 uppercase tracking-wider">{{ $message }}</span> @enderror
                                </div>
                             </div>
                        </div>

                        <div class="pt-10 flex items-center justify-between gap-6 border-t border-gray-100 dark:border-gray-800">
                            <a href="{{ route('catalogue.index') }}" class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] hover:text-gray-900 dark:hover:text-white transition-colors">
                                Back to Catalogue
                            </a>
                            <div class="flex gap-4">
                                <button type="submit" class="bg-primary-600 text-white px-10 py-4 rounded-2xl hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 font-black text-[10px] uppercase tracking-[0.2em] shadow-xl shadow-primary-500/30 transition-all hover:-translate-y-1">
                                    Create Asset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right Column: Preview & Summary --}}
        <div class="hidden lg:block lg:col-span-3 space-y-6">
            <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden sticky top-8">
                <div class="p-8">
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Real-time Preview</h3>
                    
                    <div id="product-preview" class="space-y-6">
                        <div class="aspect-square bg-gray-50 dark:bg-gray-900 rounded-[2rem] flex items-center justify-center border-2 border-dashed border-gray-100 dark:border-gray-800">
                            <i data-feather="image" class="w-12 h-12 text-gray-200"></i>
                        </div>
                        
                        <div>
                            <p id="preview-category" class="text-[8px] font-black text-primary-600 uppercase tracking-widest mb-1">UNSELECTED CATEGORY</p>
                            <h4 id="preview-name" class="text-lg font-black text-gray-900 dark:text-white uppercase tracking-tight line-clamp-2">New Product Title</h4>
                            <p id="preview-brand" class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Generic Brand</p>
                        </div>

                        <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Base Price</span>
                                <span id="preview-price" class="text-sm font-black text-gray-900 dark:text-white uppercase">Rp 0.00</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Stock Level</span>
                                <span id="preview-stock" class="text-xs font-black text-emerald-500 uppercase tracking-widest">0 Units</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function generateSku() {
        const catId = document.getElementById('category_id').value;
        if(!catId) {
            alert('Please select a category first');
            return;
        }

        fetch('{{ route("catalogue.generate-sku") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ category_id: catId })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('sku').value = data.sku;
        });
    }

    // Dynamic Preview Logic
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.getElementById('name');
        const brandInput = document.getElementById('brand');
        const priceInput = document.getElementById('price');
        const stockInput = document.getElementById('stock');
        const catSelect = document.getElementById('category_id');

        function updatePreview() {
            document.getElementById('preview-name').innerText = nameInput.value || 'New Product Title';
            document.getElementById('preview-brand').innerText = brandInput.value || 'Generic Brand';
            
            const price = parseFloat(priceInput.value) || 0;
            document.getElementById('preview-price').innerText = 'Rp ' + price.toLocaleString('id-ID');
            
            document.getElementById('preview-stock').innerText = (stockInput.value || 0) + ' Units';
            
            const selectedCat = catSelect.options[catSelect.selectedIndex].text;
            document.getElementById('preview-category').innerText = catSelect.value ? selectedCat : 'UNSELECTED CATEGORY';
        }

        [nameInput, brandInput, priceInput, stockInput, catSelect].forEach(input => {
            input.addEventListener('input', updatePreview);
        });

        feather.replace();
    });
</script>
@endsection
