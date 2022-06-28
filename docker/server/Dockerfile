FROM zolex/grpc-php-server:0.0.3

RUN apk add --no-cache \
    file \
    gettext \
    git

ARG APCU_VERSION="5.1.21"

RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        libzip-dev \
        mysql-dev \
        zlib-dev \
        libxml2-dev \
    && pecl install \
        xdebug \
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
        xdebug \
    && runDeps="$( \
        scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
        | tr ',' '\n' \
        | sort -u \
        | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
        )" \
    && apk add --no-cache --virtual .run-deps $runDeps \
    && apk del .build-deps

WORKDIR /var/www

CMD ["rr", "serve"]