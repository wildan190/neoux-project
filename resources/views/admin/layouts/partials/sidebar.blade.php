<aside id="admin-sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-gradient-to-b from-gray-900 to-gray-800 dark:from-gray-950 dark:to-gray-900 shadow-2xl flex-shrink-0 transform -translate-x-full md:translate-x-0 transition-transform duration-300 flex flex-col overflow-hidden">
    {{-- Logo & Brand --}}
    <div class="p-6 border-b border-gray-700/50">
        <div class="flex items-center space-x-3">
            <div
                class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center shadow-lg">
                <i data-feather="shield" class="w-6 h-6 text-white"></i>
            </div>
            <div>
                <h2 class="text-xl font-bold text-white">Admin Panel</h2>
                <p class="text-xs text-gray-400">NeoUX Platform</p>
            </div>
        </div>
    </div>

    {{-- Navigation --}}
    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('admin.dashboard') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.dashboard') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <i data-feather="home" class="w-5 h-5"></i>
            </div>
            <span class="ml-3">Dashboard</span>
        </a>

        <a href="{{ route('admin.companies.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('admin.companies.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.companies.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <i data-feather="briefcase" class="w-5 h-5"></i>
            </div>
            <span class="ml-3">Companies</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('admin.users.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.users.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <i data-feather="users" class="w-5 h-5"></i>
            </div>
            <span class="ml-3">Users</span>
        </a>

        <a href="{{ route('admin.categories.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('admin.categories.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.categories.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <i data-feather="tag" class="w-5 h-5"></i>
            </div>
            <span class="ml-3">Categories</span>
        </a>

        <a href="{{ route('admin.admins.index') }}"
            class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group {{ request()->routeIs('admin.admins.*') ? 'bg-primary-500 text-white shadow-lg shadow-primary-500/30' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}">
            <div
                class="w-9 h-9 rounded-lg flex items-center justify-center {{ request()->routeIs('admin.admins.*') ? 'bg-white/20' : 'bg-gray-700/50 group-hover:bg-gray-600/50' }} transition-colors">
                <i data-feather="shield" class="w-5 h-5"></i>
            </div>
            <span class="ml-3">Admins</span>
        </a>
    </nav>

    {{-- Admin Profile --}}
    <div class="p-4 border-t border-gray-700/50">
        <div class="bg-gray-700/30 rounded-xl p-3 backdrop-blur-sm">
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white font-bold shadow-lg">
                    {{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth('admin')->user()->name }}</p>
                    <p class="text-xs text-gray-400 truncate">{{ auth('admin')->user()->email }}</p>
                </div>
            </div>
            <form action="{{ route('admin.logout') }}" method="POST" class="mt-3">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center space-x-2 px-3 py-2 bg-gray-600/50 hover:bg-gray-600 rounded-lg text-xs font-medium text-gray-200 hover:text-white transition-colors">
                    <i data-feather="log-out" class="w-4 h-4"></i>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>