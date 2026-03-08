<?php

namespace App\Models\Contract;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface OrderLinePrintable
{
    public function purchasable(): MorphTo;
}
