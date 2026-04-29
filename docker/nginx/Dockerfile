FROM nginx:1.15.0-alpine

RUN apk add --no-cache netcat-openbsd ca-certificates

RUN rm -f /etc/nginx/conf.d/default.conf

WORKDIR /var/www
