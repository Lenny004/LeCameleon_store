# Phase 1 — Database Architecture (PostgreSQL / Supabase)

**Status:** Completed  
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
- `CouponType`: percent, fixed
- `AddressType`: shipping, billing
- `ShipmentStatus`: pending, shipped, in_transit, delivered, returned, failed

## Supabase considerations

- Use `uuid` primary keys where public-facing (or bigint + uuid alternate)
- Enable `pgcrypto` for `gen_random_uuid()`
- Timestamps `timestamptz`
- Soft deletes via `deleted_at`
- RLS policies deferred (app-layer auth first); document hooks in init.sql comments
- Connection via `DATABASE_URL` / discrete env vars

## Deliverables (completed)

1. **`database/schema/init.sql`** — Full PostgreSQL DDL with comments (source of truth)
2. **Laravel migrations** — 20 migration files (`000000` users rewrite + `000003`–`000022` domain tables)
3. **Eloquent models** — 22 models under `app/Models/` with relationships, casts, and PHPDoc
4. **PHP enums** — 11 backed string enums under `app/Enums/`
5. **Factories** — `UserFactory`, `BrandFactory`, `CategoryFactory`, `ProductFactory`, `OrderFactory`
6. **Seeders** — `UserSeeder`, `BrandSeeder`, `CategorySeeder`, `ProductSeeder`, `CouponSeeder`, `SettingSeeder`, `DatabaseSeeder`

### Demo seed data

- Admin: `admin@lecameleon.store` / `password`
- Staff: `staff@lecameleon.store` / `password`
- Customer: `customer@lecameleon.store` / `password`
- 6 brands, 7 categories (nested), 12 vintage products with images and attributes
- 3 coupons, 4 store settings

## Out of scope (phase 1)

- UI
- Controllers
- Payment gateway live keys
