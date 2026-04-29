#!/bin/sh
set -eu
cd /var/www

# Config (sem templating dockerize; evita amd64 só em amd64 VPS)
cp ./docker/nginx/nginx.conf /etc/nginx/conf.d/nginx.conf

# Espera PHP-FPM (netcat instalado na imagem)
i=0
while ! nc -z app 9000; do
  i=$((i + 1))
  if [ "$i" -ge 120 ]; then
    echo ":: error: timeout à espera de app:9000" >&2
    exit 1
  fi
  sleep 1
done

exec nginx -g "daemon off;"
