{{-- GUEST NAVBAR --}}
<nav class="sticky top-0 bg-white/70 dark:bg-gray-900/70 backdrop-blur z-50 shadow-sm">
    <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center gap-4">
        <a href="/" class="text-2xl font-black text-primary-600 dark:text-primary-400 flex-shrink-0">Huntr</a>
        
        <div class="hidden md:flex flex-1 justify-center px-8">
            @if(isset($showBackButton) && $showBackButton)
                <a href="{{ route('market.index') }}" class="flex items-center gap-2 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-primary-600 transition-colors">
                    <i data-feather="arrow-left" class="w-4 h-4"></i> Kembali ke Market
                </a>
            @endif
        </div>

        <div>
            @auth
                <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg font-bold text-sm transition-all hover:bg-gray-200">
                    Dashboard
                </a>
            @else
                <a href="/login" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg shadow font-bold text-sm transition-all hover:scale-[1.02]">
                    Login
                </a>
            @endauth
        </div>
    </div>
</nav>
