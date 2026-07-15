# Phase 6 — Enterprise Features

**Status:** In progress  
**Owner:** Full-stack  
**Depends on:** Core store + admin  
**Updated:** 2026-07-15

---

## Features inspired by modern ecommerce platforms

1. **Wishlist** — persist for guests (session) and users ✅
2. **Product recommendations** — related + “customers also viewed” ✅
3. **Advanced filters** — era, condition, size, brand, price range, in-stock ✅
4. **Product views analytics** — track + admin charts (real series) ✅
5. **Low-stock / sold-out alerts** — admin KPI counts ✅
6. **Coupons & promotions** — admin CRUD scaffold
7. **Reviews & ratings** — moderation routes scaffold
8. **Stock reservations** — during checkout
9. **Order status timeline** — pending
10. **SEO**: slugs, meta titles — partial

## Done in this pass

- `RecommendationService::customersAlsoViewed()` co-view affinity (90d sessions)
- PDP sections: “También te puede gustar” + “Clientes también vieron”
- `AnalyticsService` daily revenue/orders series, top products report, low-stock list
- Admin dashboard + analytics wired to real KPIs/charts (no placeholder numbers)
- Branded `403` / `404` Blade error pages
- Docker smoke: `.\docker\dev.ps1 smoke` (200s, admin redirect, 403, 404)

## Commit

`:rocket: deepen recommendations/analytics and add error smoke checks`
