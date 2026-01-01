import './bootstrap';
import Echo from "laravel-echo";

if (import.meta.env.VITE_PUSHER_APP_KEY) {
    import("pusher-js").then(({ default: Pusher }) => {
        window.Pusher = Pusher;

        window.Echo = new Echo({
            broadcaster: "pusher",
            key: import.meta.env.VITE_PUSHER_APP_KEY,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
            forceTLS: true,
        });

        if (window.userId) {
            window.Echo.private(`App.Models.User.${window.userId}`)
                .notification((notification) => {
                    console.log("🔔 New Notification:", notification);
                });
        }
    });
}
