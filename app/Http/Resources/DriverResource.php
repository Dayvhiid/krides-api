<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'fullname' => $this->fullname,
            'vehicle_id' => $this->vehicle_id,
            'picture' => $this->picture,
            'phone' => $this->phone,
        ];
    }
}
