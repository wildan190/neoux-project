@extends('layouts.app', [
    'title' => 'Edit Catalogue Item',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Catalogue', 'url' => route('catalogue.index')],
        ['name' => 'Edit Item', 'url' => '#']
    ]
])

@section('content')
<div class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden">
    <div class="p-8">
        <h2 class="text-2xl font-bold mb-6 text-gray-900 dark:text-white">Edit Catalogue Item</h2>
        
        <form action="{{ route('catalogue.update', $item) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Category --}}
                <div>
                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Category</label>
                    <select name="category_id" id="category_id" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- SKU --}}
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700 dark:text-gray-300">SKU</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $item->sku) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Unique product identifier</p>
                </div>

                {{-- Name --}}
                <div class="md:col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Product Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
                    <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('description', $item->description) }}</textarea>
                </div>

                {{-- Tags --}}
                <div class="md:col-span-2">
                    <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tags</label>
                    <input type="text" name="tags" id="tags" value="{{ old('tags', $item->tags) }}" placeholder="e.g. electronics, smartphone, 5G" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Comma-separated tags</p>
                </div>
            </div>

            {{-- Dynamic Attributes --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Attributes</label>
                    <button type="button" onclick="addAttribute()" class="text-sm text-indigo-600 hover:text-indigo-500 font-medium">+ Add Attribute</button>
                </div>
                <div id="attributes-container" class="space-y-3">
                    @if($item->attributes && $item->attributes->count() > 0)
                        @foreach($item->attributes as $index => $attribute)
                            <div class="grid grid-cols-2 gap-3 items-center">
                                <input type="text" name="attributes[{{ $index }}][key]" value="{{ old('attributes.'.$index.'.key', $attribute->attribute_key) }}" placeholder="e.g. Size, Color, Material" class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <div class="flex gap-2">
                                    <input type="text" name="attributes[{{ $index }}][value]" value="{{ old('attributes.'.$index.'.value', $attribute->attribute_value) }}" placeholder="e.g. XL, Red, Cotton" class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <button type="button" onclick="this.closest('.grid').remove()" class="text-red-500 hover:text-red-700 p-2">
                                        <i data-feather="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="attributes[0][key]" placeholder="e.g. Size, Color, Material" class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <input type="text" name="attributes[0][value]" placeholder="e.g. XL, Red, Cotton" class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    @endif
                </div>
            </div>

            {{-- Existing Images --}}
            @if($item->images->count() > 0)
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Current Images</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="existing-images">
                    @foreach($item->images as $image)
                        <div class="relative group" data-image-id="{{ $image->id }}">
                            <img src="{{ asset('storage/' . $image->image_path) }}" class="w-full h-32 object-cover rounded-lg border-2 {{ $image->is_primary ? 'border-indigo-500' : 'border-gray-200' }}">
                            <button type="button" onclick="deleteExistingImage({{ $image->id }})" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                                <i data-feather="trash-2" class="w-4 h-4"></i>
                            </button>
                            @if($image->is_primary)
                                <span class="absolute bottom-2 left-2 bg-indigo-500 text-white text-xs px-2 py-1 rounded">★ Primary</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div id="deleted-images"></div>
            </div>
            @endif

            {{-- Add New Images --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Add New Images (Max 1MB each)</label>
                <div id="drop-zone" class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-8 text-center hover:border-indigo-500 transition-colors cursor-pointer bg-gray-50 dark:bg-gray-700/50">
                    <input type="file" name="images[]" id="images" multiple accept="image/*" class="hidden">
                    <i data-feather="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                    <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">Click to upload or drag and drop</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">JPG, PNG, GIF up to 1MB</p>
                </div>
                <div id="image-preview" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4"></div>
                <input type="hidden" name="primary_image_index" id="primary_image_index" value="0">
            </div>

            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 flex justify-end gap-3">
                <a href="{{ route('catalogue.show', $item) }}" class="px-6 py-2.5 border border-gray-300 text-gray-700 dark:text-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2.5 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 font-medium shadow-lg shadow-indigo-500/30 transition-all">
                    Update Item
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let attributeIndex = {{ $item->attributes->count() }};
let imageFiles = new DataTransfer();
let primaryImageIndex = 0;
let deletedImageIds = [];

// Initialize feather icons
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});

// Add attribute row
function addAttribute() {
    const container = document.getElementById('attributes-container');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-2 gap-3 items-center';
    div.innerHTML = `
        <input type="text" name="attributes[${attributeIndex}][key]" placeholder="e.g. Size, Color, Material" class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        <div class="flex gap-2">
            <input type="text" name="attributes[${attributeIndex}][value]" placeholder="e.g. XL, Red, Cotton" class="block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <button type="button" onclick="this.closest('.grid').remove()" class="text-red-500 hover:text-red-700 p-2">
                <i data-feather="trash-2" class="w-5 h-5"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    attributeIndex++;
    feather.replace();
}

// Delete existing image
function deleteExistingImage(imageId) {
    if (confirm('Delete this image?')) {
        deletedImageIds.push(imageId);
        const imageElement = document.querySelector(`[data-image-id="${imageId}"]`);
        imageElement.remove();
        
        // Add hidden input for deleted image ID
        const container = document.getElementById('deleted-images');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'deleted_images[]';
        input.value = imageId;
        container.appendChild(input);
    }
}

// Drag & Drop for new images
const dropZone = document.getElementById('drop-zone');
const fileInput = document.getElementById('images');
const imagePreview = document.getElementById('image-preview');

dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('border-indigo-500', 'bg-indigo-50', 'dark:bg-indigo-900/20');
    handleFiles(e.dataTransfer.files);
});

fileInput.addEventListener('change', function() {
    handleFiles(this.files);
});

function handleFiles(files) {
    Array.from(files).forEach(file => {
        if (file.size > 1024 * 1024) {
            alert(`${file.name} exceeds 1MB`);
            return;
        }
        if (!file.type.startsWith('image/')) {
            alert(`${file.name} is not an image`);
            return;
        }
        imageFiles.items.add(file);
    });
    
    fileInput.files = imageFiles.files;
    renderImagePreviews();
}

function renderImagePreviews() {
    imagePreview.innerHTML = '';
    Array.from(imageFiles.files).forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'relative group';
            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-32 object-cover rounded-lg border-2 border-gray-200">
                <button type="button" onclick="removeNewImage(${index})" class="absolute top-2 right-2 bg-red-500 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition">
                    <i data-feather="x" class="w-4 h-4"></i>
                </button>
                <span class="absolute bottom-2 left-2 bg-green-500 text-white text-xs px-2 py-1 rounded">New</span>
            `;
            imagePreview.appendChild(div);
            feather.replace();
        };
        reader.readAsDataURL(file);
    });
}

function removeNewImage(index) {
    imageFiles.items.remove(index);
    fileInput.files = imageFiles.files;
    renderImagePreviews();
}
</script>
@endsection
