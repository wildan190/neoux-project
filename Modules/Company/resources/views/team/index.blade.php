@extends('layouts.app', [
    'title' => 'Team Network: ' . $company->name,
    'breadcrumbs' => [
        ['name' => 'Workspace', 'url' => route('company.dashboard')],
        ['name' => 'Team Nodes', 'url' => null],
    ]
])

@section('content')
<div class="space-y-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <span class="px-3 py-1 bg-gray-900 text-white rounded-lg text-[10px] font-black uppercase tracking-widest">ACCESS CONTROL</span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Global Nodes: {{ $members->total() }}</span>
            </div>
            <h1 class="text-4xl font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none">
                Workspace <span class="text-primary-600">Permissions</span>
            </h1>
        </div>
        
        <div class="flex items-center gap-4">
            <button onclick="document.getElementById('invite_modal').classList.remove('hidden')" class="h-16 px-10 flex items-center bg-primary-600 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-2xl shadow-primary-600/20 hover:bg-primary-700 transition-all active:scale-[0.98]">
                Authorize New Node
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
        {{-- Team Table --}}
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white dark:bg-gray-800 rounded-[3rem] border border-gray-100 dark:border-gray-800 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50/50 dark:bg-gray-900/50">
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800">Identity</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800">Assigned Role</th>
                                <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] border-b border-gray-100 dark:border-gray-800 text-right">Operations</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                            @foreach($members as $member)
                                <tr class="group hover:bg-gray-50/30 dark:hover:bg-gray-900/30 transition-colors">
                                    <td class="px-10 py-8">
                                        <div class="flex items-center gap-5">
                                            <div class="w-14 h-14 rounded-2xl bg-gray-50 dark:bg-gray-900 flex items-center justify-center relative overflow-hidden shadow-inner group-hover:scale-105 transition-transform duration-500">
                                                @if($member->userDetail && $member->userDetail->profile_photo)
                                                    <img src="{{ asset('storage/' . $member->userDetail->profile_photo) }}" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-xl font-black text-gray-300">{{ substr($member->name ?: $member->email, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-gray-900 dark:text-white uppercase tracking-tight leading-none mb-2">{{ $member->name }}</p>
                                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">{{ $member->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-10 py-8">
                                        <form action="{{ route('team.update-role', $member->id) }}" method="POST">
                                            @csrf
                                            <select name="role" onchange="this.form.submit()" class="bg-gray-50 dark:bg-gray-900 border-none rounded-xl px-4 py-2 text-[10px] font-black text-gray-600 dark:text-gray-400 uppercase tracking-widest focus:ring-primary-500 shadow-inner">
                                                <option value="admin" {{ $member->pivot->role === 'admin' ? 'selected' : '' }}>ADMIN</option>
                                                <option value="manager" {{ $member->pivot->role === 'manager' ? 'selected' : '' }}>MANAGER</option>
                                                <option value="purchasing_manager" {{ $member->pivot->role === 'purchasing_manager' ? 'selected' : '' }}>PURCHASING</option>
                                                <option value="finance" {{ $member->pivot->role === 'finance' ? 'selected' : '' }}>FINANCE</option>
                                                <option value="staff" {{ $member->pivot->role === 'staff' ? 'selected' : '' }}>STAFF</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="px-10 py-8 text-right">
                                        @if($member->id !== auth()->id() && $member->id !== $company->user_id)
                                            <form action="{{ route('team.remove', $member->id) }}" method="POST" onsubmit="return confirm('WARNING: Detach this node from workspace?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                                    <i data-feather="user-x" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-[9px] font-black text-gray-300 uppercase tracking-widest">Protected Node</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            @if($members->hasPages())
                <div class="pt-6">
                    {{ $members->links() }}
                </div>
            @endif
        </div>

        {{-- Pending Invitations --}}
        <div class="space-y-8">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">Pending Authorizations</h3>
            <div class="space-y-4">
                @forelse($invitations as $invitation)
                    <div class="bg-white dark:bg-gray-800 rounded-[2.5rem] p-8 border border-gray-100 dark:border-gray-800 shadow-sm relative overflow-hidden group">
                        <div class="absolute top-0 right-0 -mr-8 -mt-8 w-24 h-24 bg-yellow-100/30 rounded-full blur-2xl"></div>
                        <div class="relative z-10">
                            <p class="text-[10px] font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2 truncate">{{ $invitation->email }}</p>
                            <div class="flex items-center justify-between">
                                <span class="px-2 py-1 bg-gray-100 dark:bg-gray-900 text-gray-500 text-[8px] font-black rounded uppercase tracking-widest">{{ $invitation->role }}</span>
                                <span class="text-[9px] font-bold text-yellow-600 uppercase tracking-widest animate-pulse">Awaiting Join</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-gray-50/50 dark:bg-gray-900/10 rounded-[2.5rem] p-12 text-center border border-dashed border-gray-100 dark:border-gray-800">
                        <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest">Zero pending invites</p>
                    </div>
                @endforelse
            </div>

            {{-- Permission Guide --}}
            <div class="bg-gray-900 rounded-[2.5rem] p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute bottom-0 right-0 -mb-8 -mr-8 w-24 h-24 bg-primary-600/20 rounded-full blur-2xl"></div>
                <h4 class="text-[10px] font-black text-primary-500 uppercase tracking-widest mb-6">Permission Hierarchy</h4>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-500"></div>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-gray-400">ADMIN: Full System Authority</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-gray-400">FINANCE: Fiscal Settlement</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                        <p class="text-[9px] font-bold uppercase tracking-widest text-gray-400">STAFF: Operational Execution</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Invitation Modal --}}
<div id="invite_modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6 bg-gray-900/80 backdrop-blur-sm">
    <div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-[3.5rem] p-12 shadow-2xl relative overflow-hidden">
        <div class="absolute top-0 right-0 p-12 text-gray-50/50 dark:text-gray-900/50 pointer-events-none">
            <i data-feather="user-plus" class="w-24 h-24"></i>
        </div>
        
        <h3 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight mb-2 relative z-10">Invite Associate</h3>
        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-10 relative z-10">Authorize New Workspace Node</p>

        <form action="{{ route('team.invite') }}" method="POST" class="space-y-8 relative z-10">
            @csrf
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Target Email Identifier</label>
                <input type="email" name="email" required class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl p-5 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 shadow-inner">
            </div>
            <div>
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Authorization Level</label>
                <select name="role" class="w-full bg-gray-50 dark:bg-gray-900 border-none rounded-2xl p-5 text-[11px] font-black uppercase tracking-tight focus:ring-primary-500 shadow-inner">
                    <option value="admin">ADMIN (FULL ACCESS)</option>
                    <option value="manager">MANAGER (OPERATIONS)</option>
                    <option value="purchasing_manager">PURCHASING MANAGER</option>
                    <option value="finance">FINANCE / ACCOUNTING</option>
                    <option value="staff">STAFF / OPERATIONS</option>
                </select>
            </div>
            <div class="flex items-center gap-4 pt-4">
                <button type="button" onclick="document.getElementById('invite_modal').classList.add('hidden')" class="h-16 px-10 text-[11px] font-black text-gray-400 uppercase tracking-widest">Discard</button>
                <button type="submit" class="h-16 flex-1 bg-gray-900 text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-gray-900/20 hover:bg-black transition-all">Transmit Invite</button>
            </div>
        </form>
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
