@extends('layouts.app', [
    'title' => 'Add New Product',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Catalogue', 'url' => route('catalogue.index')],
        ['name' => 'Add Product', 'url' => '#']
    ]
])

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden max-w-4xl mx-auto">
    <div class="p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Create New Product</h2>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Create a product and its initial variant (SKU).</p>
        
        <form action="{{ route('catalogue.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            
            {{-- General Info Section --}}
            <div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">General Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label for="category_id" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Category</label>
                        <select name="category_id" id="category_id" 
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-4 py-2.5" 
                            required>
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="name" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Product Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="e.g. Safety Boots Model X" 
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-4 py-2.5" 
                            required>
                        @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="brand" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Brand</label>
                        <input type="text" name="brand" id="brand" value="{{ old('brand') }}" placeholder="e.g. Caterpillar" 
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-4 py-2.5">
                        @error('brand') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Description</label>
                        <textarea name="description" id="description" rows="3" 
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-4 py-2.5">{{ old('description') }}</textarea>
                        @error('description') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            {{-- Variant Info Section --}}
            <div>
                 <h3 class="text-lg font-medium text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2 mb-4">Initial Variant (SKU) Details</h3>
                 <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="sku" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">SKU Code</label>
                        <div class="flex gap-2">
                            <input type="text" name="sku" id="sku" value="{{ old('sku') }}" 
                                class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-4 py-2.5" 
                                required>
                            <button type="button" onclick="generateSku()" 
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-all font-bold text-xs uppercase tracking-tight">
                                Generate
                            </button>
                        </div>
                        @error('sku') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="unit" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Unit</label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit', 'Pcs') }}" 
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-4 py-2.5" 
                            required>
                        @error('unit') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="price" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Price (IDR)</label>
                        <input type="number" name="price" id="price" value="{{ old('price') }}" min="0" 
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-4 py-2.5" 
                            required>
                        @error('price') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label for="stock" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Initial Stock</label>
                        <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0" 
                            class="block w-full rounded-xl border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:text-white transition-all text-sm px-4 py-2.5" 
                            required>
                        @error('stock') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="images" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Product Images</label>
                        <input type="file" name="images[]" id="images" multiple accept="image/*" 
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-xs file:font-bold file:uppercase file:tracking-wider file:bg-primary-600 file:text-white hover:file:bg-primary-700 file:transition-all cursor-pointer">
                         @error('images') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                 </div>
            </div>

            <div class="pt-6 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <a href="{{ route('catalogue.index') }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 dark:text-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit" class="bg-primary-600 text-white px-6 py-2.5 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 font-medium shadow-lg shadow-primary-500/30 transition-all">
                    Create Product
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function generateSku() {
        fetch('{{ route("catalogue.generate-sku") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ category_id: document.getElementById('category_id').value })
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('sku').value = data.sku;
        });
    }
</script>
@endsection
