# Phase 20 — El Salvador Logistics & Fulfillment

**Status:** In progress  
**Branch:** `develop`  
**Rule:** Commit + push after each deliverable. Vanilla CSS BEM. Readable code with clear English comments.

## Domain (think big)

Boutique vintage store with local delivery network in El Salvador:

- Carrier companies / brands (logistics partners)
- Departments + municipalities (2026 territorial structure)
- Shipping zones + rates (flat per municipality + A→B distance pricing)
- Collectors / couriers (workers) + vehicles
- Coupons / discounts / delivery warnings (already partial — extend)
- Shipment lifecycle statuses + recipient outcome events
- Next scheduled dispatch date (settings)
- Customer tracking UI + full admin CRUD UIs

## Parallel agents

1. Database architect — migrations, enums, seeders (SV geo)
2. Backend logistics services — rate calculator, dispatch scheduler
3. Admin UI — companies, workers, vehicles, zones, rates
4. Storefront — shipping calculator + tracking page
5. QA — tests + smoke + docs completion

## El Salvador geo baseline (2026)

Use the post-reform structure: **14 departments**, **44 municipalities** (districts nested where needed). Seed official names in English keys / Spanish labels.

See also: [20-sv-municipalities-2026.md](./20-sv-municipalities-2026.md)

## QA checklist

### Completed

- [x] `git pull --rebase origin develop` (2026-07-15)
- [x] `php artisan migrate --force` — no pending migrations
- [x] `ElSalvadorGeoSeeder` — 14 departments + 44 municipalities
- [x] `LogisticsDemoSeeder` — zones, rates, company, workers, vehicles, dispatch, warnings
- [x] Backend services: `ShippingRateService`, `ShipmentTrackingService`, `DispatchService`
- [x] Storefront routes: `/shipping/quote`, `/tracking`
- [x] Feature tests: `ShippingRateTest`, `TrackingPageTest`

### In progress / blockers

- [ ] Admin CRUD UIs for zones, rates, warnings, dispatch (views partially in flight from parallel agents)
- [ ] Checkout municipality selector wired to live quotes in all environments (`STORE_WAREHOUSE_MUNICIPALITY_ID` or settings bootstrap)
- [ ] Full E2E smoke on Docker after admin agent merges

### Notes (2026-07-15 QA)

- Duplicate `web.php` logistics routes were fixed upstream (`8033620`).
- Missing service classes blocked `artisan` until QA added minimal implementations.
- Parallel agent WIP stashed locally as `qa-other-agents-wip` / `qa-wip-local` — re-apply after admin merge.
