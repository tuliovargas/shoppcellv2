#!/usr/bin/env bash
#
# Backup da base Laravel (MariaDB) → ficheiro .sql.gz em docker/dumps/ e opcionalmente Google Drive (rclone).
#
# 1) Instalar rclone na VPS (uma vez): https://rclone.org/install/
# 2) Configurar remoto Google Drive:  rclone config
#    — cria por ex. um remoto chamado "gdrive" (nome por defeito abaixo).
# 3) Correr na raiz do projeto:
#      ./scripts/backup-db-to-gdrive.sh
#    Com destino explícito no Drive:
#      RCLONE_REMOTE=gdrive RCLONE_PATH=Backups/shoppcell ./scripts/backup-db-to-gdrive.sh
#
# Só dump local (sem rclone):  SKIP_RCLONE=1 ./scripts/backup-db-to-gdrive.sh
#
# Cron diário: usa o wrapper scripts/cron-daily-backup.sh (instalado no crontab da VPS como opc).
#
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
COMPOSE="${COMPOSE_FILE:-docker-compose-prod.yaml}"
RCLONE_REMOTE="${RCLONE_REMOTE:-gdrive}"
RCLONE_PATH="${RCLONE_PATH:-shoppcell/db}"
SKIP_RCLONE="${SKIP_RCLONE:-0}"

if [[ ! -f "$ROOT/.env" ]]; then
  echo "Falta .env na raiz do projeto." >&2
  exit 1
fi

raw="$(grep '^DB_DATABASE=' "$ROOT/.env" 2>/dev/null | tail -1 | cut -d= -f2- | tr -d '\r')"
raw="${raw//\"/}"
raw="${raw//\'/}"
DB_NAME="${raw:-pdv}"

TS="$(date -u +%Y%m%d-%H%M%S)"
OUT_DIR="$ROOT/docker/dumps"
mkdir -p "$OUT_DIR"
OUT_FILE="${OUT_DIR}/backup-${DB_NAME}-${TS}.sql.gz"

echo "Dump de ${DB_NAME} → ${OUT_FILE} …"
if ! command -v sudo >/dev/null 2>&1; then
  SUDO=""
else
  SUDO="sudo "
fi

# Palavra-passe vem da env do contentor MariaDB ($MYSQL_ROOT_PASSWORD).
${SUDO}docker compose -f "$COMPOSE" exec -T db bash -eo pipefail -c \
  "mariadb-dump -uroot -p\"\$MYSQL_ROOT_PASSWORD\" --single-transaction --quick --routines --triggers --databases \"$DB_NAME\"" \
  | gzip -c >"$OUT_FILE"

SIZE="$(du -h "$OUT_FILE" | cut -f1)"
echo "Concluído (${SIZE})."

if [[ "$SKIP_RCLONE" == "1" ]]; then
  echo "SKIP_RCLONE=1 — não enviei para o Google Drive."
  exit 0
fi

if ! command -v rclone >/dev/null 2>&1; then
  echo "rclone não está instalado. Instala com: https://rclone.org/install/  depois: rclone config" >&2
  echo "O ficheiro local mantém-se em: $OUT_FILE" >&2
  exit 0
fi

REMOTE="${RCLONE_REMOTE}:${RCLONE_PATH}/"
echo "A enviar para ${REMOTE} …"
rclone copyto "$OUT_FILE" "${REMOTE}$(basename "$OUT_FILE")" --verbose --retries 3

# Opcional: apagar cópias locais antigas (últimos 14 dias)
find "$OUT_DIR" -maxdepth 1 -name "backup-*.sql.gz" -mtime +14 -type f -delete 2>/dev/null || true

echo "Backup no Google Drive concluído."
