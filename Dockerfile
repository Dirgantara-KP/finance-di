FROM php:8.4-fpm AS base

RUN apt-get update && apt-get install -y --no-install-recommends \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl zip \
    && pecl install redis && docker-php-ext-enable redis \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN groupadd -g 33 www-data && useradd -u 33 -g www-data -s /bin/false -M www-data

WORKDIR /var/www/html

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM base AS development

RUN pecl install xdebug && docker-php-ext-enable xdebug

COPY docker/php/development.ini /usr/local/etc/php/conf.d/custom.ini

CMD ["php-fpm"]

FROM base AS production

COPY docker/php/production.ini /usr/local/etc/php/conf.d/custom.ini

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

COPY --chown=www-data:www-data . .

RUN composer dump-autoload --optimize \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

USER www-data

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php-fpm -t || exit 1

CMD ["php-fpm"]
