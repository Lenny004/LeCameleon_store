# Le Cameleon Store — Master Execution Plan

**Project:** Vintage clothing & objects e-commerce  
**Stack:** Laravel 12 · PostgreSQL (Supabase) · Blade · Vanilla CSS (BEM)  
**Branch policy:** All work on `develop` (never commit directly to `main`)  
**Commit style:** Gitmoji prefixes (`:sparkles:`, `:card_file_box:`, `:lipstick:`, etc.)

---

## 1. Goals

Rebuild the legacy PHP store as a production-ready Laravel monorepo with:

- Modern PostgreSQL schema optimized for vintage retail (unique pieces + limited stock)
- Public storefront (catalog, search, filters, cart, checkout)
- Private admin (dashboard, inventory, orders, users, analytics)
- Enterprise-grade modules inspired by Bagisto / modern inventory patterns
- Fully rewritten Blade UI with BEM CSS and light/dark theme tokens

## 2. Repository layout (target)

```
LeCameleon_Store/
├── .doc/                      # Execution plans & architecture docs
├── app/
│   ├── Enums/
│   ├── Models/
│   ├── Http/{Controllers,Requests,Middleware,Resources}
│   ├── Services/              # Domain services (Cart, Checkout, Inventory, Analytics)
│   ├── Policies/
│   └── View/Components/
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── schema/init.sql        # Supabase-ready init script (documentation)
├── resources/
│   ├── views/{store,admin,components,layouts}
│   ├── css/                   # BEM modules
│   └── js/
├── routes/{web,admin,api}.php
├── legacy/                    # Archived original PHP app (LeCameleon)
└── tests/
```

## 3. Execution phases

| Phase | Doc | Owner agent | Deliverable |
|-------|-----|-------------|-------------|
| 0 | `00-master-plan.md` | Orchestrator | This plan |
| 1 | `01-database-architecture.md` | Database Architect | Schema, enums, init.sql, Eloquent models |
| 2 | `02-laravel-scaffold.md` | Backend Dev | Laravel app, config, Supabase, git develop |
| 3 | `03-domain-backend.md` | Backend Dev | Services, controllers, policies, routes |
| 4 | `04-public-storefront.md` | Frontend Dev | Public Blade + BEM + UX |
| 5 | `05-admin-panel.md` | Frontend Dev | Admin Blade + dashboard UX |
| 6 | `06-enterprise-features.md` | Full-stack | Wishlist, recommendations, analytics, reviews |
| 7 | `07-qa-hardening.md` | Reviewer | Docs, seeders, polish, production checklist |

## 4. Working rules

1. Write/update a phase plan in `.doc/` before coding that phase.
2. Implement → review → document → commit to `develop` with gitmoji.
3. Prefer readable Laravel conventions over clever abstractions.
4. English for code, enums, DB names; Spanish for user-facing copy where the brand needs it (UI can be bilingual later; start English labels in admin, Spanish storefront copy OK).
5. BD is not deployed yet — schema may evolve freely; keep `init.sql` as source of truth.

## 5. Success criteria

- [ ] Laravel boots with PostgreSQL/Supabase config
- [ ] Full schema documented in `database/schema/init.sql`
- [ ] Public: catalog, filters, product detail, cart, checkout
- [ ] Admin: dashboard KPIs, CRUD inventory/orders/users
- [ ] Light/dark CSS variables applied via BEM
- [ ] Seed data for demo vintage catalog
- [ ] `.doc/` plans reflect what was actually built

## 6. Status

- **Started:** 2026-07-14
- **Current phase:** 0 → 1
- **Branch:** `develop` (to be created)
