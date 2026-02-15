# Nastavení WALANCE – formuláře, rezervace, CRM

## Požadavky

- **PHP 7.4+** (s rozšířením PDO SQLite)
- Webový server (Apache, Nginx) nebo vestavěný PHP server

## Rychlý start

```bash
# V adresáři projektu spusťte PHP server
php -S localhost:8000

# Otevřete v prohlížeči
# http://localhost:8000
```

## 1. Kontaktní formulář

- Odesílá na **info@walance.cz** (nastavte v `api/config.php`)
- Ukládá kontakty do SQLite (`data/contacts.db`)
- Funguje bez další konfigurace

## 2. Rezervační kalendář

- Zobrazuje volné sloty (Po–Pá, 9–17 h, každých 30 min)
- Ukládá rezervace do CRM
- Odesílá e-mail na info@walance.cz

### Napojení na Google Calendar

1. Vytvořte projekt na [Google Cloud Console](https://console.cloud.google.com/)
2. Povolte **Google Calendar API**
3. Vytvořte **Service Account** a stáhněte JSON
4. Uložte jako `api/credentials/google-calendar.json`
5. Nainstalujte klienta:
   ```bash
   composer install
   ```
6. Sdílejte kalendář s e-mailem Service Accountu (v Google Calendar → Nastavení → Sdílení)

## 3. CRM administrace

- URL: `http://localhost:8000/admin/`
- **Kontakty** – přehled kontaktů z formuláře a rezervací
- **Rezervace** – správa termínů: potvrdit / zamítnout
- **Výchozí heslo:** `password` – **změňte ihned!**

## 4. MySQL (volitelné)

Pro produkci doporučujeme MySQL místo SQLite:

1. Vytvořte databázi a uživatele v MySQL
2. Spusťte `api/db-mysql.sql` pro vytvoření tabulek
3. V `api/config.php` nastavte:
   - `DB_TYPE` = `'mysql'`
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`

### Změna hesla

```bash
php api/setup-password.php vase_nove_heslo
```

Výstup vložte do `api/config.php` jako `ADMIN_PASSWORD_HASH`.

## Struktura souborů

```
walance/
├── api/
│   ├── config.php      # Konfigurace (e-mail, heslo)
│   ├── contact.php     # Kontaktní formulář
│   ├── booking.php     # Rezervace
│   ├── slots.php       # Dostupné sloty
│   ├── db.php          # Databáze
│   ├── GoogleCalendar.php
│   └── credentials/
│       └── google-calendar.json  # Service Account (volitelné)
├── admin/
│   ├── index.php       # Přihlášení
│   └── dashboard.php   # CRM kontakty
├── data/
│   └── contacts.db     # SQLite (vytvoří se automaticky)
└── index.html
```

## Hosting

Na sdíleném hostingu (např. Wedos, Forpsi):

1. Nahrajte celý projekt
2. Nastavte `api/config.php` (e-mail, heslo)
3. Zajistěte, že složka `data/` je zapisovatelná
4. Pro Google Calendar: nahrajte `vendor/` (po `composer install`)
