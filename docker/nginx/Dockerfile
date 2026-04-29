FROM nginx:1.15.0-alpine

RUN apk add --no-cache netcat-openbsd ca-certificates

# Remover *todos* os .conf da imagem base; imagens antigas podem ter trazido nginx.conf com SSL.
RUN rm -f /etc/nginx/conf.d/*.conf

WORKDIR /var/www
