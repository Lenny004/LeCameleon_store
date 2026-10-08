# Phase 2 — Laravel Scaffold & Supabase Config

**Status:** Planned  
**Owner:** Backend Dev  
**Depends on:** Phase 1 design (can scaffold in parallel)

---

## Objectives

Bootstrap a clean Laravel application in the monorepo root, configure PostgreSQL for Supabase, archive legacy PHP under `legacy/`, and establish git workflow on `develop`.

## Steps

1. Move `LeCameleon/` → `legacy/LeCameleon/`
2. Create Laravel project in repo root (or `app/` sibling — prefer root)
3. Configure `.env.example` for Supabase Postgres
4. Install: Sanctum (optional API), Intervention Image (later), debugbar (dev)
5. Create route files: `web.php`, `admin.php`, `api.php`
6. Base layout stubs + CSS design tokens
7. README + `.gitignore`
8. Git init → `main` empty/initial → branch `develop`

## Env keys (Supabase)

```
DB_CONNECTION=pgsql
DB_HOST=db.xxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=
DB_SSLMODE=require

# Optional pooled connection
# DB_HOST=aws-0-....pooler.supabase.com
```

## Commit

`:tada: initial Laravel scaffold for Le Cameleon vintage store`
