#!/usr/bin/env bash
# Altera a palavra-passe do root na MariaDB (volume existente) e sincroniza .env + serviços.
# Correr na raiz do projeto (ex.: na VPS), com o contentor `db` a correr.
#
#   DB_PASSWORD_OLD=root DB_PASSWORD_NEW='SenhaSegura123' sudo -E ./scripts/mysql-set-root-password.sh
#
# Depois pode recolocar o compose (DB_PASSWORD já no .env) sem perder dados: sudo docker compose -f docker-compose-prod.yaml up -d db phpmyadmin app

set -euo pipefail
ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"
COMPOSE="${COMPOSE_FILE:-docker-compose-prod.yaml}"
OLD_PW="${DB_PASSWORD_OLD:-}"
NEW_PW="${DB_PASSWORD_NEW:-}"

if [[ -z "${OLD_PW}" || -z "${NEW_PW}" ]]; then
  printf '%s\n' "Uso (na raiz do projeto):" "  DB_PASSWORD_OLD=root DB_PASSWORD_NEW='NovaSenha' sudo -E bash ./scripts/mysql-set-root-password.sh" >&2
  exit 1
fi

# Aspas SQL (') duplicadas
esc_sql_single() {
  python3 -c "import sys; print(sys.argv[1].replace(\"'\", \"''\"))" "$1"
}
ESC_NEW="$(esc_sql_single "$NEW_PW")"

echo "A alterar utilizador MariaDB root…"
sudo docker compose -f "$COMPOSE" exec -T db mariadb -u root "-p${OLD_PW}" <<< "ALTER USER 'root'@'%' IDENTIFIED BY '${ESC_NEW}'; ALTER USER 'root'@'localhost' IDENTIFIED BY '${ESC_NEW}'; FLUSH PRIVILEGES;"

ENVF="$ROOT/.env"
if [[ ! -f "$ENVF" ]]; then
  touch "$ENVF"
fi
export ROOT
export DB_PASSWORD_NEW="$NEW_PW"
python3 <<'PYCODE'
import os, re
from pathlib import Path
root = Path(os.environ["ROOT"])
password = os.environ["DB_PASSWORD_NEW"]
path = root / ".env"
text = path.read_text(encoding="utf-8")
line = f"DB_PASSWORD={password}\n"
if re.search(r"^DB_PASSWORD=", text, re.M):
    text = re.sub(r"^DB_PASSWORD=.*(?:\r?\n|$)", line, text, flags=re.M, count=1)
else:
    text = text.rstrip() + "\n" + line
path.write_text(text, encoding="utf-8")
PYCODE
unset DB_PASSWORD_NEW

chmod 600 "$ENVF" 2>/dev/null || true

echo "A atualizar Laravel config cache e phpMyAdmin + app…"
sudo docker compose -f "$COMPOSE" up -d db phpmyadmin app
sudo docker compose -f "$COMPOSE" exec -T app php artisan config:clear

echo "Concluído. Testa login na app e phpMyAdmin (root + nova senha)."
