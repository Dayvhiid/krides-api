<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RideResource extends JsonResource
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
            'distance' => $this->distance,
            'DriverId' => $this->driverId,
            'paymentStatus' => $this->paymentStatus,
            'vehicleId' => $this->vehicleId,
            'driver_id' => $this->driver_id,
        ];
    }
}
