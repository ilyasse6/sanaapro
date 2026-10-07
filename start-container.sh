#!/bin/bash
set -e

if [ "$IS_LARAVEL" = "true" ]; then
  if [ -z "$APP_KEY" ]; then
    echo "ERROR: APP_KEY is not set. Generate one with: php artisan key:generate --show"
    echo "Then add it as an environment variable in Railway and redeploy."
    exit 1
  fi

  mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
  chmod -R ug+rwx storage bootstrap/cache || true

  if [ "$RAILPACK_SKIP_MIGRATIONS" != "true" ]; then
    echo "Waiting for database ..."
    ATTEMPTS=0
    until php artisan migrate --force --no-interaction; do
      ATTEMPTS=$((ATTEMPTS + 1))
      if [ "$ATTEMPTS" -ge 30 ]; then
        echo "ERROR: database migrations failed after ${ATTEMPTS} attempts."
        exit 1
      fi
      echo "Database not ready (attempt ${ATTEMPTS}/30), retrying in 2s ..."
      sleep 2
    done
    echo "Migrations completed."

    if [ "$RUN_SEEDERS" = "true" ]; then
      echo "Running database seeders (RUN_SEEDERS=true) ..."
      php artisan db:seed --force --no-interaction
    fi
  fi

  php artisan storage:link --force 2>/dev/null || php artisan storage:link || true
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
  php artisan optimize

  echo "Starting Laravel (FrankenPHP) ..."
fi

exec docker-php-entrypoint --config /Caddyfile --adapter caddyfile 2>&1
