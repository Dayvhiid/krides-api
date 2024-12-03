<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TripUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $trip;

    public function __construct($trip)
    {
        $this->trip = $trip;
    }

    public function broadcastOn()
    {
        return new Channel('trips.'.$this->trip['id']);
    }

    public function broadcastAs()
    {
        return 'trip.updated';
    }
}
