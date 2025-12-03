<aside class="w-64 bg-white dark:bg-gray-800 shadow-md flex-shrink-0">
    <div class="p-6">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Admin Panel</h2>
    </div>

    <nav class="mt-6">
        <a href="{{ route('admin.dashboard') }}"
            class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 dark:bg-gray-700 border-r-4 border-primary-500' : '' }}">
            <i data-feather="home" class="w-5 h-5 mr-3"></i>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('admin.companies.index') }}"
            class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.companies.*') ? 'bg-gray-100 dark:bg-gray-700 border-r-4 border-primary-500' : '' }}">
            <i data-feather="briefcase" class="w-5 h-5 mr-3"></i>
            <span>Companies</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
            class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 dark:bg-gray-700 border-r-4 border-primary-500' : '' }}">
            <i data-feather="users" class="w-5 h-5 mr-3"></i>
            <span>Users</span>
        </a>

        <a href="{{ route('admin.categories.index') }}"
            class="flex items-center space-x-3 px-4 py-3 rounded-lg {{ request()->routeIs('admin.categories.*') ? 'bg-primary-50 text-primary-600 dark:bg-primary-900 dark:text-primary-200' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }} transition-colors">
            <i data-feather="tag" class="w-5 h-5"></i>
            <span class="font-medium">Categories</span>
        </a>

        <a href="{{ route('admin.admins.index') }}"
            class="flex items-center px-6 py-3 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ request()->routeIs('admin.admins.*') ? 'bg-gray-100 dark:bg-gray-700 border-r-4 border-primary-500' : '' }}">
            <i data-feather="shield" class="w-5 h-5 mr-3"></i>
            <span>Admins</span>
        </a>
    </nav>

    <div class="absolute bottom-0 w-64 p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <div class="w-10 h-10 rounded-full bg-primary-500 flex items-center justify-center text-white font-bold">
                    {{ strtoupper(substr(auth('admin')->user()->name, 0, 1)) }}
                </div>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ auth('admin')->user()->name }}</p>
                <form action="{{ route('admin.logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">Logout</button>
                </form>
            </div>
        </div>
    </div>
</aside>