@extends('layouts.app', [
    'title' => 'Add New Warehouse',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Warehouse Management', 'url' => route('procurement.warehouse.index')],
        ['name' => 'Add New', 'url' => '#']
    ]
])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-3xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="p-8 border-b border-gray-100 dark:border-gray-700">
            <h1 class="text-2xl font-black text-gray-900 dark:text-white uppercase tracking-tight">Add New Warehouse</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Create a new storage location for your company</p>
        </div>

        <form action="{{ route('procurement.warehouse.store') }}" method="POST" class="p-8">
            @csrf
            <div class="space-y-6">
                <div>
                    <label for="name" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Warehouse Name</label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                        class="w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-3 px-4">
                    @error('name')
                        <p class="mt-1 text-xs text-red-500 font-bold uppercase tracking-tight">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label for="code" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Warehouse Code</label>
                    <input type="text" name="code" id="code" required value="{{ old('code') }}" placeholder="e.g. WH-01"
                        class="w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-3 px-4">
                    @error('code')
                        <p class="mt-1 text-xs text-red-500 font-bold uppercase tracking-tight">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-400 italic">Code must be unique for your company.</p>
                </div>
                
                <div>
                    <label for="address" class="block text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest mb-1">Address</label>
                    <textarea name="address" id="address" rows="4"
                        class="w-full border-gray-200 dark:border-gray-700 dark:bg-gray-900 dark:text-white rounded-xl shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-3 px-4">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-xs text-red-500 font-bold uppercase tracking-tight">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-10 flex items-center justify-between border-t border-gray-50 dark:border-gray-700 pt-8">
                <a href="{{ route('procurement.warehouse.index') }}" 
                    class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-widest hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    Cancel & Go Back
                </a>
                <button type="submit" 
                    class="px-8 py-3 bg-primary-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition shadow-lg shadow-primary-500/30">
                    Create Warehouse
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
