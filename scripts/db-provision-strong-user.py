#!/usr/bin/env python3
"""
Cria utilizador MariaDB para Laravel (sc_pdv), gera MYSQL_ROOT_PASSWORD e DB_* fortes no .env.
Uso na raíz do projeto (VPS):

  MYSQL_ROOT_OLD=root sudo -E python3 ./scripts/db-provision-strong-user.py
"""
from __future__ import annotations

import os
import re
import secrets
import subprocess
import sys
from pathlib import Path


APP_DB_USER = "sc_pdv"
COMPOSE_FILE = os.environ.get("COMPOSE_FILE", "docker-compose-prod.yaml")


def strong_pw() -> str:
    return secrets.token_hex(48)


def _sudo_prefix() -> list[str]:
    if os.environ.get("NO_SUDO_DOCKER") == "1":
        return []
    if hasattr(os, "geteuid") and os.geteuid() == 0:
        return []
    return ["sudo"]


def dc(project: Path, *args: str) -> None:
    cmd = [*_sudo_prefix(), "docker", "compose", "-f", COMPOSE_FILE, *args]
    subprocess.run(cmd, cwd=str(project), check=True)


def load_env_kv(path: Path) -> dict[str, str]:
    kv: dict[str, str] = {}
    if not path.is_file():
        return kv
    for raw in path.read_text(encoding="utf-8").splitlines():
        line = raw.strip()
        if not line or line.startswith("#") or "=" not in line:
            continue
        k, _, v = line.partition("=")
        kv[k.strip()] = v.strip().strip('"').strip("'")
    return kv


def save_env_updates(path: Path, updates: dict[str, str]) -> None:
    text = path.read_text(encoding="utf-8") if path.is_file() else ""
    keys_left = set(updates.keys())
    out: list[str] = []
    for line in text.splitlines():
        m = re.match(r"^([A-Za-z_][A-Za-z0-9_]*)=", line)
        if m and m.group(1) in updates:
            k = m.group(1)
            out.append(f"{k}={updates[k]}")
            keys_left.discard(k)
        else:
            out.append(line)
    for k in sorted(keys_left):
        out.append(f"{k}={updates[k]}")
    nl = "\n".join(out)
    if not nl.endswith("\n"):
        nl += "\n"
    path.parent.mkdir(parents=True, exist_ok=True)
    path.write_text(nl, encoding="utf-8")
    try:
        path.chmod(0o600)
    except OSError:
        pass


def q(s: str) -> str:
    return "'" + s.replace("\\", "\\\\").replace("'", "''") + "'"


def main() -> None:
    project = Path.cwd()
    if not (project / COMPOSE_FILE).is_file():
        print("Erro: correr na raíz do projeto (falta docker-compose-prod.yaml).", file=sys.stderr)
        sys.exit(1)

    old_root = os.environ.get("MYSQL_ROOT_OLD", "root")
    env_path = project / ".env"
    kv_before = load_env_kv(env_path)
    db_name = (kv_before.get("DB_DATABASE") or "pdv").strip("`").strip('"').strip("'") or "pdv"

    root_pw = strong_pw()
    app_pw = strong_pw()

    sql_parts = [
        f"DROP USER IF EXISTS '{APP_DB_USER}'@'%'",
        f"DROP USER IF EXISTS '{APP_DB_USER}'@'localhost'",
        f"CREATE USER '{APP_DB_USER}'@'%' IDENTIFIED BY {q(app_pw)}",
        f"CREATE USER '{APP_DB_USER}'@'localhost' IDENTIFIED BY {q(app_pw)}",
        f"GRANT ALL PRIVILEGES ON `{db_name}`.* TO '{APP_DB_USER}'@'%'",
        f"GRANT ALL PRIVILEGES ON `{db_name}`.* TO '{APP_DB_USER}'@'localhost'",
        f"ALTER USER 'root'@'%' IDENTIFIED BY {q(root_pw)}",
        f"ALTER USER 'root'@'localhost' IDENTIFIED BY {q(root_pw)}",
        "FLUSH PRIVILEGES",
    ]
    sql = ";\n".join(sql_parts) + ";\n"

    print("MariaDB — a criar utilizador da app + alterar root…")
    cmd = [
        *_sudo_prefix(),
        "docker",
        "compose",
        "-f",
        COMPOSE_FILE,
        "exec",
        "-T",
        "db",
        "mariadb",
        "-u",
        "root",
        f"-p{old_root}",
    ]
    subprocess.run(cmd, cwd=str(project), input=sql.encode("utf-8"), check=True)

    updates = {
        "MYSQL_ROOT_PASSWORD": root_pw,
        "DB_USERNAME": APP_DB_USER,
        "DB_PASSWORD": app_pw,
    }
    save_env_updates(env_path, updates)

    cred = project / ".mysql-credentials-once-delete-after-copy.txt"
    cred.write_text(
        "Copiar valores para gestor de segredos; apagar este ficheiro depois.\n\n"
        f"MYSQL_ROOT_PASSWORD={root_pw}\n"
        f"DB_USERNAME={APP_DB_USER}\n"
        f"DB_PASSWORD={app_pw}\n"
        f"phpMyAdmin: root / (use MYSQL_ROOT_PASSWORD acima)\n"
        f"Laravel DB_DATABASE={db_name}\n",
        encoding="utf-8",
    )
    try:
        cred.chmod(0o600)
    except OSError:
        pass

    print("Compose up (phpmyadmin/app)…")
    dc(project, "up", "-d", "db", "phpmyadmin", "app")

    subprocess.run(
        [
            *_sudo_prefix(),
            "docker",
            "compose",
            "-f",
            COMPOSE_FILE,
            "exec",
            "-T",
            "app",
            "php",
            "artisan",
            "config:clear",
        ],
        cwd=str(project),
        check=True,
    )

    print(f"Guardado só no servidor e em:\n  {cred}\n(apaga após registares.)")


if __name__ == "__main__":
    main()
