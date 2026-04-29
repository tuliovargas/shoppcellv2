#!/usr/bin/env bash
# Emite certificado Let's Encrypt (HTTP-01) e copia fullchain/privkey para docker/nginx/ssl/
# Requisitos: nginx a correr na 80; DNS aponta para este servidor; Cloudflare em Full ok.
# Uso na raiz do projeto:
#   export LE_EMAIL="teu@email.com"
#   export LE_DOMAIN="app.lojashoppcell.com.br"   # opcional
#   sudo ./scripts/letsencrypt-issue.sh
# Teste (staging): export LE_STAGING=1

set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
COMPOSE="${COMPOSE_FILE:-docker-compose-prod.yaml}"
DOMAIN="${LE_DOMAIN:-app.lojashoppcell.com.br}"
EMAIL="${LE_EMAIL:-}"
STAGING_FLAG=()
if [[ "${LE_STAGING:-0}" == "1" ]]; then
  STAGING_FLAG=(--staging)
fi

if [[ -z "$EMAIL" ]]; then
  echo "Define LE_EMAIL, ex.: export LE_EMAIL=admin@exemplo.com" >&2
  exit 1
fi

mkdir -p docker/certbot/www docker/certbot/conf

echo "A pedir certificado para ${DOMAIN} (email ${EMAIL})…"
sudo docker compose -f "$COMPOSE" run --rm --no-deps \
  certbot certonly \
  --webroot -w /var/www/docker/certbot/www \
  --email "$EMAIL" --agree-tos --no-eff-email \
  "${STAGING_FLAG[@]}" \
  -d "$DOMAIN"

LIVE="$ROOT/docker/certbot/conf/live/$DOMAIN"
if [[ ! -f "$LIVE/fullchain.pem" ]]; then
  echo "Falha: não encontrei $LIVE/fullchain.pem" >&2
  exit 1
fi

install -m 0644 "$LIVE/fullchain.pem" docker/nginx/ssl/fullchain.pem
install -m 0600 "$LIVE/privkey.pem" docker/nginx/ssl/privkey.pem

echo "PEMs copiados para docker/nginx/ssl/. A recarregar nginx…"
sudo docker compose -f "$COMPOSE" exec nginx nginx -s reload

echo "Concluído. Cloudflare: usar SSL/TLS em Full (strict) com este certificado na origem."
