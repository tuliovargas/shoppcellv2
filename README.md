<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Sobre Projeto

LojaPDV é um sistema feito para loja de informática, para venda de produtos e controle de estoque.

> Configure o BD com o SQL MariaBD versão 10;
> Renomeie o arquivo **.env.exemple** e atualize o arquivo para **.env** e insira os dados do BD.

Após isso, dê os seguintes comandos
```javascript
composer install
npm install
php artisan key:generate
php artisan migrate --seed
npm run dev
```
daí o projeto já deve rodar, senão então rode no 
```javascript
php artisan serve
```
Telas do projeto: https://www.figma.com/file/WvpobjeIF7ctgF4bIhzX5S/

## Docker-compose
```shell
docker-compose up -d

docker-compose -f docker-compose-prod.yaml up -d
```

## Backup para Google Drive (requisitos)

Os backups são feitos pelo script **`scripts/backup-db-to-gdrive.sh`** usando **[rclone](https://rclone.org/)** como cliente (upload para uma pasta na tua conta Google Drive). É o método recomendado; não são necessários `client_secret.json` nem `REFRESH_TOKEN` no Laravel para esse fluxo.

### 1. Na máquina do servidor (VPS)

1. **Instalar o rclone** (Linux): [Installing rclone](https://rclone.org/install/).
   ```bash
   sudo -v ; curl https://rclone.org/install.sh | sudo bash
   ```

2. **Conta Google** com espaço suficiente na Drive para `.sql.gz`.

3. **[Google Cloud Console](https://console.cloud.google.com/)** — credenciais **tuas** (recomendado em produção; evita quotas do cliente por defeito do rclone):

   | Passo | Ação |
   |-------|------|
   | Projeto | Cria ou escolhe um projeto. |
   | API | **APIs e serviços** → **Biblioteca** → ativar **Google Drive API**. |
   | Ecrã de consentimento OAuth | Primeiro pedido: **Consentimento OAuth** → tipo externo; quando pedir scopes, garante pelo menos **.../auth/drive** (Drive completo para escrita nos backups). Adiciona a tua conta Google como utilizador teste até publicares se for app “externo”. |
   | Cliente OAuth | **Credenciais** → **Criar credenciais** → **ID do cliente OAuth** → tipo **App para computador** (desktop), **não** “aplicativo da Web”. |

   Ao editar esse cliente OAuth, em **Authorized redirect URIs** adiciona **exatamente** o URI que aparecer nas mensagens do `rclone` (tipo “Redirect URL”), em geral `http://127.0.0.1:53682/` ([documentação oficial](https://rclone.org/drive/)).

4. **Configurar o remoto Drive** como o mesmo utilizador que vai correr cron/backup (`sudo su - eseutil`, etc.):

   ```bash
   rclone config
   ```

   Fluxo habitual: `n` (novo remoto) → nome `gdrive` (é o défaut dos scripts aqui) → storage `drive` → `client_id` / `client_secret` colados da consola (ou Enter para cliente por defeito do rclone) → ao pedir autorização pelo browser segue os avisos do terminal.

   Credenciais e token ficam em **`~/.config/rclone/rclone.conf`**. Depois:

   ```bash
   chmod 600 ~/.config/rclone/rclone.conf
   ```

5. **Testar:**

   ```bash
   rclone lsd gdrive:
   ```

**VPS sem browser** (SSH): segue [Remote setup](https://rclone.org/remote_setup/) — túnel `ssh -L localhost:53682:localhost:53682` ou `rclone authorize` noutra máquina com browser e colar o token no `rclone config`.

### 2. Cliente OAuth só “por defeito” do rclone

Se **não** criares projeto na Google: no `rclone config` aceita o client id/secret por defeito (Enter). Pode falhar em contas com restrições de política; aí usa a secção **1** em cima.

### 3. Integração com este repositório

- **Dump + compressão:** o script gera `docker/dumps/backup-<base>-<data>.sql.gz`.
- **`GOOGLE_DRIVE_CLIENT_ID`** e **`GOOGLE_DRIVE_CLIENT_SECRET`** no ficheiro **`.env` na VPS** (raiz do projeto no servidor), **não** no `.env.example` (este é só modelo e pode ir para o Git). O deploy por **rsync** **não** envia o `.env` — tens de editar na VPS (SSH, `nano`, etc.) ou copiar com `scp` a partir da tua máquina. O **`scripts/backup-db-to-gdrive.sh`** só lê **`$ROOT/.env`**. De seguida exporta-as para `RCLONE_CONFIG_<REMOTO>_…` antes do `rclone copyto` (`<REMOTO>` = `RCLONE_REMOTE`, por defeito `gdrive`). O **refresh token** fica em **`~/.config/rclone/rclone.conf`** após `rclone config` (mesmo utilizador UNIX que o cron). `chmod 640 .env` no servidor.
- **Upload:** se o `rclone` existir e **não** definires `SKIP_RCLONE=1`, o ficheiro é enviado para:
  - remoto: variável **`RCLONE_REMOTE`** (por defeito `gdrive`);
  - pasta na Drive: **`RCLONE_PATH`** (por defeito `shoppcell/db`).
- **Exemplo manual:**
  ```bash
  cd /caminho/do/projeto
  RCLONE_REMOTE=gdrive RCLONE_PATH=Backups/shoppcell ./scripts/backup-db-to-gdrive.sh
  ```
- **Apenas dump local (sem Drive):** `SKIP_RCLONE=1 ./scripts/backup-db-to-gdrive.sh`

### 4. Agendamento (cron)

O wrapper **`scripts/cron-daily-backup.sh`** grava o output em **`~/logs/shoppcell-backup.log`**. Exemplo de linha no `crontab` (todos os dias às 03:00 no relógio do servidor):

```cron
0 3 * * * /caminho/do/projeto/scripts/cron-daily-backup.sh
```

Ajusta o horário ao fuso da VPS (muitas máquinas em nuvem usam **UTC**).

> **Nota:** As variáveis `REFRESH_TOKEN` e `BACKUP_FOLDER` no `.env` eram um desenho antigo; o fluxo atual usa **rclone** e as variáveis **`RCLONE_REMOTE`** / **`RCLONE_PATH`** no ambiente do shell (ou os valores por defeito no script).


