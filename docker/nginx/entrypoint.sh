#!/bin/sh
set -eu
cd /var/www

OUT=/etc/nginx/conf.d/default.conf
cp ./docker/nginx/nginx.conf "${OUT}"

if [ -r /etc/nginx/ssl/custom/fullchain.pem ] && [ -r /etc/nginx/ssl/custom/privkey.pem ]; then
  printf '\n' >> "${OUT}"
  cat ./docker/nginx/nginx-443.conf.optional >> "${OUT}"
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
