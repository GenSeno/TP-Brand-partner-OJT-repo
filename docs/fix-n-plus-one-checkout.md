# Fix N+1 Queries in BrandPartnerCheckoutController

**Task name:** Fix N+1 queries in checkout

**Severity:** MEDIUM

## Issue

Two `foreach` loops in `BrandPartnerCheckoutController@store` (lines 182-186 and 225-229) query products one-by-one inside the loop, causing N+1 database queries per cart item.

**Before:**
```php
// Loop 1 — queries individually
foreach ($cartItems as $item) {
    $product = BrandPartnerProduct::where('id', $item['product_id'])
        ->where('brand_partner_id', $brandPartner->id)
        ->where('status', BrandPartnerProductStatus::PUBLISHED)->first();
    // ...
}

// Loop 2 — queries individually again
foreach ($cartItems as $item) {
    $product = BrandPartnerProduct::find($item['product_id']);
    // ...
}
```

## Fix

- **Loop 1:** Load all products with a single `whereIn()` query before the loop, keyed by ID for O(1) lookup.
- **Loop 2:** Reuse the products already loaded from Loop 1 instead of re-querying.

**After:**
```php
$productIds = array_column($cartItems, 'product_id');
$products = BrandPartnerProduct::whereIn('id', $productIds)
    ->where('brand_partner_id', $brandPartner->id)
    ->where('status', BrandPartnerProductStatus::PUBLISHED)
    ->get()
    ->keyBy('id');

foreach ($cartItems as $item) {
    $product = $products->get($item['product_id']);
    if (! $product) continue;
    // ...
}

// Loop 2 reuses $products from above
foreach ($cartItems as $item) {
    $product = $products->get($item['product_id']);
    if ($product) {
        $product->decrementStock(...);
    }
}
```

## File Changed

`app/Http/Controllers/Store/BrandPartnerCheckoutController.php` — lines 182-231
