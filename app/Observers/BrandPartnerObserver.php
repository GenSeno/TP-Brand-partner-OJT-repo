<?php

namespace App\Observers;

use App\Models\BrandPartner;
use Illuminate\Support\Str;

class BrandPartnerObserver
{
    /**
     * Handle the BrandPartner "creating" event.
     */
    public function creating(BrandPartner $brandPartner): void
    {
        if (empty($brandPartner->slug)) {
            $brandPartner->slug = $this->generateUniqueSlug($brandPartner->name);
        }
    }

    /**
     * Handle the BrandPartner "updating" event.
     */
    public function updating(BrandPartner $brandPartner): void
    {
        if ($brandPartner->isDirty('name') && !$brandPartner->isDirty('slug')) {
            $brandPartner->slug = $this->generateUniqueSlug($brandPartner->name, $brandPartner->id);
        }
    }

    /**
     * Generate a unique slug for the brand partner.
     */
    protected function generateUniqueSlug(string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        $query = BrandPartner::withTrashed()->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $query = BrandPartner::withTrashed()->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
            $count++;
        }

        return $slug;
    }
}
