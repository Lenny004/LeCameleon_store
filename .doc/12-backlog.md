# Phase 12 — Backlog (P2 / later)

**Status:** Documented  
**Branch:** `develop`

Features intentionally deferred after Phases 6–11. Revisit when the store is selling with the current MVP+.

## Competitive / enterprise (P2)

| Item | Why later |
|------|-----------|
| Make an offer / counter-offer | ~~Needs messaging + negotiation UI~~ ✅ Phase 14 |
| Authenticity / provenance workflow | Admin evidence upload + badge; valuable for designer pieces |
| Full-text search (Scout / Meilisearch / `to_tsvector`) | Current `LIKE` filters are enough for small catalogs |
| Saved searches + email digests | ~~Depends on search + alerts infra~~ ✅ saved searches (Phase 15); email digests later |
| Sold comps / price history | Analytics data model expansion |
| Multi-warehouse inventory | Single-location is enough for one boutique |
| Full i18n (lang files ES/EN) | ~~Copy is mixed ES storefront / EN admin~~ partial stubs `lang/*/store.php` (Phase 15) |
| Automatic Stripe refunds on RMA approve | Returns scaffold is status-only; add when Stripe live |
| GraphQL / headless storefront | Monolith Blade is the chosen architecture |
| Multi-currency | Config is single `STORE_CURRENCY` |

## Nice-to-have polish

- Product image CDN / image variants (thumb, webp)
- ~~Abandoned cart emails~~ ✅ (daily job, `abandoned_cart_hours` config)
- ~~Admin activity audit log~~ ✅ (`/admin/activity`, `RecordsActivity`)
- Two-factor auth for admin
- ~~Rate limiting on stock-alert and review endpoints (beyond defaults)~~ ✅ (`throttle` on stock-alert, reviews, checkout)

## Suggested next epic after go-live

1. Stripe live keys + webhook signature verification  
2. Authenticity badges for high-value SKUs  
3. Scout search when catalog > ~500 SKUs  
