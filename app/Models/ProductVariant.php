<?php

namespace App\Models;

use App\Actions\GenerateVariantSKU;
use App\Enums\OrderLineType;
use App\Enums\UomType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Lunar\Traits\LogsActivity;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @property int $id
 * @property int $product_id
 * @property int $unit_quantity
 * @property int $min_quantity
 * @property int $quantity_increment
 * @property string $sku
 * @property string $description
 * @property bool $enabled
 * @property bool $default
 * @property bool $has_transactions
 * @property ?string $uom_code
 * @property float $length_value
 * @property string $length_unit
 * @property float $width_value
 * @property string $width_unit
 * @property float $height_value
 * @property string $height_unit
 * @property float $weight_value
 * @property string $weight_unit
 * @property float $volume_value
 * @property string $volume_unit
 * @property bool $shippable
 * @property int $stock
 * @property int $backorder
 * @property bool $purchasable
 * @property ?string $barcode
 * @property bool $hidden
 * @property ?\Illuminate\Support\Carbon $created_at
 * @property ?\Illuminate\Support\Carbon $updated_at
 * @property ?\Illuminate\Support\Carbon $deleted_at
 */
class ProductVariant extends Model
{
    use LogsActivity;
    use SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'shippable' => 'boolean',
        'purchasable' => 'boolean',
        'enabled' => 'boolean',
        'default' => 'boolean',
        'hidden' => 'boolean',
        'has_transactions' => 'boolean',
    ];

    protected $appends = [
        'price',
    ];

    public function product(): Relations\BelongsTo
    {
        return $this->belongsTo(Product::class)->withTrashed();
    }

    public function values(): Relations\BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            foreignPivotKey: 'variant_id',
            relatedPivotKey: 'value_id'
        )->withTimestamps();
    }

    public function prices(): Relations\MorphMany
    {
        return $this->morphMany(
            Price::class,
            'priceable'
        );
    }

    public function price(): Attribute
    {
        return Attribute::get(
            get: fn() => $this->prices()->where('currency_id', Currency::getDefault()?->id)->first()
        );
    }

    public function orderLines(): Relations\MorphMany
    {
        return $this->morphMany(OrderLine::class, 'purchasable');
    }

    public function quoteLines(): Relations\MorphMany
    {
        return $this->morphMany(QuoteLine::class, 'purchasable');
    }

    public function checkTransactions(): void
    {
        if (
            !$this->has_transactions
            && ($this->orderLines()->exists() || $this->quoteLines()->exists())
        ) {
            $this->update(['has_transactions' => true]);
        }
    }

    public function getType(): OrderLineType
    {
        return $this->shippable ? OrderLineType::PHYSICAL : OrderLineType::DIGITAL;
    }

    public function getOption(): string
    {
        return $this->values->join(', ');
    }

    public function getIdentifier(): string
    {
        return $this->sku;
    }

    public function image(): Relations\BelongsTo
    {
        return $this->belongsTo(Media::class); // media comes from product images
    }

    public function unitMeasure(): Relations\BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'uom', 'code');
    }

    public function uom(): Relations\BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'uom_code', 'code')
            ->where('type', UomType::COUNT);
    }

    public function lengthUom(): Relations\BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'length_unit', 'code')
            ->where('type', UomType::LENGTH);
    }

    public function heightUom(): Relations\BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'height_unit', 'code')
            ->where('type', UomType::LENGTH);
    }

    public function widthUom(): Relations\BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'width_unit', 'code')
            ->where('type', UomType::LENGTH);
    }

    public function weightUom(): Relations\BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'weight_unit', 'code')
            ->where('type', UomType::WEIGHT);
    }

    public function volumeUom(): Relations\BelongsTo
    {
        return $this->belongsTo(UnitMeasure::class, 'volume_unit', 'code')
            ->where('type', UomType::VOLUME);
    }

    public static function generate(Collection $options, Product $product)
    {
        if ($options->isEmpty()) {
            return;
        }

        $product->options()->sync($options);

        $sets = $options->map(fn($opt) => collect($opt->values));
        $combinations = $sets->shift()->crossJoin(...$sets);

        $combinations->each(function ($combination) use ($product) {
            $combination = collect($combination);
            $exists = $product->variants()
                ->whereHas('values', function ($query) use ($combination) {
                    $query->whereIn('value_id', $combination->pluck('id'));
                }, '=', $combination->count())
                ->exists();

            if (!$exists) {
                $variant = $product->variants()->create([
                    'sku' => GenerateVariantSKU::run($product->id, $combination),
                    'description' => $combination->pluck('label')->join(' / '),
                ]);

                $variant->values()->sync(
                    $combination->pluck('id')
                );
            }
        });
    }

    public static function createByOptions(Collection $options)
    {
        if ($options->isEmpty()) {
            return;
        }

        $optionIds = $options->pluck('id');

        $products = Product::whereHas('options', function ($q) use ($optionIds) {
            $q->whereIn('product_option_product.id', $optionIds);
        }, '<', $optionIds->count())->get();

        $products->each(function ($product) use ($options) {
            static::generate($options, $product);
        });
    }

    public static function createByProduct(Product $product)
    {
        $options = ProductOption::autoApplied()
            ->orWhereHas('products', function ($q) use ($product) {
                $q->where('products.id', $product->id);
            })
            ->orderBy('position')->get();
        static::generate($options, $product);
    }

    public static function checkAndGenerate()
    {
        $options = ProductOption::autoApplied()->orderBy('position')->get();
        static::createByOptions($options);
    }

    public function scopeEnabled(Builder $query, bool $enabled = true): Builder
    {
        return $query->where('enabled', $enabled);
    }

    public function scopeHidden(Builder $query, bool $hidden = true): Builder
    {
        return $query->where('hidden', $hidden);
    }
}
