# =========================================================================
# Stage 1: Build Frontend Assets (Vite)
# =========================================================================
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json vite.config.js ./
RUN npm ci --prefer-offline --no-audit
COPY resources/ resources/
COPY public/ public/
RUN npm run build

# =========================================================================
# Stage 2: Install Composer Dependencies
# =========================================================================
FROM composer:2.7 AS composer
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# =========================================================================
# Stage 3: Production PHP Runtime
# =========================================================================
FROM php:8.2-fpm-alpine AS base

# Install system dependencies & libraries required for PHP extensions
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    icu-dev \
    oniguruma-dev \
    libxml2-dev \
    gmp-dev \
    bash \
    su-exec

# Configure and install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache \
        gmp \
        xml

# Copy production PHP and PHP-FPM configuration
COPY docker/php/production.ini /usr/local/etc/php/conf.d/99-production.ini
COPY docker/php/fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf

WORKDIR /var/www/html

# Copy application source code
COPY . .

# Copy built vendor and frontend assets from build stages
COPY --from=composer /app/vendor/ ./vendor/
COPY --from=frontend /app/public/build/ ./public/build/

# Set up required writable directories and permissions for www-data (uid 82 on Alpine)
RUN mkdir -p \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/app/private \
        storage/logs \
        public/storage \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public/storage \
    && chmod -R 775 storage bootstrap/cache public/storage

# Copy entrypoint script
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["php-fpm"]
