ARG PHP_VERSION="8.0.3"

FROM php:${PHP_VERSION}-alpine AS build

SHELL ["/bin/ash", "-eo", "pipefail", "-c"]

ARG GRPC_VERSION="1.36.0"
ARG PROTOBUF_VERSION="3.15.5"

# hadolint ignore=DL3018,SC2086
RUN apk add --no-cache --virtual .build-deps cmake make g++ git zlib-dev \
    && apk add go --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/community \
    && mkdir -p /protoc \
    && cd /protoc && git clone -b v${GRPC_VERSION} https://github.com/grpc/grpc \
    && cd /protoc/grpc && git submodule update --init \
    && cd /protoc/grpc && mkdir cmake/build && cd cmake/build && cmake ../.. && make protoc grpc_php_plugin \
    && mv /protoc/grpc/cmake/build/grpc_php_plugin /usr/local/bin/grpc_php_plugin \
    && mv /protoc/grpc/cmake/build/third_party/protobuf/protoc-3.14.0.0 /usr/local/bin/protoc \
    && cd /protoc && git clone https://github.com/spiral/php-grpc.git \
    && cd /protoc/php-grpc/cmd/rr-grpc && go get -t . && go install \
    && cd /protoc/php-grpc/cmd/protoc-gen-php-grpc && go get -t . && go install \
    && rm -rf /protoc \
    && apk del .build-deps \
    && apk add make