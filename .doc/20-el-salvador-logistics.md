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
