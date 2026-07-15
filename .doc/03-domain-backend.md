# Phase 3 — Domain Backend (Services, Controllers, Policies)

**Status:** Planned  
**Owner:** Backend Dev  
**Depends on:** Phase 1–2

---

## Architecture

```
Http/Controllers/Store/*     # Public storefront
Http/Controllers/Admin/*     # Private admin
Http/Controllers/Api/*       # JSON endpoints (cart, search)
Services/
  CatalogService
  CartService
  CheckoutService
  InventoryService
  RecommendationService
  AnalyticsService
  OrderService
```

## Public module endpoints / routes

- `GET /` — home (featured, new arrivals, collections)
- `GET /shop` — catalog + filters (category, brand, era, size, condition, price)
- `GET /shop/{slug}` — product detail
- `GET /search` — full-text / ILIKE search
- Cart: add / update / remove / view
- Checkout: address → review → place order
- Auth: register / login / logout / profile / orders

## Admin module (`/admin`)

- Dashboard KPIs (sales, orders, low stock, conversion)
- Products CRUD + images + attributes
- Inventory adjustments + movement log
- Orders management + status transitions
- Customers / staff users
- Categories, brands, coupons
- Reviews moderation
- Analytics reports

## Security

- Middleware `role:admin|staff` for admin
- Policies for Order, Product, User
- Form Requests for validation
- CSRF on web; Sanctum for API if needed

## Commit cadence

One commit per vertical slice when stable.
