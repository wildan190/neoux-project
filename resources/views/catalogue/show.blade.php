@extends('layouts.app', [
    'title' => $item->name,
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Catalogue', 'url' => route('catalogue.index')],
        ['name' => $item->name, 'url' => '#']
    ]
])

@section('content')
<div class="max-w-7xl mx-auto">
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('catalogue.index') }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 flex items-center gap-2">
            <i data-feather="arrow-left" class="w-4 h-4"></i> Back to Catalogue
        </a>
        <div class="flex gap-2">
            <a href="{{ route('catalogue.edit', $item) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
                <i data-feather="edit-2" class="w-4 h-4"></i>
                Edit
            </a>
            <form action="{{ route('catalogue.destroy', $item) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this item?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 flex items-center gap-2">
                    <i data-feather="trash-2" class="w-4 h-4"></i>
                    Delete
                </button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Images Gallery --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                @if($item->images->count() > 0)
                    <div class="mb-4">
                        <img id="main-image" src="{{ asset('storage/' . ($item->primaryImage ? $item->primaryImage->image_path : $item->images->first()->image_path)) }}" alt="{{ $item->name }}" class="w-full h-96 object-contain rounded-lg bg-gray-50 dark:bg-gray-900">
                    </div>
                    @if($item->images->count() > 1)
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($item->images as $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $item->name }}" onclick="changeMainImage('{{ asset('storage/' . $image->image_path) }}')" class="w-full h-20 object-cover rounded-lg cursor-pointer hover:opacity-75 transition {{ $image->is_primary ? 'ring-2 ring-indigo-500' : '' }}">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="h-96 bg-gray-100 dark:bg-gray-900 rounded-lg flex items-center justify-center">
                        <i data-feather="image" class="w-24 h-24 text-gray-400"></i>
                    </div>
                @endif
            </div>

            {{-- Description --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-feather="info" class="w-5 h-5 text-indigo-500"></i>
                    Description
                </h3>
                <p class="text-gray-700 dark:text-gray-300 leading-relaxed">
                    {{ $item->description ?: 'No description provided.' }}
                </p>
            </div>

            {{-- Attributes --}}
            @if($item->attributes->count() > 0)
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                    <i data-feather="list" class="w-5 h-5 text-indigo-500"></i>
                    Attributes
                </h3>
                <div class="space-y-3">
                    @foreach($item->attributes as $attribute)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ $attribute->attribute_key }}</span>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $attribute->attribute_value }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        {{-- Product Info Sidebar --}}
        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $item->name }}</h1>
                
                @if($item->category)
                    <span class="inline-block px-3 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full text-sm font-semibold mb-4">
                        {{ $item->category->name }}
                    </span>
                @endif

                <div class="space-y-4 mt-4">
                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">SKU</p>
                        <p class="text-sm font-mono font-semibold text-gray-900 dark:text-white">{{ $item->sku }}</p>
                    </div>

                    @if($item->tags)
                        <div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Tags</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($item->tags_array as $tag)
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 text-xs rounded">{{ trim($tag) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400">Created</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $item->created_at->format('M d, Y') }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Last Updated</p>
                        <p class="text-sm text-gray-900 dark:text-white">{{ $item->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Quick Stats --}}
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">Quick Stats</h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Total Images</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->images->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Attributes</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->attributes->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Tags</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ count($item->tags_array) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changeMainImage(src) {
    document.getElementById('main-image').src = src;
}
</script>
@endsection
