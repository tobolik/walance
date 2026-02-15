# Automatický deploy přes SFTP

Cíl: **https://walance.cz**

Při každém push na `master` nebo `main` se projekt automaticky nasadí na server přes SFTP.

## Nastavení GitHub Secrets

V repozitáři: **Settings → Secrets and variables → Actions** přidejte:

| Secret | Povinné | Popis |
|--------|---------|-------|
| `SFTP_HOST` | ano | Adresa serveru (např. `sftp.walance.cz` nebo IP) |
| `SFTP_USERNAME` | ano | SFTP uživatel |
| `SFTP_PASSWORD` | ano* | SFTP heslo |
| `SFTP_REMOTE_PATH` | ano | Cesta na serveru – **povinné!** (např. `/www/walance` nebo `/public_html`) |
| `SFTP_PORT` | ne | Port (výchozí 22) |
| `SSH_PRIVATE_KEY` | ano* | SSH privátní klíč (alternativa k heslu) |
| `MIGRATE_TOKEN` | pro auto-migraci | Token pro spuštění migrace po deploy |

\* Použijte buď `SFTP_PASSWORD` nebo `SSH_PRIVATE_KEY`.

### Automatická migrace po deploy

V **Settings → Secrets and variables → Actions**:

1. **Variables** – přidejte `ENABLE_AUTO_MIGRATE` = `true`
2. **Variables** – volitelně `SITE_URL` (výchozí `https://walance.cz`)
3. **Secrets** – přidejte `MIGRATE_TOKEN` (náhodný řetězec)
4. V `api/config.local.php` na serveru nastavte stejnou hodnotu: `define('MIGRATE_TOKEN', 'váš-token');`

## Co se nasazuje

- Vše kromě: `node_modules`, `.git`, `data/*.db`, `api/credentials/*.json`, `api/config.local.php`
- `vendor/` se nasazuje (composer install běží v CI před deployem)
- `config.local.php` zůstává na serveru z ručního nastavení (není v gitu)

## Po nasazení na serveru

1. Zkopírujte `api/config.local.example.php` jako `api/config.local.php` a nastavte (e-mail, MySQL, MIGRATE_TOKEN)
2. Migrace se spustí automaticky po každém deploy (viz výše). Ručně: `php api/migrate.php` nebo `https://walance.cz/api/migrate.php?token=VAŠE_HODNOTA`
3. Zajistěte zapisovatelnost složky `data/` (pro SQLite)
4. Pro Google Calendar: nahrajte `api/credentials/google-calendar.json`
