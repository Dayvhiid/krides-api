<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TripRequested implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trip;

    public function __construct($trip)
    {
        $this->trip = $trip;
    }

    public function broadcastOn()
    {
        return new Channel('trips');
    }

    public function broadcastWith()
    {
        return [
            'user_id' => $this->trip->user_id,
            'pickup' => $this->trip->location,
            'destination' => $this->trip->destination,
        ];
    }
}
