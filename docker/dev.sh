#!/usr/bin/env sh
set -e
cd "$(dirname "$0")/.."

cmd="${1:-up}"
shift || true

case "$cmd" in
  up) docker compose up -d --build "$@" ;;
  down) docker compose down "$@" ;;
  build) docker compose build "$@" ;;
  setup)
    [ -f .env ] || cp .env.example .env
    docker compose up -d --build
    docker compose exec app composer install --no-interaction
    docker compose exec app php artisan key:generate --force
    docker compose exec app php artisan migrate --force --seed
    docker compose exec app php artisan storage:link
    echo "Store:   http://localhost:8080"
    echo "Mailpit: http://localhost:8025"
    echo "Vite:    http://localhost:5173"
    ;;
  artisan) docker compose exec app php artisan "$@" ;;
  composer) docker compose exec app composer "$@" ;;
  npm) docker compose exec node npm "$@" ;;
  logs) docker compose logs -f "$@" ;;
  shell) docker compose exec app sh ;;
  ps) docker compose ps ;;
  *) echo "Usage: $0 {up|down|build|setup|artisan|composer|npm|logs|shell|ps}"; exit 1 ;;
esac
