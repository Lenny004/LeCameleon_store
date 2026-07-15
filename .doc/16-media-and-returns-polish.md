# Phase 16 — Media URLs + Return Refund Polish

**Status:** Done  
**Branch:** `develop`

## Goals

1. **Product image URLs** — consistent public-disk URLs with placeholder fallback on cards and PDP gallery
2. **Return refunds** — when staff marks a return as refunded, record a manual refund payment row if the order was captured

---

## 1. Product image display

### `ProductImage::url()`

- Resolves `path` via `Storage::disk('public')->url($path)` when the file exists (or path is already absolute).
- Falls back to `config('store.product_image_placeholder')` (`placeholders/vintage-product.jpg`).
- Cards without an image still use `.product-card__media { aspect-ratio: 3 / 4 }` — no broken layout.

### Storefront usage

| View | Change |
|------|--------|
| `components/product-card.blade.php` | Primary image via `ProductImage::url()` |
| `store/shop/show.blade.php` | Gallery JSON built from `$img->url()` |

### Admin uploads (`ProductController`)

- Stores the **original** file on the `public` disk under `products/{id}/`.
- No Intervention Image dependency.
- **Optional GD thumb:** when `ext-gd` is available, a resized `_thumb` copy is written alongside the original (not stored in DB; useful for future admin previews).

### Placeholder CSS

Existing BEM blocks already reserve space:

- `.product-card__media` — `aspect-ratio: 3 / 4`
- `.product-gallery__main` — `aspect-ratio: 3 / 4`, `background-color: var(--secondary)`

Run `php artisan storage:link` in Docker if product images 404 locally.

---

## 2. Returns → refunded + `PaymentService::recordRefund`

### Admin flow

| Method | Route | When |
|--------|-------|------|
| PATCH | `/admin/return-requests/{returnRequest}/refund` | Approved return → **Mark refunded** |

### `PaymentService::recordRefund(Order $order, float $amount, string $note)`

- Runs only when the order has at least one **captured** payment.
- Creates a new `payments` row: `provider=manual`, `status=refunded` (`PaymentStatus::Refunded` — no migration).
- Stores `note` in `payload.note`; `transaction_reference` = `refund-{timestamp}`.
- Refund amount: return line `line_total`, or full `grand_total` when no line item.

### `PaymentStatus` enum

`Refunded = 'refunded'` already exists — string column, no schema change.

---

## Commit

`:lipstick: polish product image URLs and return refunds`
