
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

/**
 * Real-time Notifications & Audio Feedback
 */
const userId = document.querySelector('meta[name="user-id"]')?.getAttribute('content');
const notifTonePath = '/assets/tone/notification/notif-tone.mp3';

// Audio element singleton
const notifAudio = new Audio(notifTonePath);
notifAudio.preload = 'auto';

// Debug: Check if audio loads
notifAudio.addEventListener('canplaythrough', () => console.log('Notification tone loaded successfully.'));
notifAudio.addEventListener('error', (e) => console.error('Error loading notification tone:', e));

// Unlock Audio Context on first interaction
// Browsers require a user gesture to allow audio playback
const unlockAudio = () => {
    notifAudio.play().then(() => {
        notifAudio.pause();
        notifAudio.currentTime = 0;
        console.log('Audio unlocked successfully.');
        
        // Remove listeners ONLY after successful unlock
        document.removeEventListener('click', unlockAudio);
        document.removeEventListener('keydown', unlockAudio);
        document.removeEventListener('touchstart', unlockAudio);
    }).catch((e) => {
        // Keep listeners attached if unlock failed (e.g. browser policy), so we can try again on next interaction
        // Using debug instead of warn to reduce console noise for expected behavior
        console.debug('Audio unlock attempt failed, retrying on next interaction...');
    });
};

// Listen for any interaction
document.addEventListener('click', unlockAudio);
document.addEventListener('keydown', unlockAudio); // Also listen for keys
document.addEventListener('touchstart', unlockAudio);

// Expose for manual triggering
window.unlockAudioManual = unlockAudio;

// Helper to fetch latest count (as fallback/sync)
const fetchUnreadCount = async () => {
    try {
        const response = await fetch('/notifications/unread-count', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        const data = await response.json();
        
        // Use the global updater from app.blade.php if available to sync everything
        if (typeof window.updateUnreadCount === 'function') {
            window.updateUnreadCount(false);
        }
    } catch (error) {
        console.error('Failed to fetch notification count:', error);
    }
};

if (userId) {
    window.Echo.private(`users.${userId}`)
        .notification((notification) => {
            console.log('Real-time Notification Received:', notification);

            // Play Sound with Autoplay Handling
            const playSound = async () => {
                try {
                    // Resetting the audio helps if it got stuck
                    notifAudio.currentTime = 0;
                    const prom = notifAudio.play();
                    
                    if (prom !== undefined) {
                        await prom;
                    }
                } catch (error) {
                    console.warn('Autoplay prevented:', error);
                    
                    // Show a specific toast to encourage interaction
                    if (typeof window.showToast === 'function') {
                       if (typeof Swal !== 'undefined') {
                           const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 5000,
                                timerProgressBar: true
                            });
                            Toast.fire({
                                icon: 'warning',
                                title: 'Click here to enable sound',
                                didOpen: (toast) => {
                                    toast.addEventListener('click', () => {
                                        unlockAudio();
                                        notifAudio.play();
                                    });
                                }
                            });
                       }
                    }
                }
            };
            
            // Allow testing from console or UI
            window.testNotificationSound = playSound;
            
            playSound();

            // Trigger Global UI Updates
            
            // 1. Toast Notification
            if (typeof window.showToast === 'function') {
                window.showToast(notification.message || notification.title || 'New Notification', 'success');
            }

            // 2. Fetch fresh count & update badge
            if (typeof window.fetchLatestNotifications === 'function') {
                window.fetchLatestNotifications();
            }
            if (typeof window.updateUnreadCount === 'function') {
                window.updateUnreadCount(false);
            }
        });
}
