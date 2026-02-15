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

\* Použijte buď `SFTP_PASSWORD` nebo `SSH_PRIVATE_KEY`.

## Co se nasazuje

- Vše kromě: `node_modules`, `.git`, `vendor`, `data/*.db`, `api/credentials/*.json`
- Složky `data/` a `api/credentials/` se vytvoří prázdné

## Po nasazení na serveru

1. Nastavte `api/config.php` (e-mail, heslo admin, MySQL pokud používáte)
2. Zajistěte zapisovatelnost složky `data/` (pro SQLite)
3. Pro Google Calendar: nahrajte `api/credentials/google-calendar.json`
4. Spusťte `composer install` v kořeni projektu (pro Google Calendar API)
