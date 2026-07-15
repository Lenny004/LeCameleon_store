# Phase 14 — Offers + Search Upgrade

**Status:** In progress  
**Branch:** `develop`

## Goals

1. **Make an offer** — buyers propose a price on unique pieces; staff can accept/counter/decline
2. **Better catalog search** — case-insensitive multi-term search across name, description, sku, brand (still no Scout; keep readable)

## Make an offer (done)

### Schema

`offers` table: UUID PK (`HasUuids`), `product_id`, `user_id`, `amount`, `message`, `status` (`pending|accepted|declined|countered|expired|withdrawn`), `counter_amount`, `admin_notes`, `expires_at`, timestamps.

### Store

- Auth required: `POST /shop/{product}/offers` (`OfferRequest`: amount ≥ 1 and **&lt;** list price).
- PDP shows **Hacer oferta** for published in-stock products (highlighted for `is_unique_piece`).
- Account: `GET /account/offers` lists the signed-in customer's offers.

### Admin

- `GET /admin/offers` — pending offers first.
- Actions: accept, decline, counter (`counter_amount` + status `countered`). Accept does **not** change product price.
- `RecordsActivity` on accept / decline / counter.

### Mail

- `OfferReceived` → `config('mail.from.address')` on new offer.
- `OfferResponded` → buyer on accept / decline / counter.

## Catalog search (`CatalogService`)

### `q` filter (shop + API)

- Query string is split on whitespace into **terms**.
- Every term must match (**AND**) at least one field (**OR** within the term):
  - `products.name`
  - `products.description`
  - `products.sku`
  - `products.color`
  - `products.material`
  - `products.era_decade`
  - related `brands.name` (`whereHas`)
- Matching is case-insensitive (`LOWER(...) LIKE`).
- An empty or whitespace-only `q` applies **no** text filter — existing category, brand, price, stock, and sort filters still work.
- Example: `denim jacket` matches a product whose name contains “denim” and whose description contains “jacket”.

### API suggestions

`GET /api/v1/search?q=...` returns the paginated product payload **plus**:

```json
"suggestions": [
  { "slug": "denim-chore-coat", "name": "Denim Chore Coat", "price": "89.50" }
]
```

- Populated by `CatalogService::searchSuggestions($q, $limit = 8)` for highlight/autocomplete UIs.
- Omitted matches when `q` is empty (`[]`).

### Store routes

- `/shop` and `/search` both use `paginatePublished()`; `/search` normalizes `query` → `q`.
- Empty search shows the full published catalog (subject to other filters).

## Commits

Push each slice to `origin/develop` with gitmoji.
