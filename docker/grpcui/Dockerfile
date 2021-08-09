ARG ALPINE_VERSION="3.14.1"

FROM alpine:${ALPINE_VERSION} AS build

RUN apk add go --no-cache --repository=http://dl-cdn.alpinelinux.org/alpine/edge/community \
    && go get github.com/fullstorydev/grpcui/... \
    && go install github.com/fullstorydev/grpcui/cmd/grpcui@v1.1.0
