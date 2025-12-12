<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class WaitlistJoined extends Notification
{
    use Queueable;

    public $waitlist;

    public function __construct($waitlist)
    {
        $this->waitlist = $waitlist;
    }

    public function via($notifiable)
    {
        return ['database','broadcast']; // no mail to avoid spam for join
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "You're on the waitlist. Expected availability: " . $this->waitlist->expected_available_at->toDateTimeString(),
            'waitlist_id' => $this->waitlist->id,
            'expected_available_at' => $this->waitlist->expected_available_at->toDateTimeString(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage(['data' => $this->toDatabase($notifiable)]);
    }
}
