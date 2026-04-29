#!/bin/bash
set -eu

echo "À espera de db:3306..."
for ((i = 0; i < 120; i++)); do
  if (echo >/dev/tcp/db/3306) >/dev/null 2>&1; then
    echo "MariaDB aceita ligações."
    break
  fi
  sleep 1
  if [[ $i -eq 119 ]]; then
    echo "Timeout à espera de db:3306" >&2
    exit 1
  fi
done

set +e
chown -R www-data:www-data .
set -e

if [ ! -f ".env" ]; then
  cp .env.example .env
fi
if [ ! -f ".env.testing" ] && [ -f ".env.testing.example" ]; then
  cp .env.testing.example .env.testing
fi

if ! grep -qE '^APP_KEY=[^[:space:]]+' .env 2>/dev/null; then
  php artisan key:generate --force --no-interaction
fi

composer install --no-interaction --prefer-dist --optimize-autoloader

php artisan migrate --force --no-interaction

exec php-fpm
