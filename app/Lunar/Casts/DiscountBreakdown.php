<?php

namespace App\Lunar\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Contracts\Database\Eloquent\SerializesCastableAttributes;
use App\Lunar\ValueObjects\Cart\DiscountBreakdownLine;
use App\Lunar\DataTypes\Price;
use App\Models\Currency;
use Spatie\LaravelBlink\BlinkFacade;

class DiscountBreakdown implements CastsAttributes, SerializesCastableAttributes
{
    /**
     * Cast the given value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return \App\Lunar\ValueObjects\Cart\DiscountBreakdown
     */
    public function get($model, $key, $value, $attributes)
    {
        $breakdown = new \App\Lunar\ValueObjects\Cart\DiscountBreakdown;
        $breakdown->amounts = collect(
            json_decode($value, false)
        )->mapWithKeys(function ($amount, $key) {
            $currency = BlinkFacade::once("currency_{$amount->currency_code}", function () use ($amount) {
                return Currency::whereCode($amount->currency_code)->first();
            });

            return [
                $key => new DiscountBreakdownLine(
                    price: new Price($amount->value, $currency),
                    identifier: $amount->identifier,
                    description: $amount->description,
                    percentage: $amount->percentage ?? null,
                    amount: $amount->amount ?? null,
                    type: $amount->type ?? 'fixed',
                ),
            ];
        });

        return $breakdown;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  Price  $value
     * @param  array  $attributes
     * @return array
     *
     * @throws \Exception
     */
    public function set($model, $key, $value, $attributes)
    {
        if ($value && !is_a($value, \App\Lunar\ValueObjects\Cart\DiscountBreakdown::class)) {
            throw new \Exception('Discount breakdown must be instance of App\\Lunar\\ValueObjects\\Cart\\DiscountBreakdown');
        }

        if (!$value) {
            return [];
        }

        return [
            $key => $value->amounts->map(function ($item) {
                return [
                    'description' => $item->description,
                    'identifier' => $item->identifier,
                    'percentage' => $item->percentage,
                    'amount' => $item->amount,
                    'type' => $item->type,
                    'value' => $item->price->value,
                    'currency_code' => $item->price->currency->code,
                ];
            })->toJson(),
        ];
    }

    /**
     * Get the serialized representation of the value.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array<string, mixed>  $attributes
     */
    public function serialize($model, $key, $value, $attributes)
    {
        if (!$value) {
            return null;
        }

        return $value->amounts->map(fn($item) => [
            'description' => $item->description,
            'identifier' => $item->identifier,
            'percentage' => $item->percentage,
            'amount' => $item->amount,
            'type' => $item->type,
            'value' => $item->price->value,
            'currency_code' => $item->price->currency->code,
        ])->values()->toArray();
    }
}
