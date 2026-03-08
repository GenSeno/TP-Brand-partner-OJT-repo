<?php

namespace App\Lunar\DataTypes;

use App\Lunar\Exceptions\InvalidDataTypeValueException;
use App\Models\Currency;
use App\Lunar\Pricing\DefaultPriceFormatter;
use JsonSerializable;

class Price implements JsonSerializable
{
    /**
     * Initialise the Price datatype.
     *
     * @throws InvalidDataTypeValueException
     */
    public function __construct(
        public mixed $value,
        public ?Currency $currency = null,
        public int $unitQty = 1
    ) {
        $this->currency = $currency ?: Currency::getDefault();
        if (!is_int($value)) {
            throw new InvalidDataTypeValueException(
                'Value was "' . (gettype($value)) . '" expected "int"'
            );
        }
    }

    /**
     * Getter for methods/properties.
     *
     * @param  string  $name
     * @return void
     */
    public function __get($name)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}();
        }
    }

    /**
     * Cast class as a string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value;
    }

    private function formatter()
    {
        return app(
            config('lunar.pricing.formatter', DefaultPriceFormatter::class),
            [
                'value' => $this->value,
                'currency' => $this->currency,
                'unitQty' => $this->unitQty,
            ]
        );
    }

    /**
     * Get the decimal value.
     */
    public function decimal(...$arguments): float
    {
        return $this->formatter()->decimal(...$arguments);
    }

    /**
     * Get the decimal unit value.
     */
    public function unitDecimal(...$arguments): float
    {
        return $this->formatter()->unitDecimal(...$arguments);
    }

    /**
     * Format the value with the currency.
     *
     * @return string
     */
    public function formatted(...$arguments): mixed
    {
        return $this->formatter()->formatted(...$arguments);
    }

    /**
     * Format the unit value with the currency.
     *
     * @return string
     */
    public function unitFormatted(...$arguments): mixed
    {
        return $this->formatter()->unitFormatted(...$arguments);
    }

    protected function formatValue(int|float $value, ...$arguments): mixed
    {
        return $this->formatter()->formatValue($value, ...$arguments);
    }

    /**
     * Specify how the object should be serialized to JSON
     */
    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'decimal' => $this->decimal(),
            'formatted' => $this->formatted(),
            'currency' => $this->currency->code,
            'unit_qty' => $this->unitQty,
        ];
    }
}
