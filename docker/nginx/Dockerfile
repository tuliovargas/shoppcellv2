FROM nginx:1.15.0-alpine

RUN apk add --no-cache netcat-openbsd ca-certificates openssl

# Map Cloudflare (00-map) é copiado em runtime pelo entrypoint a partir de /var/www (volume); não COPY na imagem.
RUN rm -f /etc/nginx/conf.d/*.conf

WORKDIR /var/www
