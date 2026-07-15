# Phase 7 — QA & Production Hardening

**Status:** Completed  
**Owner:** Reviewer  
**Depends on:** Phases 1–6

---

## Checklist

- [x] Feature tests: catalog filter, cart oversell, checkout reservation, coupon, admin role
- [x] Seeders: measurements JSON, approved review, placeholder image path
- [x] Seeders produce a realistic vintage catalog
- [x] `.env.example` documents Supabase setup
- [x] README: install, migrate, seed, run (+ guest checkout, Mailpit, payment:capture, schedule:work, returns)
- [ ] Policies tested manually
- [ ] Responsive check (mobile / desktop)
- [ ] Theme toggle persistence
- [x] No secrets in repo
- [x] Legacy app archived under `legacy/`
- [x] `.doc/` updated to match reality
- [x] `00-master-plan.md` success criteria marked where true

## Feature tests (PHPUnit)

| Test | Coverage |
|------|----------|
| `CatalogFilterTest` | Published products on `/shop`; drafts hidden |
| `CartOversellTest` | Cannot exceed sellable quantity |
| `CheckoutReservationTest` | Order placement increases `quantity_reserved` |
| `CouponCheckoutTest` | Valid coupon applies discount; invalid rejected |
| `AdminRoleMiddlewareTest` | Customer 403 on `/admin`; guest redirected to login |

Run: `docker compose exec app php artisan test`

## Final commit

`:white_check_mark: add feature tests and production checklist`
