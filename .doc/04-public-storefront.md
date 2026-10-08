# Phase 4 — Public Storefront (Blade + BEM)

**Status:** Completed  
**Owner:** Frontend Dev  
**Depends on:** Phase 2–3 stubs

---

## Design system

CSS custom properties (light / dark):

```css
/* Cool gray + neon green — see resources/css/base/tokens.css */
:root {
  --text: rgb(4, 3, 22);
  --background: rgb(237, 237, 237);
  --primary: rgb(17, 255, 0);
  --secondary: rgb(214, 214, 214);
  --accent: rgb(1, 233, 98);
}

[data-theme="dark"] {
  --text: rgb(234, 233, 252);
  --background: rgb(18, 18, 18);
  --primary: rgb(17, 255, 0);
  --secondary: rgb(41, 41, 41);
  --accent: rgb(22, 254, 118);
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
