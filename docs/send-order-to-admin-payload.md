# Production Sales Order Payload

**Task name:** Update Production SO Payload

Updated `TpinkLabService::buildAdminOrderPayload()` to send complete product and order data needed by the production team to create Sales Orders (SO).

---

## Endpoint

`POST {TPINKLAB_ADMIN_API_URL}` (default: `http://localhost:8000/api/orders`)

**Header:** `X-API-Key: {TPINKLAB_API_KEY}`

**Triggered by:**
- Manual "Add New Order" button in brand partner dashboard
- Order `confirm()` action
- Pre-order checkout

---

## Full Payload Structure

```json
{
  "brand_partner": {
    "id": 1,
    "slug": "pakaras",
    "name": "Tribu Pakaras",
    "email": "partner@pakaras.com"
  },
  "order": {
    "reference": "BP1-2506-0001",
    "status": "pending",
    "payment_status": "paid",
    "placed_at": "2025-06-21T10:30:00+08:00",
    "notes": "Rush order",
    "jo_number": null,
    "jo_status": null,
    "has_pre_order": false,
    "total_quantity": 50,
    "pre_order_qty": 0
  },
  "customer": {
    "name": "Juan Dela Cruz",
    "email": "juan@example.com",
    "phone": "09171234567",
    "first_name": "Juan",
    "last_name": "Dela Cruz"
  },
  "address": {
    "address_line1": "123 Rizal St",
    "address_line2": "Brgy. Poblacion",
    "barangay": "Poblacion",
    "city": "Makati",
    "province": "Metro Manila",
    "postcode": "1200",
    "full": "123 Rizal St, Brgy. Poblacion, Makati, Metro Manila, 1200"
  },
  "totals": {
    "sub_total": 1500000,
    "tax_total": 0,
    "total": 1500000,
    "formatted_total": "15,000.00"
  },
  "items": [
    {
      "line_id": 1,
      "product_id": 5,
      "name": "Classic T-Shirt",
      "sku": "CTS-001",
      "quantity": 25,
      "unit_price": 30000,
      "total": 750000,
      "color": "Red",
      "size": "XL",
      "pre_order": false,
      "product": {
        "description": "<p>Premium quality cotton t-shirt</p>",
        "short_description": "Premium cotton tee",
        "price": 30000,
        "compare_price": 35000,
        "stock": 100,
        "track_stock": true,
        "colors": ["Red", "Blue", "Black"],
        "sizes": ["S", "M", "L", "XL"],
        "variants": [
          { "color": "Red", "size": "XL", "stock": 25 },
          { "color": "Blue", "size": "XL", "stock": 30 }
        ],
        "status": "published",
        "approval_status": "approved",
        "images": [
          { "url": "https://.../image.jpg", "is_primary": true, "position": 0 }
        ]
      }
    }
  ]
}
```

---

## Fields Reference

### `brand_partner`
| Field | Source | Description |
|---|---|---|
| `id` | `$order->brand_partner_id` | Brand partner ID |
| `slug` | `$order->brandPartner->slug` | Partner slug for routing |
| `name` | `$order->brandPartner->name` | Partner display name |
| `email` | `$order->brandPartner->email` | Partner contact email |

### `order`
| Field | Source | Description |
|---|---|---|
| `reference` | `$order->reference` | Order reference code |
| `status` | `$order->status->value` | Order status (enum) |
| `payment_status` | `$order->payment_status` | Payment status string |
| `placed_at` | `$order->placed_at` | ISO 8601 timestamp |
| `notes` | `$order->notes` | Order notes |
| `jo_number` | `$order->jo_number` | Job Order number (set by production) |
| `jo_status` | `$order->jo_status` | Job Order status (set by production) |
| `has_pre_order` | `$order->has_pre_order` | Whether any item is a pre-order |
| `total_quantity` | `$order->total_quantity` | Sum of all line quantities |
| `pre_order_qty` | `$order->pre_order_quantity` | Sum of pre-order quantities |

### `customer`
| Field | Source | Description |
|---|---|---|
| `name` | `$order->customer_name` | Full customer name |
| `email` | `$order->customer_email` | Customer email |
| `phone` | `$order->customer_phone` | Customer phone |
| `first_name` | Parsed from `customer_name` | First name |
| `last_name` | Parsed from `customer_name` | Last name |

### `address`
| Field | Source | Description |
|---|---|---|
| `address_line1` | `$order->address_line1` | Street address |
| `address_line2` | `$order->address_line2` | Secondary address line |
| `barangay` | `$order->barangay` | Barangay/district |
| `city` | `$order->city` | City/municipality |
| `province` | `$order->province` | Province/state |
| `postcode` | `$order->postcode` | Postal/ZIP code |
| `full` | `$order->shipping_address` | Comma-separated full address |

### `totals`
All monetary values in **cents** (divide by 100 for PHP display).

| Field | Source | Description |
|---|---|---|
| `sub_total` | `$order->sub_total` | Subtotal in cents |
| `tax_total` | `$order->tax_total` | Tax total in cents |
| `total` | `$order->total` | Grand total in cents |
| `formatted_total` | `$order->formatted_total` | Formatted string (e.g. "15,000.00") |

### `items[]` (per line)
| Field | Source | Description |
|---|---|---|
| `line_id` | `$line->id` | Order line ID |
| `product_id` | `$line->product_id` | Product ID |
| `name` | `$line->product_name` | Product name at time of order |
| `sku` | `$line->product->sku` | Product SKU |
| `quantity` | `$line->quantity` | Quantity ordered |
| `unit_price` | `$line->unit_price` | Unit price in cents |
| `total` | `$line->total` | Line total in cents |
| `color` | `$line->meta['color']` | Selected color variant |
| `size` | `$line->meta['size']` | Selected size variant |
| `pre_order` | `$line->meta['pre_order']` | Whether this line is a pre-order |

### `items[].product` (product tech specs)
| Field | Source | Description |
|---|---|---|
| `description` | `$product->description` | Full product description (HTML) |
| `short_description` | `$product->short_description` | Short description |
| `price` | `$product->price` | Base price in cents |
| `compare_price` | `$product->compare_price` | Compare-at price in cents |
| `stock` | `$product->stock` | Current stock count |
| `track_stock` | `$product->track_stock` | Whether stock is tracked |
| `colors` | `$product->colors_array` | Array of available colors |
| `sizes` | `$product->sizes_array` | Array of available sizes |
| `variants` | `$product->meta['variants']` | Variant stock breakdown `[{color, size, stock}]` |
| `status` | `$product->status->value` | Product status (draft/published/disabled) |
| `approval_status` | `$product->approval_status->value` | Approval status (pending/approved/rejected) |
| `images` | `$product->images` | All product images with url, is_primary, position |

---

## File Changed

`app/Services/TpinkLabService.php` — method `buildAdminOrderPayload()` (lines 158-198)
