# Phase 9 — Fix Blockers (Checkout + Admin Product Form)

**Status:** Completed  
**Branch:** `develop`  
**Owner:** Backend + Frontend

## Goal

Make checkout and admin product CRUD work end-to-end against the existing FormRequests and models.

## Fixes applied

1. Checkout Blade uses `billing_address.*` / `shipping_address.*`, coupon, notes
2. Single-step checkout (review step removed from flow)
3. `CheckoutService` creates the order first, then reserves stock with `reference_id`
4. Admin product form matches `ProductRequest` fields
5. `ProductRequest` normalizes `is_unique_piece` checkbox

## Done when

- [x] Checkout form field names match `CheckoutRequest`
- [x] Admin product form matches `ProductRequest`
- [x] Reservations store `reference_type` + `reference_id`

## Commit

`:bug: fix checkout and admin product form contracts`
