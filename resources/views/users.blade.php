@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100">Users</h2>
        <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">Add User</button>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">ID</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Name</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Email</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Role</th>
                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-600 dark:text-gray-300">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <td class="px-4 py-2">1</td>
                    <td class="px-4 py-2">John Doe</td>
                    <td class="px-4 py-2">john@example.com</td>
                    <td class="px-4 py-2">Admin</td>
                    <td class="px-4 py-2 space-x-2">
                        <button class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded">Edit</button>
                        <button class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded">Delete</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                    <td class="px-4 py-2">2</td>
                    <td class="px-4 py-2">Jane Smith</td>
                    <td class="px-4 py-2">jane@example.com</td>
                    <td class="px-4 py-2">User</td>
                    <td class="px-4 py-2 space-x-2">
                        <button class="px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white rounded">Edit</button>
                        <button class="px-2 py-1 bg-red-500 hover:bg-red-600 text-white rounded">Delete</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
