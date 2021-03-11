FROM zolex/grpc-php-client:0.0.1

# hadolint ignore=DL3018,SC2086
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && docker-php-ext-install sockets \
    && docker-php-ext-enable sockets \
    && apk del .build-deps
