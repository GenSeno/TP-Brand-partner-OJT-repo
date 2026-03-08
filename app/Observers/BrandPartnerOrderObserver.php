<?php

namespace App\Observers;

use App\Models\BrandPartnerOrder;

class BrandPartnerOrderObserver
{
    /**
     * Handle the BrandPartnerOrder "created" event.
     */
    public function created(BrandPartnerOrder $order): void
    {
        if (empty($order->reference)) {
            $order->updateQuietly([
                'reference' => $this->generateReference($order),
            ]);
        }
    }

    /**
     * Generate a unique reference for the brand partner order.
     * Format: BP{brand_partner_id}-{year}{month}-{padded_id}
     */
    protected function generateReference(BrandPartnerOrder $order): string
    {
        $prefix = 'BP' . $order->brand_partner_id;
        $date = now()->format('ym');
        $paddedId = str_pad($order->id, 5, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$paddedId}";
    }
}
