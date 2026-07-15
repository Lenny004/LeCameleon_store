# Phase 1 — Database Architecture (PostgreSQL / Supabase)

**Status:** Planned → Executing  
**Owner:** Database Architect  
**Depends on:** Master plan

---

## Objectives

Design a modern (2026) relational schema for a vintage clothing & objects store, compatible with Supabase (PostgreSQL), documented via `database/schema/init.sql`, and mirrored by Eloquent models + enums.

## Domain notes (vintage retail)

Unlike mass fashion, vintage inventory is often:

- **One-of-a-kind** or very low stock
- Condition-sensitive (`mint`, `excellent`, `good`, `fair`)
- Era / decade tagged (`1970s`, `1990s`)
- Size systems vary (US/EU/letter)
- Authentication / provenance matters for high-value pieces
- Mix of **apparel** and **objects** (decor, accessories)

## Core entities

| Entity | Purpose |
|--------|---------|
| `users` | Customers + staff (role-based) |
| `addresses` | Shipping / billing |
| `categories` | Nested taxonomy (self-FK) |
| `brands` | Designer / label |
| `products` | Sellable items (base) |
| `product_images` | Gallery |
| `product_attributes` | Flexible attrs (era, material, size, color) |
| `inventory_items` | Stock units / SKUs with condition & qty |
| `inventory_movements` | Audit trail (in/out/reserve/adjust) |
| `carts` / `cart_items` | Guest + user carts |
| `orders` / `order_items` | Checkout snapshots |
| `payments` | Payment records |
| `shipments` | Fulfillment |
| `coupons` | Discounts |
| `wishlists` / `wishlist_items` | Saved items |
| `reviews` | Product ratings |
| `product_relations` | Related / upsell / cross-sell |
| `product_views` | Analytics events |
| `settings` | Key-value store config |

## Enums (English)

- `UserRole`: customer, staff, admin
- `ProductStatus`: draft, published, archived, sold_out
- `ProductType`: apparel, object, accessory
- `ConditionGrade`: mint, excellent, good, fair, poor
- `OrderStatus`: pending, paid, processing, shipped, delivered, cancelled, refunded
- `PaymentStatus`: pending, authorized, captured, failed, refunded
- `InventoryMovementType`: stock_in, stock_out, reserve, release, adjust, return
- `RelationType`: related, upsell, cross_sell, similar

## Supabase considerations

- Use `uuid` primary keys where public-facing (or bigint + uuid alternate)
- Enable `pgcrypto` for `gen_random_uuid()`
- Timestamps `timestamptz`
- Soft deletes via `deleted_at`
- RLS policies deferred (app-layer auth first); document hooks in init.sql comments
- Connection via `DATABASE_URL` / discrete env vars

## Deliverables

1. `database/schema/init.sql` — full DDL + comments
2. Laravel migrations matching the schema
3. Eloquent models with relationships & casts
4. PHP enums under `app/Enums`
5. Factories + seeders for demo vintage catalog

## Out of scope (phase 1)

- UI
- Controllers
- Payment gateway live keys
