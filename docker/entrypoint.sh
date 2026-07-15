#!/usr/bin/env sh
set -e

cd /var/www/html

# Ensure runtime directories exist and are writable
mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

# Install PHP dependencies when vendor is missing (fresh clone)
if [ ! -f vendor/autoload.php ]; then
  echo "[entrypoint] Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate app key if missing
if [ -f .env ]; then
  if ! grep -qE '^APP_KEY=base64:.+' .env; then
    echo "[entrypoint] Generating APP_KEY..."
    php artisan key:generate --force || true
  fi
fi

exec "$@"
