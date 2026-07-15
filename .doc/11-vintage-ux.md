# Phase 11 — Vintage UX: Measurements, Tracking, Waitlist, Returns

**Status:** Completed  
**Branch:** `develop`  
**Owner:** Backend + Frontend

---

## Goal

Improve the vintage shopping experience with garment measurements on PDP, customer-visible shipment tracking, stock waitlist alerts, and a returns policy scaffold.

## Checklist

### 1. Garment measurements

- [x] Add nullable JSON column `measurements` on `products` (migration + `init.sql`)
- [x] Cast on `Product` model as array
- [x] Admin product form: optional number inputs for chest, waist, hips, length, shoulder, sleeve (cm)
- [x] Merge inputs into `measurements` in `ProductRequest::prepareForValidation`
- [x] PDP `store/shop/show.blade.php`: measurements table when present

### 2. Shipping tracking for customer

- [x] Eager-load `shipments` in `AccountController::showOrder`
- [x] Customer order show: carrier, tracking number, status, shipped_at
- [x] Admin order show: shipment form (carrier, tracking_number, status)
- [x] `Admin\OrderController::updateShipment` create/update shipment

### 3. Stock alerts / waitlist

- [x] Migration `stock_alerts` with unique `(product_id, email)`
- [x] `StockAlert` model
- [x] POST `/shop/{product}/stock-alert` (guest OK, email validation)
- [x] PDP “Avisarme” form when sellable qty is 0
- [x] `StockAlertService` + `StockAvailable` mailable on `InventoryService::stockIn` / quantity increase

### 4. Returns scaffold

- [x] GET `/returns` static policy page (Spanish) + optional `returns_policy` setting
- [x] Migration `return_requests` (pending|approved|denied|refunded)
- [x] Customer return form on account order show (auth)
- [x] Admin list + approve/deny routes (no automatic Stripe refund)

---

## Docker

```bash
docker compose exec app php artisan migrate
```

---

## Commit

`:sparkles: add vintage measurements tracking waitlist and returns scaffold`
