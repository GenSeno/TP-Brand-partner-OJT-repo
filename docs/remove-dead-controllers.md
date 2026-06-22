# Remove Dead Controllers

**Task name:** Remove dead controllers

**Severity:** LOW

## Issue

Two controller files exist but are never referenced by any route, import, or template:

1. **`Store\BrandPartnerLoginController.php`** — has an `index()` method rendering a `login` page, but the store already uses `Store\AuthController` for login. Zero references in the codebase.
2. **`Store\EventsController.php`** — empty class skeleton with no methods. Zero references in the codebase.

## Fix

Deleted both files.

## Files Removed

- `app/Http/Controllers/Store/BrandPartnerLoginController.php`
- `app/Http/Controllers/Store/EventsController.php`
