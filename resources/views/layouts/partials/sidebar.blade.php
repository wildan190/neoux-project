<aside id="sidebar"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 shadow-lg transform transition-transform duration-300">

    {{-- Navigation (tanpa header) --}}
    <nav class="mt-6 px-3">
        <ul class="space-y-2">
            <li>
                <a href="/dashboard"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition cursor-pointer">
                    <i data-feather="home" class="w-5 h-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="/users"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition cursor-pointer">
                    <i data-feather="users" class="w-5 h-5"></i>
                    <span class="font-medium">Users</span>
                </a>
            </li>
            <li>
                <a href="{{ route('companies.index') }}"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition cursor-pointer">
                    <i data-feather="briefcase" class="w-5 h-5"></i>
                    <span class="font-medium">Company</span>
                </a>
            </li>
            <li>
                <a href="/settings"
                    class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition cursor-pointer">
                    <i data-feather="settings" class="w-5 h-5"></i>
                    <span class="font-medium">Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    {{-- Footer --}}
    <div
        class="absolute bottom-0 left-0 w-full px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div
                    class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center text-indigo-600 dark:text-indigo-400 font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="text-sm">
                    <p class="font-medium text-gray-700 dark:text-gray-200">{{ Auth::user()->name }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition"
                    title="Logout">
                    <i data-feather="log-out" class="w-5 h-5"></i>
                </button>
            </form>
        </div>
    </div>

</aside>