# Le Cameleon Store

Vintage clothing & objects ecommerce built with **Laravel 13**, **PostgreSQL**, **Blade**, and **vanilla CSS (BEM)**.

Local development runs entirely on **Docker** — you do not need PHP, Composer, Node, or Postgres installed on the host.

## Requirements

- [Docker Desktop](https://www.docker.com/products/docker-desktop/)

## Quick start

```powershell
# PowerShell
.\docker\dev.ps1 setup
```

```bash
# macOS / Linux / WSL
chmod +x docker/dev.sh
./docker/dev.sh setup
```

Then open:

| Service | URL |
|---------|-----|
| Store | http://localhost:8080 |
| Mailpit | http://localhost:18025 |
| Vite HMR | http://localhost:5173 |
| Postgres (host) | `localhost:5433` |

## What's new (develop)

Recent storefront and admin additions on `develop`:

- **Make an offer** — buyers propose a price on published in-stock products; staff accept, decline, or counter in admin.
- **Authenticity** — admin can mark products as verified (`is_authenticated`) with notes; PDP shows a verified badge.
- **Stripe webhook** — `POST /api/webhooks/stripe` captures pending payments when `STRIPE_WEBHOOK_SECRET` is set (or in local dev without secret).
- **Saved searches** — signed-in customers save catalog filter sets from `/shop` and rerun them from account.

### Demo accounts (after seed)

| Role | Email | Password |
|------|-------|----------|
| Admin | `admin@lecameleon.store` | `password` |
| Staff | `staff@lecameleon.store` | `password` |
| Customer | `customer@lecameleon.store` | `password` |

## Common Docker commands

```powershell
.\docker\dev.ps1 up          # start stack
.\docker\dev.ps1 down        # stop stack
.\docker\dev.ps1 artisan migrate
.\docker\dev.ps1 artisan db:seed
.\docker\dev.ps1 artisan test
.\docker\dev.ps1 artisan tinker
.\docker\dev.ps1 composer require vendor/package
.\docker\dev.ps1 smoke       # HTTP smoke (200 / redirect / 403 / 404)
.\docker\dev.ps1 logs
.\docker\dev.ps1 shell
```

Equivalent inside Compose:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan test
docker compose exec app php artisan schedule:work   # reservation TTL + queued jobs
docker compose exec app php artisan payment:capture LC-20260715-ABC123
```

## Storefront notes

- **Guest checkout** — `/checkout` works without an account; guests must provide an email, and order confirmation is sent via Mailpit locally ([http://localhost:18025](http://localhost:18025)).
- **Returns** — public policy at `/returns`; logged-in customers can open return requests from account order detail.
- **Payments (demo)** — orders start as manual pending payments; staff can capture in admin or run `payment:capture {order-number}` in the app container.
- **Scheduler** — run `schedule:work` in Docker to release expired stock reservations and process queued mail.

## Architecture docs

Execution plans live in [`.doc/`](.doc/). Start with:

- [`.doc/00-master-plan.md`](.doc/00-master-plan.md)
- [`.doc/08-docker.md`](.doc/08-docker.md)
- [`.doc/01-database-architecture.md`](.doc/01-database-architecture.md)

## Supabase

Local Docker uses Postgres 16. For remote Supabase, set the `DB_*` variables in `.env` as documented in `.env.example`. Schema documentation: `database/schema/init.sql`.

## Git workflow

- Work on `develop`
- Never commit directly to `main`
- Commit messages use gitmoji

## Legacy

The original PHP application is archived under `legacy/LeCameleon`.
