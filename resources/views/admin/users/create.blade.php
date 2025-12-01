@extends('admin.layouts.app', ['title' => 'Create User'])

@section('content')
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}" class="text-indigo-600 hover:text-indigo-900">
            <i data-feather="arrow-left" class="w-4 h-4 inline"></i> Back to Users
        </a>
    </div>

    <div class="max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow p-6">
        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-6">Create New User</h2>

        @if ($errors->any())
            <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <p class="mt-1 text-sm text-gray-500">User will receive this email for login</p>
            </div>

            <div class="mb-6">
                <label for="password"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <p class="mt-1 text-sm text-gray-500">Minimum 8 characters</p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('admin.users.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    Create User
                </button>
            </div>
        </form>
    </div>
@endsection