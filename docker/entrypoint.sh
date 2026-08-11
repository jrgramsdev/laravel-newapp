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
php artisan db:seed --force || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Content generation runs on the queue, so without a worker a generation would
# sit at "queued" forever and the UI would poll until it timed out. Render's
# free tier has no separate worker service, so run one alongside the web
# process — wrapped in a restart loop, because a worker that dies silently
# reintroduces exactly that bug.
#
# --max-time recycles the worker hourly to shed any leaked memory; the loop
# then starts a fresh one.
queue_worker() {
    while true; do
        php artisan queue:work \
            --sleep=1 \
            --tries=3 \
            --max-time=3600 \
            --quiet || true
        echo "[entrypoint] queue worker exited; restarting in 2s" >&2
        sleep 2
    done
}

queue_worker &

# Stop the worker loop when the container is told to shut down.
trap 'kill 0' TERM INT

# Serve on the platform-provided port (Render/Fly set $PORT).
php artisan serve --host 0.0.0.0 --port "${PORT:-8000}"
