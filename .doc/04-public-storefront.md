# Phase 4 — Public Storefront (Blade + BEM)

**Status:** Completed  
**Owner:** Frontend Dev  
**Depends on:** Phase 2–3 stubs

---

## Design system

CSS custom properties (light / dark):

```css
/* Vintage modern: warm paper + forest olive + copper */
:root {
  --text: rgb(28, 25, 20);
  --background: rgb(244, 239, 230);
  --primary: rgb(59, 79, 56);
  --secondary: rgb(201, 190, 174);
  --accent: rgb(168, 98, 58);
  --surface: rgb(252, 249, 244);
  --font-display: 'Fraunces', Georgia, serif;
  --font-body: 'Source Sans 3', system-ui, sans-serif;
}

[data-theme="dark"] {
  --text: rgb(237, 232, 223);
  --background: rgb(22, 20, 17);
  --primary: rgb(130, 158, 124);
  --secondary: rgb(68, 62, 54);
  --accent: rgb(196, 136, 90);
  --surface: rgb(32, 29, 25);
}
```

Methodology: **BEM** blocks (`header`, `product-card`, `filter-panel`, `shop-layout`, `order-summary`, `checkout`).
Demo assets: logos/marketing from `legacy/LeCameleon` via `LegacyPublicAssetsSeeder` → `public/images/`; product photos seeded into `storage/app/public`.

## Pages

1. Home — brand-first hero, one CTA, featured grid below fold
2. Shop — filters sidebar + product grid
3. Product — gallery, condition, era, add to cart / wishlist
4. Cart & Checkout
5. Account (profile, orders)
6. Auth screens

## UX / libs

- Alpine.js for interactivity (cart drawer, theme toggle, filters)
- Native `<dialog>` / Alpine for modals
- Prefer CSS over JS for hover/focus states
- Responsive mobile-first
- Accessible focus rings using `--primary`

## Commit

`:lipstick: rewrite public storefront with BEM and theme tokens`
