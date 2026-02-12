<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="user-id" content="{{ auth()->id() }}">
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400..700;1,400..700&display=swap"
        rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
        @keyframes shimmer {
            0% { background-position: -1000px 0; }
            100% { background-position: 1000px 0; }
        }
        .animate-shimmer {
            animation: shimmer 2s infinite linear;
            background: linear-gradient(to right, #f6f7f8 8%, #edeef1 18%, #f6f7f8 33%);
            background-size: 1000px 100%;
        }
        .dark .animate-shimmer {
            background: linear-gradient(to right, #1f2937 8%, #374151 18%, #1f2937 33%);
            background-size: 1000px 100%;
        }
    </style>
</head>

<body class="bg-gray-50 dark:bg-gray-900 transition-colors duration-300">
    <div class="flex min-h-screen">

        {{-- Sidebar --}}
        @include('layouts.partials.sidebar')

        {{-- Mobile Overlay --}}
        <div id="overlay" class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

        {{-- Main Content --}}
        <div class="flex-1 flex flex-col overflow-hidden min-w-0 md:pl-64">

            {{-- Header --}}
            <header
                class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 sticky top-0 z-40">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">

                        {{-- Left --}}
                        <div class="flex items-center space-x-4">
                            {{-- Mobile Menu Button --}}
                            <button id="toggleSidebar"
                                class="md:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                <i data-feather="menu" class="w-6 h-6 text-gray-600 dark:text-gray-300"></i>
                            </button>

                            <div id="header-content-area">
                                @if(isset($title))
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white" id="page-title">{{ $title }}</h1>
                                    <div id="breadcrumb-area">
                                        @include('layouts.partials.breadcrumbs')
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Right --}}
                        <div class="flex items-center gap-4">

                            {{-- Company Switcher --}}
                            @php
                                $selectedCompanyId = session('selected_company_id');
                                $userCompanies = auth()->user()->allCompanies();
                                $selectedCompany = $userCompanies->firstWhere('id', $selectedCompanyId)
                                    ?? $userCompanies->first();
                            @endphp

                            @if($selectedCompany)
                                <div class="relative group" id="companySwitcher">
                                    <button
                                        class="hidden md:flex items-center gap-2 px-2.5 py-1.5 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700/50 dark:hover:bg-gray-700 rounded-lg transition-all border border-gray-200 dark:border-gray-600">
                                        <div
                                            class="w-7 h-7 rounded-lg bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400">
                                            <i data-feather="briefcase" class="w-3.5 h-3.5"></i>
                                        </div>
                                        <div class="text-left mr-1">
                                            <p class="text-xs font-bold text-gray-900 dark:text-white leading-none">
                                                {{ $selectedCompany->name }}
                                            </p>
                                            <p class="text-[9px] text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                {{ $selectedCompany->category }}
                                            </p>
                                        </div>
                                        <i data-feather="chevron-down"
                                            class="w-3.5 h-3.5 text-gray-400 transition group-hover:rotate-180"></i>
                                    </button>

                                    {{-- Dropdown --}}
                                    <div
                                        class="absolute right-0 top-full mt-2 w-72 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all origin-top-right z-50">
                                        <div
                                            class="p-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">
                                                Switch Workspace
                                            </p>
                                        </div>

                                        <div class="max-h-[300px] overflow-y-auto p-2 space-y-1">
                                            @foreach($userCompanies as $company)
                                                <form action="{{ route('dashboard.select-company', $company->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="w-full flex items-center gap-3 p-2 rounded-xl transition
                                                        {{ $company->id == $selectedCompany->id
                                                            ? 'bg-primary-50 dark:bg-primary-900/20 border border-primary-100 dark:border-primary-900/30'
                                                            : 'hover:bg-gray-50 dark:hover:bg-gray-700/50 border border-transparent'
                                                        }}">
                                                        <div
                                                            class="w-8 h-8 rounded-lg flex items-center justify-center
                                                            {{ $company->id == $selectedCompany->id
                                                                ? 'bg-primary-100 dark:bg-primary-900/40 text-primary-600 dark:text-primary-400'
                                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'
                                                            }}">
                                                            @if($company->id == $selectedCompany->id)
                                                                <i data-feather="check" class="w-4 h-4"></i>
                                                            @else
                                                                <span class="text-xs font-bold">{{ substr($company->name, 0, 1) }}</span>
                                                            @endif
                                                        </div>

                                                        <div class="text-left flex-1 min-w-0">
                                                            <p
                                                                class="text-sm font-semibold truncate
                                                                {{ $company->id == $selectedCompany->id ? 'text-primary-700 dark:text-primary-300' : 'text-gray-900 dark:text-white' }}">
                                                                {{ $company->name }}
                                                            </p>
                                                            <div class="flex items-center gap-2">
                                                                <span class="w-1.5 h-1.5 rounded-full
                                                                    {{ in_array($company->status, ['approved', 'active'])
                                                                        ? 'bg-green-500'
                                                                        : ($company->status == 'pending' ? 'bg-yellow-500' : 'bg-red-500') }}">
                                                                </span>
                                                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                                                    {{ $company->status }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </button>
                                                </form>
                                            @endforeach
                                        </div>

                                        <div
                                            class="p-2 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-700/30">
                                            <a href="{{ route('companies.create') }}"
                                                class="flex items-center justify-center gap-2 p-2 text-sm font-medium text-primary-600 dark:text-primary-400 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-xl">
                                                <i data-feather="plus-circle" class="w-4 h-4"></i>
                                                Register New Company
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endif


                            {{-- Notifications --}}
                            <div class="relative" id="notificationDropdown">
                                <button id="notificationButton"
                                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition relative">
                                    <i data-feather="bell" class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
                                    <span id="notificationBadge"
                                        class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-red-500 text-white text-[10px] font-bold rounded-full hidden border-2 border-white dark:border-gray-800 flex items-center justify-center"></span>
                                </button>

                                <div id="notificationMenu"
                                    class="absolute right-0 mt-3 w-96 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-700 hidden overflow-hidden z-50">
                                    <div
                                        class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                                        <h3 class="font-bold text-gray-900 dark:text-white">Notifications</h3>
                                        <button onclick="markAllNotificationsRead()"
                                            class="text-xs text-primary-600 hover:text-primary-700 font-bold">Mark all
                                            read</button>
                                        <button onclick="if(window.testNotificationSound) window.testNotificationSound()"
                                            class="ml-2 text-xs text-gray-400 hover:text-gray-600" title="Test Sound">
                                            <i data-feather="volume-2" class="w-3 h-3"></i>
                                        </button>
                                    </div>
                                    <div id="notificationList" class="max-h-80 overflow-y-auto">
                                        <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">Loading...
                                        </div>
                                    </div>
                                    <div class="p-3 border-t border-gray-100 dark:border-gray-700 text-center">
                                        <a href="{{ route('notifications.index') }}"
                                            class="text-sm font-bold text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">View
                                            All</a>
                                    </div>
                                </div>
                            </div>

                            {{-- Dark Mode --}}
                            <button id="darkModeToggle"
                                class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                                <i id="darkIcon" data-feather="moon"
                                    class="w-5 h-5 text-gray-600 dark:text-gray-300"></i>
                            </button>

                        </div>
                    </div>
                </div>
            </header>

            {{-- Skeleton Template (Hidden) --}}
            <template id="skeleton-template">
                <div class="animate-pulse space-y-8">
                    {{-- Header Skeleton --}}
                    <div class="flex items-center justify-between">
                        <div class="h-8 w-64 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                        <div class="h-10 w-32 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                    </div>
                    
                    {{-- Banner/Card Skeleton --}}
                    <div class="h-32 w-full bg-gray-100 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700"></div>
                    
                    {{-- Table Skeleton --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <div class="h-6 w-48 bg-gray-200 dark:bg-gray-700 rounded"></div>
                        </div>
                        <div class="p-6 space-y-4">
                            @for($i = 0; $i < 5; $i++)
                                <div class="flex items-center justify-between py-2 border-b border-gray-50 dark:border-gray-700/50 last:border-0">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-10 h-10 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                                        <div class="space-y-2">
                                            <div class="h-4 w-32 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                            <div class="h-3 w-20 bg-gray-100 dark:bg-gray-800 rounded"></div>
                                        </div>
                                    </div>
                                    <div class="h-4 w-24 bg-gray-200 dark:bg-gray-700 rounded"></div>
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </template>

            {{-- Progress Bar (at top of main) --}}
            <div id="global-progress" class="fixed top-0 left-0 md:left-64 right-0 h-1 bg-primary-600 z-[60] transition-all duration-300 opacity-0" style="width: 0%"></div>

            {{-- Content --}}
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 dark:bg-gray-900">
                <div class="w-full px-6 py-8" id="main-content-area">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- ===================================================== --}}
    {{-- CLEAN FIXED JAVASCRIPT --}}
    {{-- ===================================================== --}}
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            /* ---------------------------
             * FEATHER ICONS
             * --------------------------- */
            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            /* ---------------------------
             * DARK MODE SYSTEM
             * --------------------------- */
            var htmlEl = document.documentElement;
            var darkIcon = document.getElementById('darkIcon');
            var darkToggle = document.getElementById('darkModeToggle');

            var savedTheme = localStorage.getItem('theme');

            if (savedTheme === 'dark') {
                htmlEl.classList.add('dark');
                if (darkIcon) darkIcon.dataset.feather = 'sun';
            }

            if (typeof feather !== 'undefined') {
                feather.replace();
            }

            if (darkToggle) {
                darkToggle.addEventListener('click', function() {
                    htmlEl.classList.toggle('dark');
                    var isDark = htmlEl.classList.contains('dark');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                    if (darkIcon) {
                        darkIcon.dataset.feather = isDark ? 'sun' : 'moon';
                    }
                    if (typeof feather !== 'undefined') {
                        feather.replace();
                    }
                });
            }

            /* ---------------------------
             * NOTIFICATION SYSTEM
             * --------------------------- */
            var notifButton = document.getElementById('notificationButton');
            var notifMenu = document.getElementById('notificationMenu');
            var notifBadge = document.getElementById('notificationBadge');
            var notifList = document.getElementById('notificationList');

            if (notifButton) {
                notifButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    notifMenu.classList.toggle('hidden');
                    if (!notifMenu.classList.contains('hidden')) {
                        fetchLatestNotifications();
                    }
                });
            }

            document.addEventListener('click', function(e) {
                if (notifMenu && !notifMenu.contains(e.target) && !notifButton.contains(e.target)) {
                    notifMenu.classList.add('hidden');
                }
            });

            window.fetchLatestNotifications = function() {
                fetch('{{ route('notifications.latest') }}')
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        renderNotifications(data.notifications);
                    })
                    .catch(function(err) {
                        console.error('Failed to fetch notifications', err);
                    });
            };

            function renderNotifications(notifications) {
                if (!notifList) return;

                if (notifications.length === 0) {
                    notifList.innerHTML = '<div class="p-8 text-center">' +
                        '<div class="w-12 h-12 bg-gray-50 dark:bg-gray-700/50 rounded-full flex items-center justify-center mx-auto mb-3">' +
                        '<i data-feather="bell-off" class="w-6 h-6 text-gray-400"></i>' +
                        '</div>' +
                        '<p class="text-xs text-gray-500 dark:text-gray-400">No notifications yet</p>' +
                        '</div>';
                    if (typeof feather !== 'undefined') feather.replace();
                    return;
                }

                notifList.innerHTML = notifications.map(function(n) {
                    var title = n.data.title || '';
                    var message = n.data.message || '';
                    var url = n.data.url || '';
                    var readClass = n.read_at ? 'opacity-60' : '';
                    var dotClass = n.read_at ? 'bg-transparent' : 'bg-primary-600';
                    var timeStr = new Date(n.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

                    return '<a href="#" class="block p-5 border-b border-gray-50 dark:border-gray-700/50 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition ' + readClass + '" ' +
                        'onclick="event.preventDefault(); markAsReadLocal(\'' + n.id + '\', \'' + url + '\')">' +
                        '<div class="flex gap-4">' +
                        '<div class="w-2.5 h-2.5 ' + dotClass + ' rounded-full mt-1.5 flex-shrink-0"></div>' +
                        '<div class="min-w-0 flex-1">' +
                        '<div class="flex justify-between items-start gap-2">' +
                        '<p class="text-[12px] font-bold text-gray-900 dark:text-white uppercase tracking-wider truncate">' + title + '</p>' +
                        '<p class="text-[10px] text-gray-400 dark:text-gray-500 whitespace-nowrap">' + timeStr + '</p>' +
                        '</div>' +
                        '<p class="text-[13px] text-gray-600 dark:text-gray-400 line-clamp-2 mt-1 leading-relaxed">' + message + '</p>' +
                        '</div>' +
                        '</div>' +
                        '</a>';
                }).join('');
            }

            window.markAsReadLocal = function(id, url) {
                // Fix for absolute URLs pointing to localhost in database
                if (url && url.includes('localhost:8000')) {
                    url = url.replace(/https?:\/\/localhost:8000/, window.location.origin);
                }

                fetch('/notifications/mark-as-read/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).finally(function() {
                    if (url && url !== 'null' && url !== 'undefined') {
                        // Use the SPA loader if available, otherwise standard redirect
                        if (typeof loadPage === 'function' && url.startsWith(window.location.origin)) {
                            loadPage(url);
                        } else {
                            window.location.href = url;
                        }
                    } else {
                        window.location.reload();
                    }
                });
            };

            window.markAllNotificationsRead = function() {
                fetch('{{ route('notifications.mark-all-as-read') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(function() {
                    window.fetchLatestNotifications();
                    window.updateUnreadCount(false);
                }).catch(function(err) {
                    console.error('Failed to mark all as read', err);
                });
            };

            var lastUnreadCount = -1;

            window.updateUnreadCount = function(triggerToast) {
                if (typeof triggerToast === 'undefined') triggerToast = true;

                fetch('{{ route('notifications.unread-count') }}')
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        // Update all potential badges
                        Object.keys(data).forEach(function(key) {
                            var badge = document.getElementById('badge-' + key);
                            if (badge) {
                                var count = data[key];
                                // Don't show badge if it's the active page
                                var link = badge.closest('a');
                                var isActive = link && (link.classList.contains('bg-primary-500') || link.getAttribute('href') === window.location.pathname);
                                
                                if (count > 0 && !isActive) {
                                    badge.innerText = count;
                                    badge.classList.remove('hidden');
                                    badge.style.display = '';
                                } else {
                                    badge.classList.add('hidden');
                                    badge.innerText = '';
                                    badge.style.display = 'none';
                                }
                            }
                        });

                        // Special handling for notification badge in header
                        if (notifBadge) {
                            if (data.notifications > 0) {
                                notifBadge.innerText = data.notifications;
                                notifBadge.classList.remove('hidden');
                                notifBadge.style.display = '';
                            } else {
                                notifBadge.classList.add('hidden');
                                notifBadge.innerText = '';
                                notifBadge.style.display = 'none';
                            }
                        }

                        if (lastUnreadCount !== -1 && data.notifications > lastUnreadCount && triggerToast) {
                            window.showToast('You have a new notification', 'success');
                            if (notifMenu && !notifMenu.classList.contains('hidden')) {
                                window.fetchLatestNotifications();
                            }
                        }
                        lastUnreadCount = data.notifications;
                    })
                    .catch(function(err) {
                        console.error('Failed to fetch unread count', err);
                    });
            };

            window.updateUnreadCount(false);

            /* ---------------------------
             * TOAST SYSTEM
             * --------------------------- */
            var toastContainer = document.getElementById('toastContainer');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toastContainer';
                toastContainer.className = 'fixed bottom-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none';
                document.body.appendChild(toastContainer);
            }

            window.showToast = function(message, type) {
                if (!type) type = 'success';
                var toast = document.createElement('div');
                var bgColorClass = type === 'success' ? 'border-green-100 dark:border-green-900/30' : 'border-red-100 dark:border-red-900/30';
                var colorClass = type === 'success' ? 'text-green-500' : 'text-red-500';
                var iconName = type === 'success' ? 'check-circle' : 'alert-circle';

                toast.className = 'p-4 px-6 rounded-2xl shadow-2xl transform transition-all duration-300 translate-y-12 opacity-0 flex items-center gap-4 bg-white dark:bg-gray-800 border ' + bgColorClass + ' pointer-events-auto';
                
                toast.innerHTML = '<div class="' + colorClass + '">' +
                    '<i data-feather="' + iconName + '" class="w-6 h-6"></i>' +
                    '</div>' +
                    '<div>' +
                    '<p class="text-sm font-bold text-gray-900 dark:text-white">' + message + '</p>' +
                    '</div>';

                toastContainer.appendChild(toast);
                if (typeof feather !== 'undefined') feather.replace();

                requestAnimationFrame(function() {
                    toast.classList.remove('translate-y-12', 'opacity-0');
                    toast.classList.add('translate-y-0', 'opacity-100');
                });

                setTimeout(function() {
                    toast.classList.remove('translate-y-0', 'opacity-100');
                    toast.classList.add('translate-x-12', 'opacity-0');
                    setTimeout(function() { toast.remove(); }, 300);
                }, 4000);
            };

            // Session messages
            @if(session('success'))
                window.showToast({{ Js::from(session('success')) }}, 'success');
            @endif
            @if(session('error'))
                window.showToast({{ Js::from(session('error')) }}, 'error');
            @endif

            /* ---------------------------
             * MOBILE SIDEBAR
             * --------------------------- */
            var sidebar = document.getElementById('sidebar');
            var overlay = document.getElementById('overlay');
            var toggleBtn = document.getElementById('toggleSidebar');

            if (sidebar && window.innerWidth < 768) {
                sidebar.classList.add('-translate-x-full');
            }

            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    if (sidebar) sidebar.classList.remove('-translate-x-full');
                    if (overlay) overlay.classList.remove('hidden');
                });
            }

            if (overlay) {
                overlay.addEventListener('click', function() {
                    if (sidebar) sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                });
            }

            window.addEventListener('resize', function() {
                if (sidebar) {
                    if (window.innerWidth >= 768) {
                        sidebar.classList.remove('-translate-x-full');
                        if (overlay) overlay.classList.add('hidden');
                    } else {
                        sidebar.classList.add('-translate-x-full');
                    }
                }
            });

            /* ---------------------------
             * SWEETALERT2
             * --------------------------- */
            // Success Message
            @if(session('success'))
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: {{ Js::from(session('success')) }},
                    confirmButtonColor: '#4f46e5',
                    timer: 3000
                });
            @endif

            // Error Message
            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: {{ Js::from(session('error')) }},
                    confirmButtonColor: '#ef4444'
                });
            @endif

            // Validation Errors
            @if($errors->any())
                var errorHtml = '<ul class="text-left text-sm">';
                @foreach($errors->all() as $error)
                    errorHtml += '<li>• ' + {{ Js::from($error) }} + '</li>';
                @endforeach
                errorHtml += '</ul>';

                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    html: errorHtml,
                    confirmButtonColor: '#ef4444'
                });
            @endif

            /* ---------------------------
             * PARTIAL PAGE RELOAD SYSTEM (SPA-ish)
             * --------------------------- */
            const mainContent = document.getElementById('main-content-area');
            const headerContent = document.getElementById('header-content-area');
            const skeletonTemplate = document.getElementById('skeleton-template');
            const globalProgress = document.getElementById('global-progress');

            function showLoader() {
                // 1. Show Progress Bar
                globalProgress.style.opacity = '1';
                globalProgress.style.width = '30%';
                
                // 2. Clear title/breadcrumbs to prevent "old" data showing
                headerContent.style.opacity = '0.3';
                
                // 3. Inject Skeleton into main content
                if (skeletonTemplate && mainContent) {
                    mainContent.style.opacity = '0.5';
                    mainContent.innerHTML = skeletonTemplate.innerHTML;
                    // Trigger a tiny fade in for the skeleton
                    setTimeout(() => {
                        mainContent.style.opacity = '1';
                    }, 50);
                }
            }

            function hideLoader() {
                globalProgress.style.width = '100%';
                headerContent.style.opacity = '1';
                setTimeout(() => {
                    globalProgress.style.opacity = '0';
                    setTimeout(() => {
                        globalProgress.style.width = '0%';
                    }, 300);
                }, 200);
            }

            async function loadPage(url, pushState = true) {
                showLoader();
                
                try {
                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (!response.ok) throw new Error('Network response was not ok');
                    
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');

                    // 1. Update Document Title
                    const newTitle = doc.querySelector('title');
                    if (newTitle) {
                        document.title = newTitle.innerText;
                    }

                    // 2. Update Header/Breadcrumbs
                    const newHeader = doc.getElementById('header-content-area');
                    if (newHeader) {
                        headerContent.innerHTML = newHeader.innerHTML;
                    }

                    // 3. Update Main Content
                    const newMain = doc.getElementById('main-content-area');
                    if (newMain) {
                        // Small fade out of skeleton
                        mainContent.style.opacity = '0.7';
                        
                        setTimeout(() => {
                            mainContent.innerHTML = newMain.innerHTML;
                            mainContent.style.opacity = '1';
                            
                            // Execute scripts in the new main content
                            const scripts = mainContent.querySelectorAll('script');
                            scripts.forEach(oldScript => {
                                const newScript = document.createElement('script');
                                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                                oldScript.parentNode.replaceChild(newScript, oldScript);
                            });

                            // Re-initialize Global UI Components
                            reinitializeUI();
                        }, 50);
                    }

                    // 4. Update Sidebar Active State
                    updateSidebarActive(url);

                    // 5. Update History
                    if (pushState) {
                        window.history.pushState({ url }, '', url);
                    }

                    // 6. Re-initialize Global UI Components
                    reinitializeUI();

                    // 7. Scroll to top
                    window.scrollTo({ top: 0, behavior: 'instant' });

                } catch (error) {
                    console.error('Partial load failed:', error);
                    window.location.href = url;
                } finally {
                    hideLoader();
                }
            }

            function updateSidebarActive(currentUrl) {
                const sidebarLinks = document.querySelectorAll('#sidebar a');
                const urlObj = new URL(currentUrl, window.location.origin);
                const currentPath = urlObj.pathname;
                const currentSearch = urlObj.search;

                let bestMatch = null;
                let maxPathLength = -1;

                sidebarLinks.forEach(link => {
                    const href = link.getAttribute('href');
                    if (!href) return;
                    
                    const linkUrl = new URL(href, window.location.origin);
                    const linkPath = linkUrl.pathname;
                    const linkSearch = linkUrl.search;

                    let isActive = false;
                    if (linkSearch) {
                        isActive = (currentPath === linkPath && currentSearch === linkSearch);
                    } else if (linkPath !== '/') {
                        isActive = currentPath === linkPath || currentPath.startsWith(linkPath + '/');
                    } else {
                        isActive = currentPath === '/';
                    }

                    if (isActive) {
                        // Most specific match wins (the one with the longest path)
                        if (linkPath.length > maxPathLength) {
                            maxPathLength = linkPath.length;
                            bestMatch = link;
                        }
                    }
                });

                sidebarLinks.forEach(link => {
                    const isActive = (link === bestMatch);
                    const badge = link.querySelector('span[id^="badge-"]');

                    if (isActive) {
                        link.classList.add('bg-primary-500', 'text-white', 'shadow-lg', 'shadow-primary-500/30');
                        link.classList.remove('text-gray-300', 'hover:bg-gray-700/50', 'hover:text-white');
                        
                        const iconBox = link.querySelector('div');
                        if (iconBox) {
                            iconBox.classList.add('bg-white/20');
                            iconBox.classList.remove('bg-gray-700/50', 'group-hover:bg-gray-600/50');
                        }
                        
                        // Hide badge when active
                        if (badge) {
                            badge.classList.add('hidden');
                            badge.style.display = 'none';
                            badge.innerText = '';
                        }
                    } else {
                        link.classList.remove('bg-primary-500', 'text-white', 'shadow-lg', 'shadow-primary-500/30');
                        link.classList.add('text-gray-300', 'hover:bg-gray-700/50', 'hover:text-white');
                        
                        const iconBox = link.querySelector('div');
                        if (iconBox) {
                            iconBox.classList.remove('bg-white/20');
                            iconBox.classList.add('bg-gray-700/50', 'group-hover:bg-gray-600/50');
                        }
                        
                        // Badge will be shown/hidden by updateUnreadCount based on actual unread data
                    }
                });
            }

            function reinitializeUI() {
                if (typeof feather !== 'undefined') {
                    feather.replace();
                }
                bindPartialLinks();
            }

            function bindPartialLinks() {
                const links = document.querySelectorAll('a:not([target="_blank"]):not([href^="#"]):not([data-no-pjax])');
                links.forEach(link => {
                    if (link.dataset.partialBound) return;
                    
                    link.addEventListener('click', function(e) {
                        const href = this.getAttribute('href');
                        if (href && (href.startsWith('/') || href.startsWith(window.location.origin))) {
                            // Don't intercept if it's the same URL
                            if (href === window.location.href || href === window.location.pathname + window.location.search) {
                                e.preventDefault();
                                return;
                            }
                            e.preventDefault();
                            loadPage(href);
                        }
                    });
                    
                    link.dataset.partialBound = "true";
                });
            }

            // Handle browser back/forward
            window.addEventListener('popstate', function(e) {
                if (e.state && e.state.url) {
                    loadPage(e.state.url, false);
                } else {
                    window.location.reload();
                }
            });

            // Initial bind
            bindPartialLinks();

        });
    </script>

    @stack('scripts')

    {{-- SweetAlert2 is loaded via Vite bundle (window.Swal) --}}
</body>

</html>