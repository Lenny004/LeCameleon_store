# Phase 6 — Enterprise Features

**Status:** Completed  
**Owner:** Full-stack  
**Depends on:** Core store + admin  
**Updated:** 2026-07-15

---

## Phase B checklist

- [x] **Reviews on storefront** — POST route, FormRequest, PDP list + form, `is_approved=false` by default
- [x] **Coupons in checkout** — discount flash messaging, success page shows applied coupon
- [x] **Order status timeline** — `Order::statusTimeline()`, account + admin order show views
- [x] **SEO** — `@stack('meta')`, PDP meta/OG tags, `/sitemap.xml` for published products
- [x] **Low-stock UX** — admin products index badge when `quantity_available <= low_stock_threshold`

## Features inspired by modern ecommerce platforms

1. **Wishlist** — persist for guests (session) and users ✅
2. **Product recommendations** — related + “customers also viewed” ✅
3. **Advanced filters** — era, condition, size, brand, price range, in-stock ✅
4. **Product views analytics** — track + admin charts (real series) ✅
5. **Low-stock / sold-out alerts** — admin KPI counts ✅
6. **Coupons & promotions** — checkout apply + admin CRUD ✅
7. **Reviews & ratings** — storefront submit + admin moderation ✅
8. **Stock reservations** — during checkout ✅
9. **Order status timeline** — account + admin order detail ✅
10. **SEO**: slugs, meta titles, sitemap ✅

## Done in this pass

- Storefront reviews: `POST /shop/{product}/reviews`, PDP approved list + auth form
- Checkout coupon feedback: session `info` flash + success page discount row
- `Order::statusTimeline()` with shared `order-timeline` component
- SEO: layout meta stack, PDP OG tags, `GET /sitemap.xml`
- Admin products index: low-stock badge on real paginated data

## Commit

`:rocket: complete enterprise reviews coupons SEO and order timeline`
