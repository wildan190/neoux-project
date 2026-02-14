@extends('layouts.app', [
    'title' => 'Team Settings',
    'breadcrumbs' => [
        ['name' => 'Home', 'url' => route('dashboard')],
        ['name' => 'Team Settings', 'url' => '#']
    ]
])

@section('content')
@php
    $companyId = session('selected_company_id');
    $isAdminOrManager = auth()->user()->hasCompanyRole($companyId, ['admin', 'manager']);
    $canInvite = auth()->user()->hasCompanyRole($companyId, ['admin', 'manager', 'staff']);
@endphp
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Team Management</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">Manage members and roles for {{ $company->name }}</p>
        </div>
        @if($canInvite)
        <div>
            <button onclick="document.getElementById('inviteModal').classList.remove('hidden')" 
                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center gap-2">
                <i data-feather="user-plus" class="w-4 h-4"></i>
                Invite Member
            </button>
        </div>
        @endif
    </div>

    <!-- Role Guide -->
    <div x-data="{ expanded: true }" class="bg-gradient-to-br from-primary-900 to-primary-800 rounded-xl shadow-lg shadow-primary-900/20 overflow-hidden text-white mb-6">
        <div class="px-6 py-4 flex justify-between items-center cursor-pointer hover:bg-white/5 transition-colors group" @click="expanded = !expanded">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-white/10 rounded-lg group-hover:bg-white/20 transition-colors">
                    <i data-feather="shield" class="w-5 h-5 text-primary-200 group-hover:text-white transition-colors"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg">Role Capabilities Guide</h3>
                    <p class="text-primary-200 text-xs group-hover:text-primary-100 transition-colors">Learn what each role can do in your company</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-primary-200 group-hover:text-white transition-colors uppercase tracking-wider" x-text="expanded ? 'Hide' : 'Show'"></span>
                <i data-feather="chevron-down" class="w-5 h-5 transition-transform duration-300 group-hover:text-white" :class="{ 'rotate-180': expanded }"></i>
            </div>
        </div>
        
        <div x-show="expanded" x-transition class="border-t border-white/10">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6">
                <!-- Admin -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-primary-200 font-bold uppercase tracking-wider text-xs">
                        <span class="w-2 h-2 rounded-full bg-red-400"></span> Admin
                    </div>
                    <ul class="text-sm space-y-1 text-primary-100/80 list-disc list-inside">
                        <li>Full access to all modules</li>
                        <li>Manage team members & roles</li>
                        <li>Edit company settings</li>
                        <li>Approve payments & requests</li>
                    </ul>
                </div>

                <!-- Manager -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-primary-200 font-bold uppercase tracking-wider text-xs">
                        <span class="w-2 h-2 rounded-full bg-purple-400"></span> Manager
                    </div>
                    <ul class="text-sm space-y-1 text-primary-100/80 list-disc list-inside">
                        <li>Manage team members</li>
                        <li>Approve PRs & Winners</li>
                        <li>Negotiate with Vendors</li>
                        <li>Cannot manage company billing</li>
                    </ul>
                </div>

                <!-- Purchasing Manager -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-primary-200 font-bold uppercase tracking-wider text-xs">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span> Purchasing
                    </div>
                    <ul class="text-sm space-y-1 text-primary-100/80 list-disc list-inside">
                        <li>Create & Approve PRs</li>
                        <li>Negotiate Offers</li>
                        <li>Manage Purchase Orders</li>
                        <li>Vendor Selection</li>
                    </ul>
                </div>

                <!-- Finance -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-primary-200 font-bold uppercase tracking-wider text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Finance
                    </div>
                    <ul class="text-sm space-y-1 text-primary-100/80 list-disc list-inside">
                        <li>Approve Invoices</li>
                        <li>Execute Payments</li>
                        <li>View Financial Reports</li>
                        <li>Cannot approve procurement</li>
                    </ul>
                </div>

                <!-- Staff -->
                <div class="space-y-2">
                    <div class="flex items-center gap-2 text-primary-200 font-bold uppercase tracking-wider text-xs">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span> Staff
                    </div>
                    <ul class="text-sm space-y-1 text-primary-100/80 list-disc list-inside">
                        <li>Create Purchase Requisitions</li>
                        <li>View Status of requests</li>
                        <li>Basic access only</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Members List -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700">
            <h3 class="font-semibold text-gray-900 dark:text-white">Active Members</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-6 py-3">Member</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Joined Date</th>
                        <th class="px-6 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    {{-- Owner --}}
                    @if($company->user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center text-primary-600 font-bold">
                                {{ substr($company->user->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $company->user->name ?? 'Owner' }}</div>
                                <div class="text-xs">{{ $company->user->email }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-purple-100 text-purple-800 rounded text-xs font-semibold">Owner</span>
                        </td>
                        <td class="px-6 py-4">{{ $company->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right transform scale-90">
                           <span class="text-xs text-gray-400 italic">Cannot remove</span>
                        </td>
                    </tr>
                    @endif

                    {{-- Members --}}
                    @foreach($members as $member)
                        @if($member->id !== $company->user_id)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-6 py-4 flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-600 font-bold">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-medium text-gray-900 dark:text-white">{{ $member->name }}</div>
                                    <div class="text-xs">{{ $member->email }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $memberRole = $member->getRoleInCompany($companyId);
                                @endphp
                                @if($isAdminOrManager)
                                <form action="{{ route('team.update-role', $member->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="relative">
                                        <select name="role" onchange="this.form.submit()" class="appearance-none w-full bg-gray-50 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-200 py-1 px-3 pr-8 rounded leading-tight focus:outline-none focus:bg-white dark:focus:bg-gray-600 focus:border-primary-500 text-xs font-medium cursor-pointer transition-colors">
                                            <option value="admin" {{ $memberRole === 'admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="manager" {{ $memberRole === 'manager' ? 'selected' : '' }}>Manager</option>
                                            <option value="purchasing_manager" {{ $memberRole === 'purchasing_manager' ? 'selected' : '' }}>Purchasing Manager</option>
                                            <option value="finance" {{ $memberRole === 'finance' ? 'selected' : '' }}>Finance</option>
                                            <option value="staff" {{ $memberRole === 'staff' ? 'selected' : '' }}>Staff</option>
                                        </select>
                                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                                            <i data-feather="chevron-down" class="w-3 h-3"></i>
                                        </div>
                                    </div>
                                </form>
                                @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-semibold capitalize">{{ str_replace('_', ' ', $memberRole) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $member->pivot->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                @if($isAdminOrManager)
                                <form action="{{ route('team.remove', $member->id) }}" method="POST" onsubmit="return confirm('Remove this member?');">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors" title="Remove Member">
                                        <i data-feather="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-gray-400 italic">No access</span>
                                @endif
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            {{ $members->links() }}
        </div>
    </div>

    <!-- Pending Invitations -->
    @if($invitations->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow overflow-hidden mt-8">
        <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
            <h3 class="font-semibold text-gray-900 dark:text-white">Pending Invitations</h3>
            <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full font-bold">{{ $invitations->count() }} Pending</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-500 dark:text-gray-400">
                <thead class="bg-gray-50 dark:bg-gray-700/50 text-xs uppercase text-gray-700 dark:text-gray-300">
                    <tr>
                        <th class="px-6 py-3">Email</th>
                        <th class="px-6 py-3">Role</th>
                        <th class="px-6 py-3">Sent Date</th>
                        <th class="px-6 py-3">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($invitations as $invite)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-6 py-4 font-medium flex items-center gap-2">
                            <i data-feather="mail" class="w-3 h-3 text-gray-400"></i>
                            {{ $invite->email }}
                        </td>
                        <td class="px-6 py-4 capitalize">
                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-semibold">{{ $invite->role }}</span>
                        </td>
                        <td class="px-6 py-4">{{ $invite->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs font-bold inline-flex items-center gap-1">
                                <span class="w-1.5 h-1.5 bg-yellow-400 rounded-full"></span>
                                Awaiting Response
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

    <!-- Invite Modal -->
    <div id="inviteModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" onclick="document.getElementById('inviteModal').classList.add('hidden')"></div>

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-gray-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100 dark:border-gray-700">
                
                <!-- Header -->
                <div class="bg-gray-50/50 dark:bg-gray-700/50 px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white" id="modal-title">Invite Team Member</h3>
                    <button onclick="document.getElementById('inviteModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <i data-feather="x" class="w-5 h-5"></i>
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-6">
                    <form action="{{ route('team.invite') }}" method="POST">
                        @csrf
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-feather="mail" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" required placeholder="colleague@example.com"
                                        class="block w-full pl-10 pr-3 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all sm:text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Assign Role</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i data-feather="shield" class="h-5 w-5 text-gray-400"></i>
                                    </div>
                                    <select name="role" required class="block w-full pl-10 pr-10 py-2.5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-all sm:text-sm appearance-none cursor-pointer">
                                        <option value="staff">Staff - Can create Requests</option>
                                        <option value="purchasing_manager">Purchasing Manager - Can approve PRs & Winners</option>
                                        <option value="finance">Finance - Can process Payments</option>
                                        <option value="manager">Manager - Can manage Team</option>
                                        <option value="admin">Admin - Full Control</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                        <i data-feather="chevron-down" class="h-4 w-4 text-gray-400"></i>
                                    </div>
                                </div>
                                <p class="text-[10px] text-gray-500 mt-1.5 ml-1">Select the role that best fits their responsibilities.</p>
                            </div>
                        </div>

                        <div class="mt-8 flex gap-3">
                            <button type="button" onclick="document.getElementById('inviteModal').classList.add('hidden')"
                                class="flex-1 px-4 py-2.5 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-200 font-semibold rounded-xl border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all">
                                Cancel
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-2.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-500/30 transition-all">
                                Send Invitation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
{{-- SweetAlert2 loaded via Vite --}}
<script>
    // Success Notification using SweetAlert
    @if(session('success'))
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: "{{ session('success') }}",
                icon: 'success',
                confirmButtonText: 'Great',
                buttonsStyling: false,
                customClass: {
                    popup: 'dark:bg-gray-800 dark:text-white rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700',
                    title: 'text-xl font-bold text-gray-900 dark:text-white',
                    htmlContainer: 'text-gray-600 dark:text-gray-300',
                    confirmButton: 'px-6 py-2.5 bg-gradient-to-r from-primary-600 to-primary-500 text-white font-semibold rounded-xl hover:from-primary-700 hover:to-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 shadow-lg shadow-primary-500/30 transition-all'
                }
            });
        });
    @endif

    // Error Notification
    @if($errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error',
                html: "{!! implode('<br>', $errors->all()) !!}",
                icon: 'error',
                confirmButtonText: 'Okay',
                buttonsStyling: false,
                customClass: {
                    popup: 'dark:bg-gray-800 dark:text-white rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700',
                    title: 'text-xl font-bold text-gray-900 dark:text-white',
                    htmlContainer: 'text-red-600 dark:text-red-400',
                    confirmButton: 'px-6 py-2.5 bg-gray-200 text-gray-800 font-semibold rounded-xl hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all'
                }
            });
        });
    @endif
</script>
@endpush
@endsection
