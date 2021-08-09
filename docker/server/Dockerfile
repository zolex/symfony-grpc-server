ARG PHP_VERSION="8.0.3"

FROM php:${PHP_VERSION}-alpine AS build

SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

ARG GRPC_VERSION="1.36.0"
ARG PROTOBUF_VERSION="3.15.5"

# hadolint ignore=DL3018,SC2086
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        cmake make autoconf \
        linux-headers \
        git \
        zlib-dev \
    && apk add go --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/community \
    && pecl install \
        protobuf-${PROTOBUF_VERSION} \
    && CPPFLAGS="-Wno-maybe-uninitialized" pecl install \
        grpc-${GRPC_VERSION} \
    && pecl clear-cache \
    && docker-php-ext-enable \
        grpc \
        protobuf \
    && apk del .build-deps \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer

ENTRYPOINT ["docker-php-entrypoint"]
CMD ["/var/www/bin/rr", "serve", "-d", "-v"]
