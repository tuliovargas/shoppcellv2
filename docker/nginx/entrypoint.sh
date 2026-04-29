#!/bin/sh
set -eu
cd /var/www

# $fcgi_forwarded_scheme (map) tem de existir antes do include laravel-php.inc.
# Copiar do volume montado (.:/var/www) — não depender só da imagem Docker no build
# (na VPS faltava 00-map → "unknown fcgi_forwarded_scheme" → nginx -t falhava → 521 na Cloudflare).
MAP_SRC=./docker/nginx/conf.d/00-map-cloudflare.conf
MAP_DST=/etc/nginx/conf.d/00-map-cloudflare.conf
if [ ! -f "$MAP_SRC" ]; then
  echo "error: em falta $MAP_SRC (define o map X-Forwarded-Proto vs \$scheme)" >&2
  exit 1
fi
cp "$MAP_SRC" "$MAP_DST"

OUT=/etc/nginx/conf.d/default.conf
cp ./docker/nginx/nginx.conf "${OUT}"

SSL_CHAIN=""
SSL_KEY=""

if [ -r /etc/nginx/ssl/custom/fullchain.pem ] && [ -r /etc/nginx/ssl/custom/privkey.pem ]; then
  SSL_CHAIN=/etc/nginx/ssl/custom/fullchain.pem
  SSL_KEY=/etc/nginx/ssl/custom/privkey.pem
elif command -v openssl >/dev/null 2>&1; then
  # Sem PEM na VPS: mesmo assim subir HTTPS (autoassinado). Cloudflare "Full"
  # liga à origem na 443 — sem listener = recusa TCP = erro 521; http://IP:80 funcionava mesmo assim.
  mkdir -p /etc/nginx/ssl/ephemeral
  SSL_CHAIN=/etc/nginx/ssl/ephemeral/fullchain.pem
  SSL_KEY=/etc/nginx/ssl/ephemeral/privkey.pem
  if [ ! -f "$SSL_CHAIN" ] || [ ! -f "$SSL_KEY" ]; then
    # SAN com o hostname do domínio (browsers/aviso menor); PEM reais LetsEncrypt ou Origin CA são preferíveis em produção.
    cat >/tmp/ephemeral.cnf << 'EOFCONF'
[req]
distinguished_name = req_distinguished_name
x509_extensions = v3_req
prompt = no
[req_distinguished_name]
CN = app.lojashoppcell.com.br
[v3_req]
subjectAltName = @alt_names
[alt_names]
DNS.1 = app.lojashoppcell.com.br
DNS.2 = localhost
EOFCONF
    openssl req -x509 -nodes -days 397 -newkey rsa:2048 \
      -keyout "$SSL_KEY" -out "$SSL_CHAIN" \
      -config /tmp/ephemeral.cnf -extensions v3_req
    rm -f /tmp/ephemeral.cnf
  fi
else
  echo "warning: openssl indisponível e sem PEMs — só HTTP 80." >&2
fi

if [ -n "$SSL_CHAIN" ] && [ -n "$SSL_KEY" ]; then
  printf '\n' >> "${OUT}"
  sed "s|@@FULLCHAIN@@|${SSL_CHAIN}|g; s|@@PRIVKEY@@|${SSL_KEY}|g" \
    ./docker/nginx/nginx-443.ssl.conf.template >> "${OUT}"
fi

i=0
while ! nc -z app 9000; do
  i=$((i + 1))
  if [ "$i" -ge 120 ]; then
    echo "error: timeout à espera de app:9000" >&2
    exit 1
  fi
  sleep 1
done

nginx -t
exec nginx -g "daemon off;"
