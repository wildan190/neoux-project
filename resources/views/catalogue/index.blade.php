@extends('layouts.app', [
    'title' => 'Catalogue',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Catalogue', 'url' => '#']
    ]
])

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Product Catalogue</h2>
        <p class="text-gray-600 dark:text-gray-400">Manage your product catalog</p>
    </div>
    <a href="{{ route('catalogue.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center gap-2">
        <i data-feather="plus" class="w-4 h-4"></i>
        Add Product
    </a>
</div>

{{-- Filters --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 mb-6">
    <form method="GET" class="flex gap-4">
        <div class="flex-1">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or SKU..." class="w-full rounded-lg border border-gray-300 px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
        </div>
        <select name="category" class="rounded-lg border border-gray-300 px-3 py-2 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">Filter</button>
    </form>
</div>

{{-- Data Table View --}}
<div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">#</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">SKU</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Tags</th>
                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $loop->iteration + ($items->currentPage() - 1) * $items->perPage() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-700 overflow-hidden">
                                @if($item->primaryImage)
                                    <img src="{{ asset('storage/' . $item->primaryImage->image_path) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="flex items-center justify-center h-full">
                                        <i data-feather="image" class="w-6 h-6 text-gray-400"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $item->sku }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $item->description }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($item->category)
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200">
                                    {{ $item->category->name }}
                                </span>
                            @else
                                <span class="text-sm text-gray-500">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-wrap gap-1">
                                @if($item->tags)
                                    @foreach(array_slice($item->tags_array, 0, 2) as $tag)
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded">{{ trim($tag) }}</span>
                                    @endforeach
                                    @if(count($item->tags_array) > 2)
                                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 text-xs rounded">+{{ count($item->tags_array) - 2 }}</span>
                                    @endif
                                @else
                                    <span class="text-sm text-gray-500">-</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('catalogue.show', $item) }}" class="text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400" title="View">
                                    <i data-feather="eye" class="w-5 h-5"></i>
                                </a>
                                <a href="{{ route('catalogue.edit', $item) }}" class="text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400" title="Edit">
                                    <i data-feather="edit-2" class="w-5 h-5"></i>
                                </a>
                                <form action="{{ route('catalogue.destroy', $item) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this item?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400" title="Delete">
                                        <i data-feather="trash-2" class="w-5 h-5"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center">
                            <i data-feather="inbox" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                            <p class="text-gray-500 dark:text-gray-400">No products found.</p>
                            <a href="{{ route('catalogue.create') }}" class="mt-2 inline-block text-indigo-600 hover:text-indigo-500 font-medium">
                                Add your first product
                            </a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
@if($items->hasPages())
    <div class="mt-6">
        {{ $items->links() }}
    </div>
@endif
@endsection
