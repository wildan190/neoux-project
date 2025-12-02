<nav class="flex" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-3">
        <li class="inline-flex items-center">
            <a href="{{ route('admin.dashboard') }}"
                class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-white">
                <i data-feather="home" class="w-4 h-4 mr-2"></i>
                Dashboard
            </a>
        </li>
        @if(isset($breadcrumbs) && count($breadcrumbs) > 0)
            @foreach($breadcrumbs as $breadcrumb)
                <li>
                    <div class="flex items-center">
                        <i data-feather="chevron-right" class="w-4 h-4 text-gray-400"></i>
                        @if(isset($breadcrumb['url']))
                            <a href="{{ $breadcrumb['url'] }}"
                                class="ml-1 text-sm font-medium text-gray-700 hover:text-indigo-600 md:ml-2 dark:text-gray-400 dark:hover:text-white">
                                {{ $breadcrumb['label'] }}
                            </a>
                        @else
                            <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 dark:text-gray-400">
                                {{ $breadcrumb['label'] }}
                            </span>
                        @endif
                    </div>
                </li>
            @endforeach
        @endif
    </ol>
</nav>