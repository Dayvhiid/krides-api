<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'location' => $this->location,
            'destination' => $this->destination,

            'driver' => $this->when(
                !is_null($this->driver), // ? Correct: use boolean here
                fn () => [
                    'id' => $this->driver->id,
                    'name' => $this->driver->fullname,
                    'email' => $this->driver->email,
                ]
            ),

            'number_of_passengers' => $this->number_of_passengers,
            'rider_name' => $this->rider_name,
            'amount' => $this->amount,
            'status' => $this->status,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'phone' => $this->user->phone,
            'user_id' => $this->user->id,
        ];
    }
}

