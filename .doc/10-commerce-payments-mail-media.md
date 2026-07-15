# Phase 10 — Commerce: Payments, Mail, Media, Guest Checkout, Reservation TTL

**Status:** Completed  
**Branch:** `develop`  
**Owner:** Backend + Frontend

---

## Goal

Complete the checkout-to-fulfillment loop with manual-first payments, transactional emails, admin product image uploads, guest checkout, and automatic release of expired stock reservations.

## Checklist

### 1. Payments (manual-first when no Stripe keys)

- [x] On order place, create `Payment` row: `provider=manual`, `status=pending`, `amount=grand_total`
- [x] Admin route to mark payment captured → update Payment + `OrderService` transition to Paid
- [x] Optional env `STRIPE_KEY` / `STRIPE_SECRET` documented in `.env.example` (not required)
- [x] Artisan command `payment:capture {order}` for Docker demos

### 2. Mails (Mailpit in compose)

- [x] Mailable classes: `OrderPlaced`, `OrderPaid`, `OrderShipped`, `OrderCancelled`
- [x] Blade mail views under `resources/views/mail/`
- [x] Dispatch from `CheckoutService` (`OrderPlaced`) and `OrderService` on status transitions
- [x] Send to customer email from user or `shipping_address.email`

### 3. Product image uploads

- [x] Admin product form: multipart `enctype`, multiple file input `images[]`
- [x] `ProductController` store/update: validate images, store on `public` disk under `products/{id}/`, create `ProductImage` rows; first image `is_primary`
- [x] Show existing images on edit with option to keep

### 4. Guest checkout

- [x] Make `orders.user_id` nullable (migration + `database/schema/init.sql`)
- [x] Checkout routes without auth middleware; success via session `last_order_id` or auth ownership
- [x] `CheckoutRequest` authorize returns true; require top-level `email` for guests
- [x] `CheckoutService::placeOrder` accepts `?User $user` and email string
- [x] Update `routes/web.php` accordingly

### 5. Reservation TTL

- [x] `config/store.php`: `reservation_ttl_minutes` => 30
- [x] Job `ReleaseExpiredReservations`: release stale reserves, optionally cancel pending orders
- [x] Schedule in `routes/console.php`: every five minutes
- [x] Document `php artisan schedule:work` below

---

## Docker — scheduler

Run the Laravel scheduler inside the `app` container (alongside `docker compose up`):

```bash
docker compose exec app php artisan schedule:work
```

This executes `ReleaseExpiredReservations` every five minutes to free stock held by unpaid pending orders past the TTL.

---

## Commit

`:sparkles: add payments mail media guest checkout and reservation TTL`
