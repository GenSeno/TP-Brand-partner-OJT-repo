<?php

namespace App\Models\Contract;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

interface OrderPrintable
{
    public function productLines(): HasMany;

    public function printLines(): MorphMany;
}
