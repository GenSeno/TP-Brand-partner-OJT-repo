<?php

namespace App\Models;

use App\Models\BrandPartnerProductImage;
use App\Enums\BrandPartnerProductApprovalStatus;
use App\Enums\BrandPartnerProductStatus;
use App\Lunar\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property int $brand_partner_id
 * @property int $category_id
 * @property ?int $event_id
 * @property string $name
 * @property string $slug
 * @property ?string $description
 * @property ?string $short_description
 * @property int $price
 * @property ?int $compare_price
 * @property ?string $sku
 * @property int $stock
 * @property bool $track_stock
 * @property BrandPartnerProductStatus $status
 * @property BrandPartnerProductApprovalStatus $approval_status
 * @property ?string $approval_notes
 * @property ?\Illuminate\Support\Carbon $approved_at
 * @property bool $featured
 * @property ?array $meta
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
class BrandPartnerProduct extends Model
{
    use HasFactory;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'brand_partner_id',
        'category_id',
        'collection_id',
        'event_id',
        'name',
        'slug',
        'description',
        'short_description',
        'price',
        'compare_price',
        'sku',
        'colors',
        'sizes',
        'stock',
        'track_stock',
        'status',
        'approval_status',
        'approval_notes',
        'approved_at',
        'featured',
        'meta',
    ];

    protected $casts = [
        'brand_partner_id' => 'integer',
        'category_id'      => 'integer',
        'collection_id'    => 'integer',
        'event_id'         => 'integer',
        'price' => 'integer',
        'compare_price' => 'integer',
        'stock' => 'integer',
        'track_stock' => 'boolean',
        'status'          => BrandPartnerProductStatus::class,
        'approval_status' => BrandPartnerProductApprovalStatus::class,
        'approved_at'     => 'datetime',
        'featured'        => 'boolean',
        'meta' => AsArrayObject::class,
    ];

    protected $appends = [
        'formatted_price',
        'in_stock',
        'image_url',
        'colors_array',
        'sizes_array',
    ];

    // Relationships
    public function brandPartner(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartner::class);
    }

    public function category(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartnerCategory::class, 'category_id');
    }

    public function collection(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartnerProductOptionValue::class, 'collection_id');
    }

    public function event(): Relations\BelongsTo
    {
        return $this->belongsTo(BrandPartnerEvent::class, 'event_id');
    }

    public function images(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerProductImage::class, 'product_id')->orderBy('position');
    }

    public function primaryImage(): Relations\HasOne
    {
        return $this->hasOne(BrandPartnerProductImage::class, 'product_id')
            ->where('is_primary', true)
            ->withDefault(function () {
                return $this->images()->first();
            });
    }

    public function orderLines(): Relations\HasMany
    {
        return $this->hasMany(BrandPartnerOrderLine::class, 'product_id');
    }

    // Accessors

    protected function formattedPrice(): Attribute
    {
        return Attribute::make(
            get: fn() => number_format($this->price / 100, 2),
        );
    }

    protected function inStock(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->isInStock(),
        );
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if ($this->relationLoaded('images')) {
                    $primary = $this->images->firstWhere('is_primary', true) ?? $this->images->first();
                    return $primary?->url;
                }
                return $this->primaryImage?->url;
            },
        );
    }

    protected function colorsArray(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->colors
                ? array_values(array_filter(array_map('trim', explode(',', $this->colors))))
                : [],
        );
    }

    protected function sizesArray(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->sizes
                ? array_values(array_filter(array_map('trim', explode(',', $this->sizes))))
                : [],
        );
    }

    // Scopes
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', BrandPartnerProductStatus::PUBLISHED);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeStatus(Builder $query, BrandPartnerProductStatus|string $status): Builder
    {
        $status = $status instanceof BrandPartnerProductStatus ? $status : BrandPartnerProductStatus::from($status);
        return $query->where('status', $status);
    }

    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeForEvent(Builder $query, int $eventId): Builder
    {
        return $query->where('event_id', $eventId);
    }

    public function scopeSearch(Builder $query, $value): Builder
    {
        if (!trim($value)) {
            return $query;
        }

        return $query->where(function ($q) use ($value) {
            $q->where('name', 'like', "%{$value}%")
              ->orWhere('sku', 'like', "%{$value}%");
        });
    }

    // Methods
    public function isPublished(): bool
    {
        return $this->status === BrandPartnerProductStatus::PUBLISHED;
    }

    public function isDraft(): bool
    {
        return $this->status === BrandPartnerProductStatus::DRAFT;
    }

    public function isDisabled(): bool
    {
        return $this->status === BrandPartnerProductStatus::DISABLED;
    }

    public function isPendingApproval(): bool
    {
        return $this->approval_status === BrandPartnerProductApprovalStatus::PENDING;
    }

    public function isApproved(): bool
    {
        return $this->approval_status === BrandPartnerProductApprovalStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->approval_status === BrandPartnerProductApprovalStatus::REJECTED;
    }

    public function canBePublished(): bool
    {
        return $this->approval_status === BrandPartnerProductApprovalStatus::APPROVED;
    }

    public function isInStock(): bool
    {
        if (!$this->track_stock) {
            return true;
        }

        return $this->stock > 0;
    }

    public function decrementStock(int $quantity): void
    {
        if ($this->track_stock) {
            $this->decrement('stock', $quantity);
        }
    }

    public function toggleStatus(): bool
    {
        if ($this->status === BrandPartnerProductStatus::DISABLED) {
            // Only allow toggling back to Published if approved
            $this->status = $this->canBePublished()
                ? BrandPartnerProductStatus::PUBLISHED
                : BrandPartnerProductStatus::DRAFT;
        } else {
            $this->status = BrandPartnerProductStatus::DISABLED;
        }

        return $this->save();
    }
}
