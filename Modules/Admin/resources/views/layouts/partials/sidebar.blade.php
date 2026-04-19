<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-gray-900 border-r border-gray-800 shadow-xl transform transition-transform duration-300 flex flex-col -translate-x-full md:translate-x-0">

    {{-- Admin Identity --}}
    <div class="px-6 py-6 border-b border-gray-800/50">
        <div class="flex items-center space-x-3">
            <div
                class="w-8 h-8 rounded-lg bg-primary-600 flex items-center justify-center shadow-md text-white">
                <i data-feather="shield" class="w-5 h-5"></i>
            </div>
            <div>
                <h2 class="text-lg font-bold text-white tracking-tight uppercase leading-none mb-1">Admin</h2>
                <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest leading-none">System Control</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-8 space-y-1 overflow-y-auto">
        <div class="px-4 pb-4">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.3em]">Operational</p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-4 py-3 rounded-xl text-xs font-semibold transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-primary-600 text-white shadow-md' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i data-feather="grid" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-gray-500 group-hover:text-primary-400' }}"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.companies.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('admin.companies.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i data-feather="briefcase" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.companies.*') ? 'text-white' : 'text-gray-500 group-hover:text-primary-400' }}"></i>
            <span>Company Review</span>
        </a>

        <div class="px-4 pt-8 pb-4">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">Users & Access</p>
        </div>

        <a href="{{ route('admin.users.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i data-feather="users" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.users.*') ? 'text-white' : 'text-gray-500 group-hover:text-primary-400' }}"></i>
            <span>User Management</span>
        </a>

        <a href="{{ route('admin.admins.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('admin.admins.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i data-feather="terminal" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.admins.*') ? 'text-white' : 'text-gray-500 group-hover:text-primary-400' }}"></i>
            <span>Admin Nodes</span>
        </a>

        <div class="px-4 pt-8 pb-4">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">System Data</p>
        </div>

        <a href="{{ route('admin.categories.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('admin.categories.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-500 dark:text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i data-feather="list" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.categories.*') ? 'text-white' : 'text-gray-500 group-hover:text-primary-400' }}"></i>
            <span>Module Categories</span>
        </a>

        <div class="px-4 pt-8 pb-4">
            <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em]">Support</p>
        </div>

        <a href="{{ route('admin.support.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-xs font-bold transition-all duration-200 group {{ request()->routeIs('admin.support.*') ? 'bg-primary-600 text-white shadow-lg shadow-primary-600/20' : 'text-gray-400 hover:bg-white/5 hover:text-white' }}">
            <i data-feather="life-buoy" class="w-4 h-4 mr-3 {{ request()->routeIs('admin.support.*') ? 'text-white' : 'text-gray-500 group-hover:text-primary-400' }}"></i>
            <span>Support Tickets</span>
            @php $openCount = \Modules\Support\Models\SupportTicket::where('status', 'open')->count(); @endphp
            @if($openCount > 0)
                <span class="ml-auto inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold text-white bg-amber-500 rounded-full">{{ $openCount }}</span>
            @endif
        </a>
    </nav>

    {{-- Admin Profile / Session --}}
    <div class="p-4 border-t border-gray-800/50">
        <div class="bg-gray-800/50 rounded-2xl p-4 border border-gray-800/50">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gray-800 flex items-center justify-center text-primary-500">
                    <i data-feather="user" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-[11px] font-black text-white truncate max-w-[120px]">{{ auth('admin')->user()->name }}</p>
                    <p class="text-[9px] font-bold text-gray-500 uppercase tracking-widest">Administrator</p>
                </div>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-3 py-2.5 bg-red-600/10 hover:bg-red-600 text-red-500 hover:text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                    <i data-feather="log-out" class="w-3.5 h-3.5"></i>
                    <span>ADMIN LOGOUT</span>
                </button>
            </form>
        </div>
    </div>

</aside>
