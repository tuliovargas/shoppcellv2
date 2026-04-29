#!/usr/bin/env bash
# Corre o backup BD → Google Drive e regista outputs (para usar no crontab da VPS).
# Ex.: crontab -e →  0 3 * * * /chemin/projeto/scripts/cron-daily-backup.sh
#
set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
# crontab do utilizador costuma definir HOME; se não, grava na raiz do projeto.
LOGDIR="${HOME:-$ROOT}/logs"
mkdir -p "$LOGDIR"
LOG="${LOGDIR}/shoppcell-backup.log"

{
  echo "=== cron-daily-backup $(date -u +%Y-%m-%dT%H:%M:%SZ) (host $(hostname -s 2>/dev/null || echo '?')) ==="
  cd "$ROOT"
  ./scripts/backup-db-to-gdrive.sh
  echo "--- fim ($(date -u +%Y-%m-%dT%H:%M:%SZ)) ---"
  echo ""
} >>"$LOG" 2>&1
