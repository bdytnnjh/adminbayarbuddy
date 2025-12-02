# Tahap 1: Build Node.js assets
# Menggunakan image Bun untuk build frontend yang cepat.
FROM oven/bun:1-alpine AS node-builder
WORKDIR /app

# Install Node.js sebagai fallback
RUN apk add --no-cache nodejs npm

# Salin file dependensi terlebih dahulu untuk caching
COPY package*.json bun.lock ./
COPY vite.config.js ./

# Install dependencies dengan fallback ke npm jika bun gagal
RUN bun install --frozen-lockfile || npm ci

# Salin sisa source code frontend (diperlukan untuk build)
COPY resources/ ./resources/
COPY public/ ./public/

# ========================= PERBAIKAN PENTING =========================
# Hapus direktori build yang mungkin sudah ada untuk memastikan build yang bersih.
RUN rm -rf /app/public/build

# Jalankan proses build dengan verbose output untuk debugging
RUN echo "=== STARTING BUILD PROCESS ===" && \
    echo "Node version: $(node --version)" && \
    echo "Bun version: $(bun --version)" && \
    echo "Current directory: $(pwd)" && \
    echo "Directory contents before build:" && \
    ls -la && \
    echo "Resources directory:" && \
    ls -la resources/ && \
    echo "=== RUNNING BUN BUILD ===" && \
    (bun run build --verbose || (echo "Bun build failed, trying with npm..." && npm run build)) && \
    echo "=== BUILD COMPLETED ==="

# Verifikasi hasil build dan tampilkan isi direktori build
RUN echo "=== BUILD VERIFICATION ===" && \
    ls -la /app/public/ && \
    echo "=== BUILD DIRECTORY CONTENTS ===" && \
    if [ -d "/app/public/build" ]; then \
        ls -la /app/public/build/ && \
        echo "=== MANIFEST CHECK ===" && \
        if [ -f "/app/public/build/manifest.json" ]; then \
            echo "✓ manifest.json found" && \
            cat /app/public/build/manifest.json; \
        else \
            echo "✗ manifest.json NOT found"; \
        fi; \
    else \
        echo "✗ Build directory NOT found"; \
    fi
# =====================================================================

# Tahap 2: Build PHP dependencies
# Menggunakan image Composer untuk menginstal dependensi backend.
FROM composer:2.6 AS composer-builder
WORKDIR /app
COPY composer.json composer.lock ./
# Instal hanya dependensi produksi
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --no-plugins

# Tahap 3: Final production image
# Menggunakan image FrankenPHP yang dioptimalkan untuk production.
FROM dunglas/frankenphp:php8.3-alpine

# Set environment variables untuk production
ENV SERVER_NAME=":80"
ENV PHP_INI_SCAN_DIR="/usr/local/etc/php/conf.d:/app/docker/php"
ENV ASSET_URL="https://bayarbuddy.my.id"
ENV APP_URL="https://bayarbuddy.my.id"
ENV FORCE_HTTPS="true"
ENV TRUSTED_PROXIES="*"

WORKDIR /app

# Install system dependencies dan ekstensi PHP dalam satu layer
RUN apk update && \
    apk add --no-cache \
        curl libpng oniguruma libxml2 libzip sqlite su-exec \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS libpng-dev oniguruma-dev libxml2-dev libzip-dev sqlite-dev \
    && docker-php-ext-install -j$(nproc) \
        mbstring exif pcntl bcmath gd zip pdo pdo_mysql pdo_sqlite \
    && apk del .build-deps \
    && rm -rf /var/cache/apk/* /tmp/* /var/tmp/*

# Buat user non-root untuk keamanan
RUN addgroup -g 1000 -S appuser && \
    adduser -u 1000 -S appuser -G appuser

# Salin artifak dari stage-stage sebelumnya
COPY --from=composer-builder --chown=appuser:appuser /app/vendor /app/vendor

# Salin folder public dari lokal DULU (tanpa build directory)
COPY --chown=appuser:appuser public /app/public

# LALU timpa dengan folder build dari node-builder - COPY SELURUH PUBLIC
RUN rm -rf /app/public
COPY --from=node-builder --chown=appuser:appuser /app/public /app/public

# Verifikasi assets berhasil disalin
RUN echo "=== FINAL CONTAINER VERIFICATION ===" && \
    ls -la /app/public/ && \
    echo "=== FINAL BUILD DIRECTORY ===" && \
    if [ -d "/app/public/build" ]; then \
        ls -la /app/public/build/ && \
        if [ -f "/app/public/build/manifest.json" ]; then \
            echo "✓ manifest.json successfully copied to final container"; \
        else \
            echo "✗ manifest.json missing in final container"; \
        fi; \
    else \
        echo "✗ Build directory missing in final container"; \
    fi

# Salin sisa file aplikasi
COPY --chown=appuser:appuser app /app/app
COPY --chown=appuser:appuser bootstrap /app/bootstrap
COPY --chown=appuser:appuser config /app/config
COPY --chown=appuser:appuser database /app/database
COPY --chown=appuser:appuser resources /app/resources
COPY --chown=appuser:appuser routes /app/routes
COPY --chown=appuser:appuser storage /app/storage
COPY --chown=appuser:appuser artisan /app/artisan
COPY --chown=appuser:appuser Caddyfile /etc/caddy/Caddyfile
COPY --chown=appuser:appuser .env.prod /app/.env
COPY --chown=appuser:appuser composer.json /app/composer.json
COPY --chown=appuser:appuser composer.lock /app/composer.lock

# Buat direktori yang dibutuhkan Caddy untuk menyimpan state-nya
RUN mkdir -p /data/caddy /config/caddy && \
    chown -R appuser:appuser /data /config

# Buat direktori storage dan cache SEBELUM switch user
RUN mkdir -p \
        storage/framework/sessions \
        storage/framework/views \
        storage/framework/cache \
        storage/framework/cache/data \
        storage/logs \
        bootstrap/cache && \
    chown -R appuser:appuser storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# Pindah ke user non-root sebelum menjalankan perintah aplikasi
USER appuser

# Optimisasi Laravel
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    (php artisan storage:link || echo "Storage link already exists or failed, continuing...")

# Expose port untuk HTTP, HTTPS, dan HTTP/3
EXPOSE 80 443 443/udp

# Perintah untuk menjalankan aplikasi menggunakan FrankenPHP dan Caddyfile kustom
CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]

