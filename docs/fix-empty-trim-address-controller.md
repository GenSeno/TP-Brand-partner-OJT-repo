# Fix `empty(trim())` Imprecision in AddressController

**Task name:** Fix empty(trim()) in AddressController

**Severity:** LOW-MEDIUM

## Issue

Three methods in `Admin\AddressController` use `empty(trim($value))` which is fragile:
- `empty()` is a language construct that works best with variables, not function return values
- If the input is `null`, `trim(null)` returns `""` then `empty("")` returns `true` — works by coincidence
- If the input happens to be `"0"`, `empty("0")` incorrectly returns `true` (valid ID)

## Fix

Replaced `empty(trim($input))` with `! $request->filled($key)` which is the proper Laravel way to check if an input is present and non-empty.

**Before:**
```php
$country_id = $request->input('country_id');
if (empty(trim($country_id))) { return []; }
```

**After:**
```php
if (! $request->filled('country_id')) { return []; }
```

## File Changed

`app/Http/Controllers/Admin/AddressController.php` — lines 19, 45, 57
