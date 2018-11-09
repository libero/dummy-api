#
# Stage: Composer install for production
#
FROM composer:1.7.3 AS composer

COPY composer.json \
    composer.lock \
    symfony.lock \
    ./

RUN composer --no-interaction install --no-dev --ignore-platform-reqs --no-autoloader --no-suggest --prefer-dist

COPY src/ src/

RUN composer --no-interaction dump-autoload --classmap-authoritative



#
# Stage: Composer install for development
#
FROM composer AS composer-dev

RUN composer --no-interaction install --ignore-platform-reqs --no-suggest --prefer-dist



#
# Stage: Production application
#
FROM php:7.2.11-fpm-alpine as prod

WORKDIR /app

RUN mkdir -p build var && \
    chown --recursive www-data:www-data var

RUN docker-php-ext-install \
    opcache

COPY .docker/php.ini ${PHP_INI_DIR}/conf.d/00-app.ini
COPY bin/ bin/
COPY src/ src/
COPY public/ public/
COPY config/ config/
COPY --from=composer /app/vendor/ vendor/



#
# Stage: Development application
#
FROM prod as dev

COPY .docker/php-dev.ini ${PHP_INI_DIR}/conf.d/01-app.ini
COPY --from=composer /usr/bin/composer /usr/bin/composer
COPY tests/ tests/
COPY composer.json \
    composer.lock \
    phpcs.xml.dist \
    phpstan.neon.dist \
    phpunit.xml.dist \
    symfony.lock \
    ./
COPY --from=composer-dev /app/vendor/ vendor/
