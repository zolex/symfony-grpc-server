FROM zolex/grpc-php-server:0.0.2

RUN apk add --no-cache \
    file \
    gettext \
    git

ARG APCU_VERSION="5.1.19"

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        mysql-dev \
        zlib-dev \
        libxml2-dev \
    && docker-php-ext-configure zip \
    && docker-php-ext-install -j"$(nproc)" \
        intl \
        pdo_mysql \
        zip \
    && pecl install \
        apcu-${APCU_VERSION} \
    && pecl clear-cache \
    && docker-php-ext-enable \
        apcu \
        opcache \
    && runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
        | tr ',' '\n' \
        | sort -u \
        | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
        )" \
    && apk add --no-cache --virtual .run-deps $runDeps \
    && apk del .build-deps

WORKDIR /var/www

COPY .env .env
COPY .rr.yaml .rr.yaml
COPY bin/console bin/console
COPY composer.json composer.json
COPY composer.lock composer.lock
COPY grpc-php grpc-php
COPY migrations migrations
COPY proto proto
COPY symfony.lock symfony.lock
COPY src src
COPY config config

RUN composer install
