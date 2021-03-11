# Symfony gRPC PHP Server

This project utilizes `spiral/roadrunner` and `spiral/grpc-php` to run a gRPC server in PHP.
Everything is integreated with `symfony/framework-bundle` and `symfony/flex` so you can easily extend the project like every other symfony projecet (e.g. with orm-pack, logger, etc.)

## Project Structure

There are three pre-built docker images. `protoc` for generating php code, `server` to run it and optionally `client` to run the example client commands (If you like you can also build them yourself, but it takes some time. check docker-compose.yaml)

The server's enetrypoint is a minimal custom roadrunner binary, created from the sources in `/rr`.

Protocol Buffer Definitions are located in `/proto`. Generated PHP code will go in `/grpc` and must not be commited (It's already ignored).

Roadrunner's gRPC plugin needs to know the proto files for all registered services. They are imported via `config/services.proto`.

Spiral's gRPC PHP server and the gRPC services are registered as symfony services in `config/services.yaml`.

Roadrunner starts the worker in `bin/worker.php` which uses the symfony kernel to enable all the symfony features and then serves spiral's gRPC worker.


## Run the examples
* generate the roadrunner binary with `docker-compose run protoc make server`
* generate the php code with `docker-compose run protoc make code`
* start the gRPC server with `docker-compose up server`

### Run seperately with client container
`docker-compose run client -exec "bin/console client:example:toUpper wurstwasser"`

`docker-compose run client -exec "bin/console client:example:toUpper ''"`

`docker-compose run client -exec "bin/console client:other:multiply 3 5`

`docker-compose run client -exec "bin/console client:other:multiply 21 2`

### Exec on booted client container
`docker-compose up client -d`

`docker-compose exec client bin/console client:example:toUpper wurstwasser`

`docker-compose exec client bin/console client:other:multiply 21 2`

### Exec on booted server container
`docker-compose exec server bin/console client:example:toUpper wurstwasser`

## Contribute / Extend

### Add new services

* Put your custom `.proto` files in the `/proto` directory
* import your protos in `config/services.proto`
* generate the php code with `docker-compose run protoc make code`
* create a service class in `src\Services` that extends your generated Interface
* register your service with the gRPC server in `config/services.yaml`

### Extenx the containers

If you need additional system dependencies, it's a good idea to create a new Dockerile based on one of the existing images. *If you prefer, you can also build everything from scratch, but please do not change the existing Dockerfiles.*

```Dockerfile
FROM zolex/grpc-php-server:0.0.1
RUN apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        icu-dev \
        mysql-dev \
    && docker-php-ext-install \
        intl \
        pdo_mysql \
    && pecl install \
        apcu \
    && pecl clear-cache \
    && docker-php-ext-enable \
        apcu \
    && apk del .build-deps \
```

## Hints

* everytime you make a change on the server code, you must restart the server.
* If you do not want to use the `Modix\Grpc` namespace like in the examples, you can add another psr-4 autoload entry in composer.json and call `composer dump-autoload`)
