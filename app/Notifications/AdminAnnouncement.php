<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\Channel;


class AdminAnnouncement extends Notification implements ShouldQueue
{
    use Queueable;

    public $message;
    public $url;

    public function __construct(string $message, ?string $url = null)
    {
        $this->message = $message;
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->message,
            'url' => $this->url,
            'admin' => auth()->user()->full_name ?? auth()->user()->email ?? 'System',
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => $this->message,
            'url' => $this->url,
            'admin' => auth()->user()->full_name ?? 'System',
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    // ✅ Correct: no parameters, return the channel(s)
    public function broadcastOn()
    {
        return [
            // Laravel will automatically pass the notifiable into toBroadcast()
            // new PrivateChannel('App.Models.User.' . auth()->id()),
            new PrivateChannel('App.Models.User.' . $this->notifiable->id),

        ];
    }
}


