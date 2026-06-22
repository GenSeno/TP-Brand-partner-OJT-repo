# Fix CashFlowController Casing Mismatch

**Task name:** Fix CashFlowController casing

**Severity:** HIGH — will crash on Linux production (case-sensitive filesystem)

## Issue

In `routes/admin/finance.php`, the import uses `CashflowController` (lowercase `f`) but lines 55, 57, 61, 63 reference `CashFlowController` (uppercase `F`). On Windows the filesystem is case-insensitive so it works, but on Linux/Mac PSR-4 autoloading will fail with "Class not found" because it looks for `CashFlowController.php` which doesn't exist.

## Fix

Changed all 4 route references from `CashFlowController::class` to `CashflowController::class`.

## File Changed

`routes/admin/finance.php` — lines 55, 57, 61, 63
