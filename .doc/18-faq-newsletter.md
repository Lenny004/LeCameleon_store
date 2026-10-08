# Phase 18 — FAQ and newsletter stub

**Status:** Completed  
**Branch:** `develop`

## Goals

1. FAQ page with vintage buying/care/shipping questions (static Blade, BEM)
2. Newsletter email capture table + footer form (no external ESP yet — store locally)
3. Keep full test suite green

## Delivered

- Storefront `/faq` — Spanish Q&A accordion (condition grades, measurements, shipping, returns, authenticity, offers) with Alpine `x-data` open index
- `newsletter_subscribers` table + `POST /newsletter` (throttle 5/min, unique email)
- Footer: FAQ link + newsletter email form with flash success on subscribe
- Feature tests for FAQ page and newsletter capture

Commit and push to develop.
