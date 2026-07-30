ARG PHP_VERSION=8.4

FROM composer:2 AS composer

FROM php:${PHP_VERSION}-fpm-alpine AS base

RUN apk add --no-cache \
        icu-libs \
        libzip \
        libpng \
        libjpeg-turbo \
        freetype \
        oniguruma \
        libxml2 \
        tzdata

RUN set -eux; \
    apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        oniguruma-dev \
        libxml2-dev \
        linux-headers; \
    docker-php-ext-configure gd --with-freetype --with-jpeg; \
    docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        intl \
        zip \
        opcache; \
    pecl install redis; \
    docker-php-ext-enable redis; \
    docker-php-source delete; \
    apk del .build-deps; \
    rm -rf /tmp/pear ~/.pearrc

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

FROM base AS development

RUN apk add --no-cache --virtual .dev-deps $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apk del .dev-deps

COPY docker/php/development.ini /usr/local/etc/php/conf.d/custom.ini

COPY --chmod=755 docker/entrypoint-dev.sh /usr/local/bin/docker-entrypoint-dev.sh

ENTRYPOINT ["docker-entrypoint-dev.sh"]

CMD ["php-fpm"]

FROM base AS vendor

COPY composer.json composer.lock ./
RUN --mount=type=cache,target=/root/.composer/cache,sharing=locked \
    composer install --no-dev --no-scripts --no-interaction --no-progress \
        --prefer-dist --no-autoloader
        
FROM base AS production

COPY docker/php/production.ini /usr/local/etc/php/conf.d/custom.ini

COPY --from=vendor /var/www/html/vendor ./vendor
COPY --chown=www-data:www-data . .
COPY --chmod=755 docker/entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

USER www-data

HEALTHCHECK --interval=30s --timeout=3s --start-period=5s --retries=3 \
    CMD php-fpm -t || exit 1

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]

FROM nginx:alpine AS nginx-production

COPY --from=production /var/www/html/public /var/www/html/public
COPY docker/nginx/production.conf /etc/nginx/conf.d/default.conf
