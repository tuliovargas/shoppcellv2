#!/usr/bin/env bash
# Confirma rclone + remoto Drive antes de confiar no cron.
# Corre na VPS (ou WSL/Git Bash com rclone já configurado na mesma máquina onde corres isto).
#
# Uso:
#   ./scripts/verify-rclone-backup.sh
#   RCLONE_REMOTE=meu_drive ./scripts/verify-rclone-backup.sh
#
set -euo pipefail
RCLONE_REMOTE="${RCLONE_REMOTE:-gdrive}"

ok() { echo "OK — $1"; }
fail() {
  echo "ERRO — $*" >&2
  exit 1
}

command -v rclone >/dev/null 2>&1 || fail "rclone não está no PATH. Instala: https://rclone.org/install/"

CONFIG="${RCLONE_CONFIG:-$HOME/.config/rclone/rclone.conf}"
[[ -f "$CONFIG" ]] || fail "Não existe $CONFIG — corre primeiro: rclone config (remoto \"$RCLONE_REMOTE\")."

rclone listremotes | tr -d '\r' | grep -q "^${RCLONE_REMOTE}:$" 2>/dev/null || fail "Sem remoto \"${RCLONE_REMOTE}\". Lista: rclone listremotes — cria esse nome no rclone config."
ok "ficheiro de config existe e remoto \"$RCLONE_REMOTE\" está definido"

rclone lsd "${RCLONE_REMOTE}:" >/dev/null 2>&1 || fail "OAuth/token inválido ou sem rede — corre outra vez: rclone config (reautenticar)."
ok "credenciais da Drive respondem (${RCLONE_REMOTE}:)"

RCLONE_PATH="${RCLONE_PATH:-shoppcell/db}"
REM="${RCLONE_REMOTE}:${RCLONE_PATH}"
rclone mkdir "$REM" 2>/dev/null || true
rclone ls "$REM" >/dev/null 2>&1 || fail "Sem permissão ao caminho \"$REM\". Ajusta RCLONE_PATH ou cria a pasta na Drive."
ok "pastas acessíveis em $REM"

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
if [[ -f "$ROOT/.env" ]] && [[ -x "$(command -v docker 2>/dev/null)" ]]; then
  COMPOSE="${COMPOSE_FILE:-docker-compose-prod.yaml}"
  if docker compose version >/dev/null 2>&1 && [[ -f "$ROOT/$COMPOSE" ]]; then
    if docker compose -f "$ROOT/$COMPOSE" ps --status running -q db 2>/dev/null | grep -q .; then
      ok "contentor MariaDB em execução (docker compose \"$COMPOSE\")"
    else
      echo "(aviso) contentor «db» não está em estado running — corre o backup apenas quando Docker + BD estão levantados."
    fi
  fi
fi

echo ""
echo "Tudo certo para: ./scripts/backup-db-to-gdrive.sh  e  cron com scripts/cron-daily-backup.sh"
