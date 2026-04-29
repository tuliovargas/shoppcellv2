#!/bin/sh
set -eu
cd /var/www

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
    openssl req -x509 -nodes -days 397 -newkey rsa:2048 \
      -keyout "$SSL_KEY" -out "$SSL_CHAIN" \
      -subj "/CN=app.lojashoppcell.com.br"
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

exec nginx -g "daemon off;"
