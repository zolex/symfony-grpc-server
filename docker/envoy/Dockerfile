FROM envoyproxy/envoy:v1.18-latest
COPY ./docker/envoy/envoy.yaml /etc/envoy/envoy.yaml
CMD /usr/local/bin/envoy -c /etc/envoy/envoy.yaml