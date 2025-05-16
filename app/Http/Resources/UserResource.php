<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'verification_code' => $this->verification_code,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            "status" => $this->status,
            "phone" => $this->phone,
            "phone_verified_at" => $this->phone_verified_at,
            "subaccount_id" => $this->subaccount_id,
        ];
    }
}
