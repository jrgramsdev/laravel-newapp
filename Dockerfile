# ---- Stage 1: build front-end assets with Vite/Tailwind ----
FROM node:22-slim AS assets
WORKDIR /app
COPY package.json package-lock.json* vite.config.js ./
RUN npm ci
# Tailwind v4 scans the source to detect used utility classes, so copy the
# templates and CSS before building.
COPY resources ./resources
RUN npm run build

# ---- Stage 2: PHP application ----
FROM php:8.4-cli-bookworm AS app

# Install the PHP extensions Laravel needs (SQLite driver, mbstring, etc.).
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_sqlite mbstring bcmath zip intl opcache pcntl

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Install PHP dependencies first (better layer caching).
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction

# App source + compiled assets from the build stage.
COPY . .
COPY --from=assets /app/public/build ./public/build
RUN composer dump-autoload --optimize --no-dev

# Entrypoint prepares the SQLite DB, key, caches, and starts the server.
RUN chmod +x docker/entrypoint.sh \
    && mkdir -p storage/framework/{sessions,views,cache} storage/logs bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false \
    DB_CONNECTION=sqlite \
    LOG_CHANNEL=stderr

# Render (and most PaaS) inject $PORT; default to 8000 for local runs.
ENV PORT=8000
EXPOSE 8000

CMD ["docker/entrypoint.sh"]
