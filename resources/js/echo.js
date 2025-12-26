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
let notifAudio = null;

function initAudio() {
    if (notifAudio) return notifAudio;
    notifAudio = new Audio(notifTonePath);
    notifAudio.preload = 'auto';
    return notifAudio;
}

// "Unlock" audio on first user interaction to bypass browser autoplay policies
function unlockAudio() {
    const audio = initAudio();
    audio.play().then(() => {
        audio.pause();
        audio.currentTime = 0;
        document.removeEventListener('click', unlockAudio);
        document.removeEventListener('keydown', unlockAudio);
        console.log('Audio unlocked for notifications');
    }).catch(() => {
        // Still blocked, will try again on next interaction
    });
}

document.addEventListener('click', unlockAudio);
document.addEventListener('keydown', unlockAudio);

if (userId && window.Echo) {
    window.Echo.private(`App.Modules.User.Domain.Models.User.${userId}`)
        .notification((notification) => {
            console.log('Real-time Notification:', notification);

            // Play Sound
            const audio = initAudio();
            audio.play().catch(e => {
                console.warn('Audio playback blocked. Interract with the page to enable sound.', e);
            });

            // Trigger Global UI Updates (if functions exist)
            if (window.showToast) {
                window.showToast(notification.title || 'New Notification', 'success');
            }

            if (window.updateUnreadCount) {
                window.updateUnreadCount(false); // Update badge without another toast
            }

            if (window.fetchLatestNotifications) {
                window.fetchLatestNotifications();
            }
        });
}
