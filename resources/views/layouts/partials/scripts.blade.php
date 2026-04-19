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
            fetch('{{ route('notifications.latest') }}', {
                headers: { 'Accept': 'application/json' }
            })
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
            // 1. Immediate Visual Feedback
            const dropdownItems = document.querySelectorAll('#notificationList a');
            dropdownItems.forEach(item => {
                if (item.onclick && item.onclick.toString().includes(id)) {
                    item.classList.add('opacity-60');
                    const dot = item.querySelector('.bg-primary-600');
                    if (dot) dot.classList.replace('bg-primary-600', 'bg-transparent');
                }
            });

            const mainItems = document.querySelectorAll('[onclick*="markAsReadLocal(\'' + id + '\'"]');
            mainItems.forEach(item => {
                item.classList.add('opacity-75');
                item.classList.remove('bg-primary-50/10', 'dark:bg-primary-900/5');
                const dot = item.querySelector('.bg-primary-600');
                if (dot) dot.remove();
            });

            // 2. Sanitize URL
            let targetUrl = url;
            if (targetUrl && (targetUrl !== 'null' && targetUrl !== 'undefined')) {
                if (targetUrl.includes('://')) {
                    try {
                        const urlObj = new URL(targetUrl);
                        if (urlObj.hostname === 'localhost' || urlObj.hostname === '127.0.0.1') {
                            targetUrl = urlObj.pathname + urlObj.search + urlObj.hash;
                        }
                    } catch (e) {
                        console.error('URL parsing failed', e);
                    }
                }
            } else {
                targetUrl = null;
            }

            // 3. Backend Request
            fetch('/notifications/mark-as-read/' + id, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).finally(function() {
                if (targetUrl) {
                    if (typeof loadPage === 'function' && (targetUrl.startsWith('/') || targetUrl.startsWith(window.location.origin))) {
                        loadPage(targetUrl);
                    } else {
                        window.location.href = targetUrl;
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
        window.updateUnreadCount = function(triggerToast = false) {
            fetch('{{ route('notifications.unread-count') }}', {
                headers: { 'Accept': 'application/json' }
            })
                .then(function(res) { return res.json(); })
                .then(function(data) {
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

        @auth
        window.updateUnreadCount(false);
        setInterval(function() { window.updateUnreadCount(true); }, 30000);
        @endauth

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
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: {{ Js::from(session('success')) }},
                confirmButtonColor: '#4f46e5',
                timer: 3000
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: {{ Js::from(session('error')) }},
                confirmButtonColor: '#ef4444'
            });
        @endif

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
            if (globalProgress) {
                globalProgress.style.opacity = '1';
                globalProgress.style.width = '30%';
            }
            if (headerContent) headerContent.style.opacity = '0.3';
            if (skeletonTemplate && mainContent) {
                mainContent.style.opacity = '0.5';
                mainContent.innerHTML = skeletonTemplate.innerHTML;
                setTimeout(() => {
                    mainContent.style.opacity = '1';
                }, 50);
            }
        }

        function hideLoader() {
            if (globalProgress) globalProgress.style.width = '100%';
            if (headerContent) headerContent.style.opacity = '1';
            setTimeout(() => {
                if (globalProgress) {
                    globalProgress.style.opacity = '0';
                    setTimeout(() => {
                        globalProgress.style.width = '0%';
                    }, 300);
                }
            }, 200);
        }

        async function loadPage(url, pushState = true) {
            showLoader();
            try {
                const response = await fetch(url, {
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html, application/xhtml+xml'
                    }
                });
                if (!response.ok) throw new Error('Network response was not ok');
                const html = await response.text();
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                const newTitle = doc.querySelector('title');
                if (newTitle) document.title = newTitle.innerText;

                const newHeader = doc.getElementById('header-content-area');
                if (newHeader && headerContent) headerContent.innerHTML = newHeader.innerHTML;

                const newMain = doc.getElementById('main-content-area');
                if (newMain) {
                    const currentLayout = document.querySelector('[data-layout]')?.dataset.layout;
                    const newLayout = newMain.dataset.layout || newMain.closest('[data-layout]')?.dataset.layout;
                    const currentHideSidebar = document.body.dataset.hideSidebar || 'false';
                    const newHideSidebar = doc.body.dataset.hideSidebar || 'false';
                    const currentHideHeader = document.body.dataset.hideHeader || 'false';
                    const newHideHeader = doc.body.dataset.hideHeader || 'false';
                    
                    if (currentLayout !== newLayout || currentHideSidebar !== newHideSidebar || currentHideHeader !== newHideHeader) {
                        window.location.href = url;
                        return;
                    }

                    mainContent.style.opacity = '0.7';
                    setTimeout(() => {
                        mainContent.innerHTML = newMain.innerHTML;
                        mainContent.style.opacity = '1';
                        const scripts = mainContent.querySelectorAll('script');
                        scripts.forEach(oldScript => {
                            const newScript = document.createElement('script');
                            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                            oldScript.parentNode.replaceChild(newScript, oldScript);
                        });
                        reinitializeUI();
                    }, 50);
                } else {
                    window.location.href = url;
                    return;
                }

                updateSidebarActive(url);
                updateBottomNavActive(url);
                if (pushState) window.history.pushState({ url }, '', url);
                reinitializeUI();
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
                    link.classList.add('bg-primary-600', 'text-white', 'shadow-lg', 'shadow-primary-600/20');
                    link.classList.remove('text-gray-500', 'dark:text-gray-400', 'hover:bg-gray-50', 'dark:hover:bg-gray-800', 'hover:text-primary-600', 'dark:hover:text-primary-400');
                    const iconBox = link.querySelector('div');
                    if (iconBox) {
                        iconBox.classList.add('bg-white/20');
                        iconBox.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'group-hover:bg-primary-50', 'dark:group-hover:bg-primary-900/20');
                    }
                    if (badge) {
                        badge.classList.add('hidden');
                        badge.style.display = 'none';
                        badge.innerText = '';
                    }
                } else {
                    link.classList.remove('bg-primary-600', 'text-white', 'shadow-lg', 'shadow-primary-600/20');
                    link.classList.add('text-gray-500', 'dark:text-gray-400', 'hover:bg-gray-50', 'dark:hover:bg-gray-800', 'hover:text-primary-600', 'dark:hover:text-primary-400');
                    const iconBox = link.querySelector('div');
                    if (iconBox) {
                        iconBox.classList.remove('bg-white/20');
                        iconBox.classList.add('bg-gray-100', 'dark:bg-gray-800', 'group-hover:bg-primary-50', 'dark:group-hover:bg-primary-900/20');
                    }
                }
            });
        }

        function updateBottomNavActive(currentUrl) {
            const navLinks = document.querySelectorAll('#bottomNav a');
            if (navLinks.length === 0) return;
            
            const urlObj = new URL(currentUrl, window.location.origin);
            const path = urlObj.pathname;

            navLinks.forEach(link => {
                const href = link.getAttribute('href');
                if (!href) return;
                
                let isActive = false;
                if (href === '/') {
                    if (path === '/' || path.startsWith('/market')) isActive = true;
                } else if (href.includes('/procurement/pr')) {
                    if (path.startsWith('/procurement/pr')) isActive = true;
                } else if (href.includes('/procurement/po')) {
                    if (path.startsWith('/procurement/po')) isActive = true;
                } else if (href.includes('/procurement/offers/negotiations')) {
                    if (path.startsWith('/procurement/offers/negotiations')) isActive = true;
                } else if (href.includes('/procurement/invoices')) {
                    if (path.startsWith('/procurement/invoices')) isActive = true;
                } else if (href.includes('/procurement/gr')) {
                    if (path.startsWith('/procurement/gr') || path.startsWith('/procurement/do')) isActive = true;
                }

                const icon = link.querySelector('i');
                const dot = link.querySelector('.nav-dot');

                if (isActive) {
                    link.classList.add('text-primary-600');
                    link.classList.remove('text-gray-400', 'hover:text-gray-600', 'dark:hover:text-gray-200');
                    if (icon) icon.classList.add('fill-primary-600/10');
                    if (dot) dot.classList.remove('hidden');
                } else {
                    link.classList.remove('text-primary-600');
                    link.classList.add('text-gray-400', 'hover:text-gray-600', 'dark:hover:text-gray-200');
                    if (icon) icon.classList.remove('fill-primary-600/10');
                    if (dot) dot.classList.add('hidden');
                }
            });
        }

        function reinitializeUI() {
            if (typeof feather !== 'undefined') feather.replace();
            bindPartialLinks();
        }

        function bindPartialLinks() {
            const links = document.querySelectorAll('a:not([target="_blank"]):not([href^="#"]):not([data-no-pjax])');
            links.forEach(link => {
                if (link.dataset.partialBound) return;
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    if (href && (href.startsWith('/') || href.startsWith(window.location.origin))) {
                        if (href.includes('/dashboard') || href.includes('/company-dashboard')) return;
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

        window.addEventListener('popstate', function(e) {
            if (e.state && e.state.url) {
                loadPage(e.state.url, false);
            } else {
                window.location.reload();
            }
        });

        bindPartialLinks();
    });
</script>
