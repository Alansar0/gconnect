<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WaitlistAvailable extends Notification
{
    use Queueable;

    public $waitlist;
    public $reservationMinutes;

    public function __construct($waitlist, $reservationMinutes = 5)
    {
        $this->waitlist = $waitlist;
        $this->reservationMinutes = $reservationMinutes;
    }

    public function via($notifiable)
    {
        return ['database','broadcast','mail'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => "Your voucher slot is available for {$this->reservationMinutes} minutes. Please complete purchase now.",
            'waitlist_id' => $this->waitlist->id,
            'expected_available_at' => now()->toDateTimeString(),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage(['data' => $this->toDatabase($notifiable)]);
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your voucher slot is available — claim now')
            ->line("Your voucher slot for profile: " . ($this->waitlist->profile->name ?? 'plan') . " is now available.")
            ->line("You have {$this->reservationMinutes} minutes to complete the purchase.")
            ->action('Buy Voucher', url('/get-voucher')) // adjust route
            ->line('Thanks for using our service!');
    }
}
