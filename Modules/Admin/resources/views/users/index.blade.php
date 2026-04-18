@extends('layouts.app', [
    'title' => 'User Management',
    'breadcrumbs' => [
        ['name' => 'Management', 'url' => url('/')],
        ['name' => 'System Users', 'url' => null],
    ]
])

@section('content')
<div class="space-y-10">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">USER DIRECTORY</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Nodes: {{ $users->total() }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                System <span class="text-primary-600">Access Control</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.create') }}" class="h-16 px-10 flex items-center bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-[0.98]">
                Provision New User
            </a>
        </div>
    </div>

    {{-- User Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800">Identity</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800">Permissions/Roles</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800">Registration</th>
                        <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800 text-right">Operations</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @forelse($users as $user)
                        <tr class="group hover:bg-gray-50/30 dark:hover:bg-gray-900/30 transition-colors">
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-5">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center relative overflow-hidden shadow-inner group-hover:scale-105 transition-transform duration-500">
                                        @if($user->userDetail && $user->userDetail->profile_photo)
                                            <img src="{{ asset('storage/' . $user->userDetail->profile_photo) }}" class="w-full h-full object-cover">
                                        @else
                                            <span class="text-xl font-black text-gray-300">{{ substr($user->email, 0, 1) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-2 group-hover:text-primary-600 transition-colors">
                                            {{ $user->name ?: 'UNREGISTERED PROFILE' }}
                                        </p>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        // Simplified roles display for Admin view
                                        $companiesCount = $user->companies->count();
                                    @endphp
                                    <span class="px-3 py-1 bg-gray-100 dark:bg-gray-900 text-gray-500 dark:text-gray-400 text-[9px] font-black rounded-lg uppercase tracking-widest">
                                        {{ $companiesCount }} Companies
                                    </span>
                                    @if($user->is_super_admin)
                                        <span class="px-3 py-1 bg-red-100 text-red-600 text-[9px] font-black rounded-lg uppercase tracking-widest">Super Admin</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-10 py-8 text-xs font-bold text-gray-400 uppercase tracking-widest">
                                {{ $user->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-10 py-8 text-right">
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to terminate this user access? This cannot be undone.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-10 py-24 text-center">
                                <div class="w-20 h-20 bg-gray-50 dark:bg-gray-900 rounded-[1.5rem] flex items-center justify-center mx-auto mb-6 text-gray-200 shadow-inner">
                                    <i data-feather="users" class="w-10 h-10"></i>
                                </div>
                                <p class="text-[11px] font-black text-gray-300 uppercase tracking-[0.2em]">Zero system users detected</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="px-10 py-8 border-t border-gray-50 dark:border-gray-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endpush
