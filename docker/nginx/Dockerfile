FROM nginx:1.15.0-alpine

RUN apk add --no-cache netcat-openbsd ca-certificates openssl

# Limpar configs default da imagem; em seguida map Cloudflare (/etc/nginx/conf.d primeiro no include).
RUN rm -f /etc/nginx/conf.d/*.conf
COPY docker/nginx/conf.d/*.conf /etc/nginx/conf.d/

WORKDIR /var/www
