#!/usr/bin/env bash
set -e

cd "$(dirname "$0")/.."

# Ensure a .env exists so artisan commands that expect one don't complain.
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Generate an APP_KEY at runtime only if one wasn't supplied via the environment.
# (Set a stable APP_KEY in the host's env vars for persistent sessions/cookies.)
if [ -z "${APP_KEY}" ]; then
    php artisan key:generate --force
fi

# SQLite database file (ephemeral on free tiers — fine for a demo).
mkdir -p database
if [ ! -f database/database.sqlite ]; then
    touch database/database.sqlite
fi

# Clear any stale caches, run migrations, then cache config/routes/views for prod.
php artisan config:clear
php artisan migrate --force || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Serve on the platform-provided port (Render/Fly set $PORT).
exec php artisan serve --host 0.0.0.0 --port "${PORT:-8000}"
