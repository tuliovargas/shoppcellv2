#!/usr/bin/env bash
# Renovação (correr em cron, ex. 2x/dia: certbot renew só age se faltar <30 dias)
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
COMPOSE="${COMPOSE_FILE:-docker-compose-prod.yaml}"
DOMAIN="${LE_DOMAIN:-app.lojashoppcell.com.br}"

sudo docker compose -f "$COMPOSE" run --rm --no-deps certbot renew
LIVE="$ROOT/docker/certbot/conf/live/$DOMAIN"
if [[ -f "$LIVE/fullchain.pem" ]]; then
  install -m 0644 "$LIVE/fullchain.pem" docker/nginx/ssl/fullchain.pem
  install -m 0600 "$LIVE/privkey.pem" docker/nginx/ssl/privkey.pem
  sudo docker compose -f "$COMPOSE" exec nginx nginx -s reload
fi
