# Remove Commented `dd()` in QuoteController

**Task name:** Remove commented dd() in QuoteController

**Severity:** LOW

## Issue

Leftover commented debug code at `QuoteController.php:560`:
```php
// dd(  $quotation->discount_breakdown );
```

## Fix

Removed the commented line.

## File Changed

`app/Http/Controllers/Admin/QuoteController.php` — line 560
