# Operations

## Queue and scheduler

Development Compose starts two dedicated long-running services:

| Service | Command | Responsibility |
|---------|---------|----------------|
| `queue` | `php artisan queue:work redis` | Executes queued mail and application jobs |
| `scheduler` | `php artisan schedule:work` | Evaluates Laravel's schedule and dispatches due work |

Both services wait for healthy PostgreSQL and Redis instances and restart unless explicitly stopped. The queue worker recycles hourly so that it releases memory and reloads application code. After changing queue code, restart it immediately with:

```bash
docker compose restart queue
```

Inspect worker state and scheduled tasks with:

```bash
docker compose logs -f queue scheduler
docker compose exec app php artisan schedule:list
docker compose exec app php artisan queue:failed
docker compose exec app php artisan queue:retry all
```

Run exactly one scheduler replica in each environment. Queue workers may be scaled horizontally when service names are not fixed with `container_name` in the target orchestrator.

## PostgreSQL backup

Create the destination directory first. The command reads the Compose credentials inside the database container and writes a custom-format dump to the host:

```bash
mkdir -p backups
docker compose exec -T postgres sh -c 'pg_dump -U "$POSTGRES_USER" -d "$POSTGRES_DB" -Fc' > backups/lecameleon.dump
```

Verify that the dump is readable before relying on it:

```bash
docker compose exec -T postgres pg_restore --list < backups/lecameleon.dump
```

Database dumps do not contain uploaded files. Back up the production volume or object-storage bucket used for `storage/app/public` separately, and test both restore paths regularly. Keep backups encrypted and outside the application host according to the retention policy.

## PostgreSQL restore

A restore is destructive. Confirm the target environment and take a fresh backup first. Stop database clients, restore the dump, then run migrations and restart the services:

```bash
docker compose stop nginx app queue scheduler
docker compose exec -T postgres sh -c 'pg_restore -U "$POSTGRES_USER" -d "$POSTGRES_DB" --clean --if-exists --no-owner' < backups/lecameleon.dump
docker compose run --rm app php artisan migrate --force
docker compose start app queue scheduler nginx
```

Use a newly created database instead of `--clean` when validating a backup. Never restore an untrusted dump.

## Production image

`Dockerfile` remains optimized for local bind-mounted development. `Dockerfile.prod` is the production build: it pins default toolchain versions, installs dependencies from both lockfiles, excludes development Composer packages, builds Vite assets in a separate Node stage, and enables production OPcache behavior.

Build an immutable image tagged with the commit SHA:

```bash
docker build --pull -f Dockerfile.prod -t <registry>/<image>:<git-sha> .
docker push <registry>/<image>:<git-sha>
```

For stricter reproducibility, override `PHP_IMAGE`, `COMPOSER_IMAGE`, and `NODE_IMAGE` with registry digests in the deployment pipeline. Do not bake `.env` into an image; `.dockerignore` excludes all `.env` variants except `.env.example`. Inject `APP_KEY`, database, Redis, mail, payment, and object-storage credentials through the platform's secret manager.

A deployment should use the same immutable image for these process types:

| Process | Command | Replicas |
|---------|---------|----------|
| PHP-FPM | default image command | One or more |
| Queue | `php artisan queue:work redis --sleep=3 --tries=3 --timeout=90 --max-time=3600` | One or more |
| Scheduler | `php artisan schedule:work` | Exactly one |

Run `php artisan migrate --force` as a one-off release task before switching traffic. Mount persistent storage or configure object storage for uploads, provide Redis and PostgreSQL as persistent managed services, and expose only the HTTP reverse proxy. The reverse proxy must serve the `public/` directory from the same image or from an extracted build artifact; it must not depend on a development bind mount.

After deployment, verify the HTTP health endpoint or storefront, queue logs, `php artisan schedule:list`, migration status, and a real backup/restore drill. Roll back by deploying the previous immutable image; database rollback requires a migration-specific plan and must not be assumed safe.
