# Automatický deploy přes FTP

Cíl: **https://walance.netwalkiing.cz**

Při každém push na `master` nebo `main` se projekt automaticky nasadí na server přes FTP.

## Nastavení GitHub Secrets

V repozitáři: **Settings → Secrets and variables → Actions** přidejte:

| Secret | Povinné | Popis |
|--------|---------|-------|
| `FTP_HOST` | ano | Adresa FTP serveru (např. `ftp.netwalkiing.cz` nebo IP) |
| `FTP_USERNAME` | ano | FTP uživatel |
| `FTP_PASSWORD` | ano | FTP heslo |
| `FTP_REMOTE_PATH` | ano | Cesta na serveru, musí končit `/` (např. `public_html/` nebo `www/walance/`) |
| `FTP_PORT` | ne | Port (výchozí 21) |
| `FTP_PROTOCOL` | ne | `ftp` nebo `ftps` (výchozí `ftp`) |

## Co se nasazuje

- Vše kromě: `node_modules`, `.git`, `vendor`, `data/*.db`, `api/credentials/*.json`, `.github`

## Po nasazení na serveru

1. Nastavte `api/config.php` (e-mail, heslo admin, MySQL pokud používáte)
2. Zajistěte zapisovatelnost složky `data/` (pro SQLite)
3. Pro Google Calendar: nahrajte `api/credentials/google-calendar.json`
4. Spusťte `composer install` v kořeni projektu (pro Google Calendar API)
