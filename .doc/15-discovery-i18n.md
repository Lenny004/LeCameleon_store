# Phase 15 — Saved searches + i18n stubs

**Status:** Done  
**Branch:** `develop`

## Goals

1. **Saved searches** — authenticated customers bookmark filtered catalog queries (no email digests yet)
2. **Store lang stubs** — ES/EN `lang/*/store.php` for gradual storefront i18n

## Saved searches

### Schema

`saved_searches`: UUID PK, `user_id`, `name`, `query_params` (JSON filters), `last_notified_at` nullable, timestamps.

### Routes (auth)

| Method | Path | Action |
|--------|------|--------|
| GET | `/account/saved-searches` | List user's saved searches |
| POST | `/account/saved-searches` | Save current filters |
| DELETE | `/account/saved-searches/{savedSearch}` | Delete (owner only) |

### Store UX

- On `/shop`, when signed in and at least one non-default filter is active, show **Guardar búsqueda**.
- Form posts `name` + `query_params` (current filter array as JSON).
- Account page lists saved searches with link to rerun and delete.

### Later

- Scheduled job: compare new matches vs `last_notified_at`, send digest email.

## i18n stubs

Files: `lang/es/store.php`, `lang/en/store.php`.

Keys: `nav_shop`, `nav_cart`, `add_to_cart`, `make_offer`, `verified_piece`, `checkout`, `empty_cart`.

Storefront blades adopt `__('store.*')` incrementally; PDP uses `add_to_cart`, `make_offer`, `verified_piece`.

`.env.example` documents `APP_LOCALE=es` for Spanish storefront default.

## Commits

`:sparkles: add saved searches and store lang stubs`
