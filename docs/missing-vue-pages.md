# Missing Vue Pages — Created

**Task name:** Create missing Vue pages

9 page components were missing (referenced by controllers but no `.vue` file existed). All are now created and `npm run build` passes.

---

## Store Pages (StoreLayout auto-assigned)

### `store/payment-failed.vue`
| | |
|---|---|
| **Route** | `GET /payment/{reference}/failed` — `route('store.payment.failed')` |
| **Controller** | `PaymentController@failed` |
| **Props** | `order` (BrandPartnerOrder) |
| **Purpose** | Shows payment failed message with retry/back-to-shop buttons |

---

## PDF Pages (MainLayout)

### `pdf/expense.vue`
| | |
|---|---|
| **Route** | `GET /admin/expenses/{expense}/pdf` |
| **Controller** | `ExpenseController@pdf` |
| **Props** | `expense` (Expense with supplier, lines, currency relations) |
| **Purpose** | Print-friendly expense report with print button |

---

## Brand Partner Pages

### `product/show.vue` (BrandPartnerLayout)
| | |
|---|---|
| **Route** | `GET /brand-partner/products/{product}` — `route('brand-partner.products.show', id)` |
| **Controller** | `BrandPartner\ProductController@show` |
| **Props** | `product` (BrandPartnerProduct with category, event, images, orderLines.order) |
| **Purpose** | Product detail view with info, images, order history table |

---

## Brand Partner Auth Pages (LoginLayout)

### `auth/forgot-password.vue`
| | |
|---|---|
| **Route** | `GET /brand-partner/forgot-password` — `route('brand-partner.password.request')` |
| **Controller** | `BrandPartnerAuth\PasswordResetLinkController@create` |
| **Props** | `status` (session flash, nullable) |
| **Form** | POST to `route('brand-partner.password.email')` |

### `auth/reset-password.vue`
| | |
|---|---|
| **Route** | `GET /brand-partner/reset-password/{token}` — `route('brand-partner.password.reset')` |
| **Controller** | `BrandPartnerAuth\NewPasswordController@create` |
| **Props** | `email`, `token` (string) |
| **Form** | POST to `route('brand-partner.password.store')` |

---

## Admin Auth Pages (LoginLayout) — No routes wired yet

These controllers exist but have no routes in `routes/admin/auth.php`:
- `AdminAuth\ConfirmablePasswordController` → `admin/auth/confirm-password.vue`
- `AdminAuth\NewPasswordController` → `admin/auth/reset-password.vue`
- `AdminAuth\EmailVerificationPromptController` → `admin/auth/verify-email.vue`
- `AdminAuth\PasswordResetLinkController` → `admin/auth/forgot-password.vue`

Created as stubs so they don't crash if routes are wired later.
