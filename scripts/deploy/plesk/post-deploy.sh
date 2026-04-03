#!/usr/bin/env bash

set -euo pipefail

APP_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/../../" && pwd)"
cd "$APP_ROOT"

PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"
RUN_MIGRATIONS="${RUN_MIGRATIONS:-0}"
RUN_FRONTEND_BUILD="${RUN_FRONTEND_BUILD:-0}"

echo "[plesk-deploy] app root: $APP_ROOT"

if [ ! -f artisan ]; then
    echo "[plesk-deploy] artisan not found in app root"
    exit 1
fi

echo "[plesk-deploy] composer install"
"$COMPOSER_BIN" install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "[plesk-deploy] optimize clear"
"$PHP_BIN" artisan optimize:clear

if [ ! -L public/storage ] && [ ! -e public/storage ]; then
    echo "[plesk-deploy] creating storage symlink"
    "$PHP_BIN" artisan storage:link || true
fi

if [ "$RUN_MIGRATIONS" = "1" ]; then
    echo "[plesk-deploy] running migrations"
    "$PHP_BIN" artisan migrate --force
else
    echo "[plesk-deploy] skipping migrations (RUN_MIGRATIONS=0)"
fi

if [ "$RUN_FRONTEND_BUILD" = "1" ]; then
    if command -v npm >/dev/null 2>&1; then
        echo "[plesk-deploy] frontend build enabled"
        npm ci
        npm run build
    else
        echo "[plesk-deploy] npm not available, skipping frontend build"
    fi
else
    echo "[plesk-deploy] skipping frontend build (RUN_FRONTEND_BUILD=0)"
fi

echo "[plesk-deploy] caching Laravel artifacts"
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache

echo "[plesk-deploy] restarting queues if applicable"
"$PHP_BIN" artisan queue:restart || true

echo "[plesk-deploy] completed"
