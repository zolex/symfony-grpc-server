# Symfony gRPC Server

This project utilizes `spiral/roadrunner` and `spiral/grpc-php` to run a gRPC server in PHP.
Everything is integreated with `symfony/framework-bundle` and `symfony/flex` so you can easily extend the project like every other symfony project (e.g. with orm-pack, logger, etc.)

## Project Structure

There are three pre-built docker images. `protoc` for generating php code, `server` to run it and optionally `client` to run the example client commands (If you like you can also build them yourself, but it takes some time. check docker-compose.yaml)

The server's entrypoint is a minimal custom roadrunner binary, created from the sources in `/rr`.

Protocol Buffer Definitions are located in `/proto`. Generated PHP code will go in `/grpc-php` and must not be commited (It's already ignored).

Roadrunner's gRPC plugin needs to know the proto files for all registered services. They are imported via `config/services.proto`.

Spiral's gRPC PHP server and the gRPC services are automatically registered as symfony services in `config/services.yaml`.

Roadrunner starts the worker in `bin/worker.php` which uses the symfony kernel to enable all the symfony features and serves spiral's gRPC worker.


## Run the examples

### Setup docker containers

* compile the roadrunner binary with `docker-compose run protoc make server`
* generate a certificate `docker-compose run protoc make cert`
* generate the php code with `docker-compose run protoc make php`
* start the gRPC server with `docker-compose up`
* create the database with `docker-compose exec server bin/console doctrine:migrations:migrate --no-interaction`

### gRPC UI

The included UI is able to discover the running services using the proto files and can be accessed on [http://0.0.0.0:1337](http://0.0.0.0:1337).

### Generate web/js code
If you need the javascript code for use in web clients you can generate it as well using the included protoc plugin.
* generate the web code with `docker-compose run protoc make web`


### Execute client commands
`docker-compose exec client bin/console client:example:persistVehicle Audi S5 COUPE 1`

`docker-compose exec client bin/console client:example:toUpper wurstwasser`

`docker-compose exec client bin/console client:example:toUpper ""`

`docker-compose exec client bin/console client:other:multiply 3 5`

`docker-compose exec client bin/console client:other:multiply 21 2`

## Contribute / Extend

### Composer

The composer dependencides are installed during build time of the server container. This is required to guarantee a seamless "first startup".
Because the grpc-server's worker.php is a long-running program and part of the container's entrypoint, the vendor folder must already exist on container startup.

If you need the vendor folder on your host machine (e.g. for debugging) you can use the docker-compose.override.yaml which will then overwrite the vendor folder from the build.
For this to work, you must run `docker-compose run server composer install` before booting the containers.

### Add new services

* Put your custom `.proto` files in the `/proto` directory *(php namespaces must begin with "Modix\\Grpc\\Service" otherwise automatic service registration does not work)*
* import your service protos in `proto/services.proto`
* generate the php code with `docker-compose run protoc make code`
* create a service class in `src\Services` that implements your generated interface

### Database

As the app is a long running worker, we need to ensure that the database connection is alive. The project contains a [Decorator for Doctrine EntityManager](src/Doctrine/EntityManager.php) that adds a new method to do so. 
If you need to access the database in a gRPC service, make sure to call that method like in [this example](src/Service/Example/v1/ComandService.php).
Also it is a good idea to call `$entityManager->clear()`before returning your reponse from a gRPC service method to keep the memory usage low.

### Extend the containers

If you need additional system dependencies, it's a good idea to create a new Dockerfile based on one of the existing images. *If you prefer, you can also build everything from scratch, but please do not change the existing Dockerfiles.*

Checkout [docker/server/Dockerfile-ext](docker/server/Dockerfile-ext) for an example.

## Hints

* everytime you make a change on the server code, you must restart the server.
