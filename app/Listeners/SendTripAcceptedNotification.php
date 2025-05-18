<?php namespace App\Listeners;

use App\Events\TripAcceptedEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Notifications\TripAcceptedNotification;

class SendTripAcceptedNotification
{
    public function handle(TripAcceptedEvent $event)
    {
        $user = $event->trip->user;

        if ($user) {
            Notification::send($user, new TripAcceptedNotification($event->trip));
        }

        Log::info("Trip ID {$event->trip->id} has been accepted by driver.");
    }
}
