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
            'location' => $this->id,
            'destination' => $this->destination,
            'distance' => $this->distance,
            'userId' => $this->userId,
            'DriverId' => $this->driverId,
            'paymentStatus' => $this->paymentStatus,
            'vehicleId' => $this->vehicleId
        ];
    }
}
