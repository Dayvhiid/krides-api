<?php namespace App\Notifications;

use App\Models\Trip;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TripAcceptedNotification extends Notification
{
    use Queueable;

    protected $trip;

    public function __construct(Trip $trip)
    {
        $this->trip = $trip;
    }

    public function via($notifiable)
    {
        return ['mail']; // or use 'database' or 'broadcast'
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Ride Has Been Accepted')
            ->line('Your ride request has been accepted by the driver.')
            ->action('View Trip', url('/trips/'.$this->trip->id));
    }
}
