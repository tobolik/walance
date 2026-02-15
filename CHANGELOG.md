# Changelog

Všechny významné změny v projektu WALANCE jsou dokumentovány v tomto souboru.

Formát je založen na [Keep a Changelog](https://keepachangelog.com/cs/1.0.0/).

## [1.0.9] – 2026-02-14

### Přidáno
- **Admin Kalendář:** propojení se schvalováním rezervací – klik na slot s rezervací otevře modal s akcemi (Potvrdit/Zamítnout/Zrušit zamítnutí)
- **api/calendar-bookings.php:** endpoint vrací rezervace s booking ID pro admin kalendář

### Změněno
- **Admin Kalendář:** sjednocené barvy – amber pro „Čeká na potvrzení“, teal pro „Potvrzeno“, šedá pro „Zamítnuto“
- **Admin Kalendář:** po akci na rezervaci se kalendář automaticky obnoví

## [1.0.8] – 2026-02-14

### Přidáno
- **Rezervace:** tlačítko „Zrušit zamítnutí“ u zamítnutých rezervací – vrací stav na „Čeká“

### Změněno
- **Migrace:** odstranění redundantního sloupce `contact_id` z tabulky `bookings` (aplikace používá `contacts_id`)

## [1.0.7] – 2026-02-14

### Přidáno
- **CHANGELOG.md** – historie změn projektu
- **docs/APLIKACE.md** – popis aplikace, architektura, tok dat, konfigurace

## [1.0.6] – 2026-02-14

### Změněno
- **Deploy:** `composer install` běží v CI před nasazením, složka `vendor/` se nahrává na server (Google Calendar API funguje bez ručního composer na serveru)
- **Admin Dostupnost:** výběr Google Calendar – dropdown se seznamem sdílených kalendářů nebo ruční zadání ID
- **Admin Kalendář:** oprava JavaScript chyby – duplicitní deklarace proměnné `today`

## [1.0.5] – 2026-02-14

### Opraveno
- **Admin Kalendář:** oprava chyby „Identifier 'today' has already been declared“ – prázdná mřížka kalendáře

## [1.0.4] – 2026-02-14

### Přidáno
- **Google Calendar:** výběr kalendáře v admin Dostupnost – možnost testovat na vlastním kalendáři a přepnout na kalendář kolegy
- **GoogleCalendar:** metoda `getCalendarList()` – seznam kalendářů dostupných Service Accountu
- **GoogleCalendar:** konstruktor přijímá volitelný parametr `$calendarId` pro přepnutí kalendáře
- **availability.json:** nové pole `google_calendar_id` – uložený výběr kalendáře

## [1.0.3] – 2026-02-14

### Přidáno
- **Admin Dostupnost:** rozbalovací sekce „Události z kalendáře (kontrola)“ – výpis událostí z Google Calendar na příštích 14 dní pro ověření napojení
- **GoogleCalendar:** metoda `getEventsForDisplay()` pro zobrazení událostí

## [1.0.2] – 2026-02-14

### Změněno
- **Konfigurace:** verze pouze v `api/version.php` – web i admin berou verzi automaticky
- **index.html → index.php:** hlavní stránka načítá verzi z PHP, žádné natvrdo
- **Dokumentace:** `.cursor/rules/version-increment.mdc` – kritické pravidlo pro povýšení verze

## [1.0.1] – 2026-02-14

### Přidáno
- **Admin:** verze aplikace v patičce místo v hlavičce
- **Konfigurace:** rozdělení na `config.public.php` (v gitu) a `config.local.php` (gitignore)
- **api/version.php:** samostatný soubor pro verzi aplikace
- **Dostupnost:** blokování konkrétních časů – kalendář + klik na slot pro blokování/odblokování
- **api/availability-block.php:** endpoint pro přepínání blokovaných slotů
- **availability.json:** pole `excluded_slots` pro ručně blokované časy
- **Admin Kalendář:** zobrazení blokovaných slotů (červená barva)

### Změněno
- **Web kalendář:** mřížka jen zelená (volné) / šedá (plný den), výběr času jen volné sloty
- **Admin Kalendář:** nová stránka `calendar.php` s barevnými stavy (zelená / amber / teal)
- **Admin navigace:** odkaz „Kalendář“

## [1.0.0] – 2026-02

### Přidáno
- Web WALANCE – prezentace metody, kontaktní formulář
- Rezervační kalendář – měsíční mřížka, výběr času, formulář rezervace
- CRM administrace – kontakty, rezervace, detail kontaktu s aktivitami
- Přihlášení do adminu (e-mail + heslo z DB)
- Google Calendar integrace – blokování slotů podle událostí, vytváření rezervací
- Nastavení dostupnosti – pracovní doba, interval, pracovní dny, výjimky
- MySQL a SQLite podpora
- Automatický deploy přes SFTP (GitHub Actions)
- Migrace databáze s automatickým spuštěním po deployi
