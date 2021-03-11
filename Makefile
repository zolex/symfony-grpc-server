.PHONY: server code cert
server: $(wildcard rr/**/*.go) $(wildcard rr/*.go)
	cd rr \
	&& go get -t . \
	&& go build -a -x -v -o rr \
	&& mv rr ../bin
code:
	rm -rf /var/www/grpc \
	&& mkdir -p /var/www/grpc \
	&& /usr/local/bin/protoc /var/www/proto/*.proto \
		--php_out=/var/www/grpc \
		--php-grpc_out=/var/www/grpc \
		--grpc_out=/var/www/grpc \
		--plugin=protoc-gen-grpc=/usr/local/bin/grpc_php_plugin \
		--plugin=protoc-gen-php-grpc=/root/go/bin/protoc-gen-php-grpc \
		--proto_path /var/www/proto
cert:
	mkdir -p /var/www/config/tls
	openssl req -newkey rsa:2048 -nodes -keyout /var/www/config/tls/app.key -x509 -days 365 -out /var/www/config/tls/app.crt