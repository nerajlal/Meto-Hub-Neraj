FROM node:22-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm ci --no-audit
COPY resources/ resources/
COPY public/ public/
RUN npm run build

FROM php:8.3-fpm-alpine AS php-base
RUN apk add --no-cache curl libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev \
    icu-dev oniguruma-dev libxml2-dev gmp-dev curl-dev bash su-exec unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j2 pdo_mysql exif pcntl bcmath gd zip intl gmp
COPY docker/php/production.ini /usr/local/etc/php/conf.d/99-production.ini
COPY docker/php/fpm.conf /usr/local/etc/php-fpm.d/zz-docker.conf
WORKDIR /var/www/html

FROM php-base AS dependencies
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

FROM php-base AS app
COPY . .
COPY --from=dependencies /var/www/html/vendor/ vendor/
COPY --from=frontend /app/public/build/ public/build/
RUN rm -f bootstrap/cache/*.php public/hot \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/app/private storage/logs public/storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache public/storage
COPY deploy/container-entrypoint.sh /usr/local/bin/metohub-entrypoint
RUN chmod +x /usr/local/bin/metohub-entrypoint
ENTRYPOINT ["metohub-entrypoint"]
CMD ["php-fpm"]

FROM nginx:alpine AS web
COPY --from=app /var/www/html/public /var/www/html/public
COPY deploy/container-nginx.conf /etc/nginx/conf.d/default.conf
