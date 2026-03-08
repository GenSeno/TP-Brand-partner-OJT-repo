<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressesResource extends JsonResource
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
            
            // Address lines
            'line1' => $this->line1,
            'line2' => $this->line2,
            
            // Location details
            'city' => $this->city,
            'province' => $this->province,
            'barangay' => $this->barangay,
            'postcode' => $this->postcode,
            'country_id' => $this->country_id,
            
            // Contact information (email and phone stored here)
            'email' => $this->email,
            'phone' => $this->phone,
            
            // Country relationship
            'country' => new CountryResource($this->whenLoaded('country')),
            
            // Address type (if you have multiple addresses)
            'type' => $this->type ?? 'primary',
            'default' => $this->default ?? 1,
            
            // Timestamps
            'created_at' => $this->created_at?->toDateTimeString(),
            'created_at_formatted' => $this->created_at?->format('M d, Y'),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            
            // Formatted address
            'formatted_address' => $this->getFormattedAddressAttribute(),
        ];
    }
}