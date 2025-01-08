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
            // 'distance' => $this->distance,
            // 'userId' => $this->userId,
            // 'DriverId' => $this->driverId,
            // 'paymentStatus' => $this->paymentStatus,
            // 'vehicleId' => $this->vehicleId,
            // 'user_id' => $this->user_id,
            // 'status' => $this->status
            'number_of_passengers' => $this->number_of_passengers,
            'rider_name' => $this->rider_name,
            'amount' => $this->amount
        ];
    }
}
