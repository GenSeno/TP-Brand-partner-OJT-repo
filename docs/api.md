# Internal API — TPInkAdmin Integration

These endpoints are consumed by the **TPInkAdmin** system (a separate admin app). All requests require the `X-API-Key` header matching `TPINKLAB_API_KEY` in `.env`.

---

## Authentication

All endpoints (except the Xendit webhook) require this header:

```
X-API-Key: {your_api_key}
```

If the key is missing or wrong, the API returns `401 Unauthorized`.

---

## Products

### `GET /api/products`
List all brand partner products with optional filtering.

**Query Parameters:**

| Param | Type | Description |
|---|---|---|
| `status` | string | Filter by status (`published`, `draft`, `disabled`) |
| `approval_status` | string | Filter by approval (`pending`, `approved`, `rejected`) |
| `brand_partner_id` | int | Filter by brand partner |
| `search` | string | Search by product name or SKU |
| `per_page` | int | Items per page (default: 15) |

**Response:** Paginated JSON with products including relations: `category`, `event`, `images`, `brandPartner`.

### `GET /api/products/{id}`
Get a single product by ID.

**Response:** `{ data: { ...product with relations... } }`

---

## Product Approval

### `POST /api/products/{product}/approval-status`
Receive an approval/rejection decision from TPInkAdmin.

**Body:**

| Field | Type | Description |
|---|---|---|
| `approval_status` | string | `approved` or `rejected` |
| `approval_notes` | string | Optional notes (max 1000 chars) |

**Behavior:**
- Sets `approval_status`, `approval_notes`, and `approved_at` (if approved).
- If rejected and product was `published`, reverts it to `draft`.

---

## Pre-Orders

### `GET /api/pre-orders`
List all pre-order items grouped by product.

**Response:** Grouped data with:
- `product_id`, `product_name`, `product_sku`
- `total_quantity` per product
- `variants` — color/size breakdown with quantities
- `orders` — list of customer orders with shipping address, quantity, status
- `meta.total_pre_order_quantity`, `total_products`, `total_orders`

---

## Webhooks (No Auth)

### `POST /api/webhook/xendit`
Xendit payment callback. No `X-API-Key` required.
