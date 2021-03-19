.PHONY: server php web cert
server: $(wildcard rr/**/*.go) $(wildcard rr/*.go)
	cd rr \
	&& go get -t . \
	&& go build -a -x -v -o rr \
	&& mv rr ../bin
php:
	rm -rf /var/www/grpc-php \
	&& mkdir -p /var/www/grpc-php \
	&& /usr/local/bin/protoc $(shell find /var/www/proto -type f -name "*.proto") \
		--php_out=/var/www/grpc-php \
		--php-grpc_out=/var/www/grpc-php \
		--grpc_out=/var/www/grpc-php \
		--plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin \
		--plugin=protoc-gen-php-grpc=/root/go/bin/protoc-gen-php-grpc \
		--proto_path /var/www/proto
web:
	rm -rf /var/www/grpc-web \
	&& mkdir -p /var/www/grpc-web \
	&& /usr/local/bin/protoc $(shell find /var/www/proto -type f -name "*.proto") \
		--js_out=import_style=commonjs:/var/www/grpc-web \
		--grpc-web_out=import_style=commonjs,mode=grpcwebtext:/var/www/grpc-web \
		--plugin=protoc-gen-grpc-web=/usr/local/bin/protoc-gen-grpc-web \
		--proto_path /var/www/proto
cert:
	mkdir -p /var/www/config/tls
	openssl req -newkey rsa:2048 -nodes -keyout /var/www/config/tls/app.key -x509 -days 365 -out /var/www/config/tls/app.crt