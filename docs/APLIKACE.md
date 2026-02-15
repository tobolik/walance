# WALANCE – popis aplikace

## Účel

WALANCE je webová aplikace pro metodu „anatomie udržitelného výkonu“ – kombinaci koučinku, fyzioterapie a provozního manuálu pro lídry a týmy. Aplikace slouží jako:

1. **Prezentační web** – představení metody, příběh, FAQ
2. **Rezervační systém** – online rezervace termínů
3. **CRM administrace** – správa kontaktů a rezervací

## Architektura

### Veřejná část (web)

- **index.php** – hlavní stránka s prezentací, kontaktním formulářem a rezervačním kalendářem
- Rezervace probíhá v modálním okně: výběr data → výběr času → vyplnění údajů → odeslání

### API (api/)

| Soubor | Účel |
|--------|------|
| `contact.php` | Odeslání zprávy z kontaktního formuláře |
| `booking.php` | Uložení rezervace (DB, Google Calendar, e-mail) |
| `slots.php` | Dostupné časové sloty (volné, blokované, obsazené) |
| `availability-block.php` | Přepnutí blokovaného slotu (admin) |
| `config.php` | Načítání konfigurace (version + public + local) |
| `db.php` | Připojení k databázi (SQLite/MySQL) |
| `crud.php` | CRUD operace se soft-update logikou |
| `migrate.php` | Migrace schématu databáze |
| `GoogleCalendar.php` | Integrace s Google Calendar API |

### Administrace (admin/)

| Stránka | Účel |
|---------|------|
| `index.php` | Přihlášení (e-mail + heslo) |
| `dashboard.php` | Přehled kontaktů z formuláře a rezervací |
| `contact.php` | Detail kontaktu, poznámky, aktivity (telefonát, e-mail, schůzka) |
| `bookings.php` | Správa rezervací – potvrdit / zamítnout |
| `calendar.php` | Kalendář s barevnými stavy (volné / čeká / potvrzeno / blokováno) |
| `availability.php` | Nastavení dostupnosti, výběr Google Calendar, blokování konkrétních časů |

## Tok dat

### Rezervace

1. Uživatel na webu vybere datum a čas
2. `slots.php` vrací volné sloty (zohledňuje: pracovní dobu, blokované dny, Google Calendar, DB rezervace, ručně blokované sloty)
3. Uživatel odešle formulář → `booking.php`
4. Rezervace se uloží do DB, vytvoří událost v Google Calendar, odešle e-mail

### Dostupnost slotů

Priorita blokování (od nejvyšší):

1. **Pracovní dny** – jen vybrané dny v týdnu (výchozí Po–Pá)
2. **Výjimky – blokované dny** – celé dny (např. svátky)
3. **Ručně blokované sloty** – konkrétní časy v admin Dostupnost
4. **Google Calendar** – události z vybraného kalendáře
5. **DB rezervace** – potvrzené a čekající rezervace

## Konfigurace

### Veřejná (v gitu)

- **api/version.php** – verze aplikace
- **api/config.public.php** – výchozí hodnoty slotů, cesty, GOOGLE_CALENDAR_ID

### Lokální (není v gitu)

- **api/config.local.php** – e-mail, DB přihlašovací údaje, MIGRATE_TOKEN
- **api/credentials/google-calendar.json** – Service Account pro Google Calendar
- **data/availability.json** – nastavení dostupnosti (generované adminem)

## Databáze

- **SQLite** (výchozí) – `data/contacts.db`
- **MySQL** (produkce) – tabulky dle `api/db-mysql.sql`

Hlavní entity: `contacts`, `bookings`, `admin_users`, `activities`. Soft-update: záznamy se nemazou, nastavuje se `valid_to`.

## Nasazení

- **GitHub Actions** – push na master spustí deploy přes SFTP
- **composer install** – běží v CI, `vendor/` se nahrává
- **Migrace** – automaticky po deployi (volání `api/migrate.php?token=...`)

## Technologie

- PHP 7.4+, PDO
- Tailwind CSS (CDN), Lucide Icons
- Google Calendar API (google/apiclient)
- Composer pro závislosti
