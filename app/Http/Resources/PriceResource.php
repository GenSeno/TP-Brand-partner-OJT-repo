<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Price
 */
class PriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'intval' => $this->amount->value,
            'amount' => $this->amount->decimal(),
            'formatted' => $this->amount->formatted(),
            'currency_code' => $this->currency->code,
            'currency_symbol' => $this->currency->symbol,
            'compare_amount' => $this->compare_amount,
            'min_quantity' => $this->min_quantity,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
