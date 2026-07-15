# Phase 8 — Docker Development Environment

**Status:** Completed  
**Owner:** DevOps / Orchestrator  
**Depends on:** Phase 1–2

---

## Goal

Run the entire stack without installing PHP, Composer, PostgreSQL, Redis, or Node on the host. Only **Docker Desktop** is required.

## Services

| Service | Image / build | Host port | Role |
|---------|---------------|-----------|------|
| `app` | `Dockerfile` (PHP 8.3-FPM) | — | Laravel + Composer + Node tooling |
| `nginx` | nginx:1.27-alpine | `8080` | HTTP front door |
| `postgres` | postgres:16-alpine | `5433` | Local Postgres (Supabase-compatible) |
| `redis` | redis:7-alpine | `6379` | Cache / queues |
| `node` | node:22-alpine | `5173` | Vite HMR |
| `mailpit` | axllent/mailpit | `18025` / `11025` | Dev mail UI / SMTP |

## Quick start

```bash
# 1. Copy env (already tuned for Docker Compose service names)
cp .env.example .env

# 2. Build and start
docker compose up -d --build

# 3. Install deps + migrate + seed (first time)
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose exec app php artisan storage:link

# 4. Open the store
# http://localhost:8080
# Mailpit: http://localhost:18025
# Vite: http://localhost:5173
```

### Windows (PowerShell)

```powershell
.\docker\dev.ps1 setup
```

## Common commands

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan tinker
docker compose exec app php artisan test
docker compose exec app composer require vendor/package
docker compose exec node npm install
docker compose logs -f app nginx
docker compose down
docker compose down -v   # wipe Postgres volume
```

## Supabase

Local Docker uses Postgres 16 for development. Production / shared staging can point `.env` at Supabase:

```
DB_CONNECTION=pgsql
DB_HOST=db.YOUR_PROJECT_REF.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=...
DB_SSLMODE=require
```

Keep `database/schema/init.sql` as human-readable DDL documentation; Laravel migrations remain the runtime source of truth.

## Files

- `Dockerfile` — PHP-FPM 8.3 + extensions + Composer + Node
- `compose.yaml` — full stack
- `docker/nginx/default.conf`
- `docker/php/*`
- `docker/entrypoint.sh`
- `docker/dev.ps1` / `docker/dev.sh`
