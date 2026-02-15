# Changelog

Všechny významné změny v projektu WALANCE jsou dokumentovány v tomto souboru.

Formát je založen na [Keep a Changelog](https://keepachangelog.com/cs/1.0.0/).
Datum a čas ve formátu `DD.MM.YYYY HH:MM`.

## [1.0.26] – 14.02.2026 19:00

### Přidáno
- **Rezervace:** po odeslání rezervace dostane klient potvrzovací e-mail s marketingovým textem; konzultant v BCC

## [1.0.25] – 14.02.2026 18:30

### Přidáno
- **Nastavení dostupnosti:** více rozsahů pracovní doby (např. 9–12 a 15–19) – tlačítko „Přidat rozsah“, možnost odebrat rozsah

## [1.0.24] – 14.02.2026 18:00

### Opraveno
- **Admin Kalendář:** při přepnutí měsíce – reset výběru data, aby nedocházelo k TypeError (Object.keys undefined)
- **Admin Kalendář:** zamítnuté sloty – sjednocení stylů s legendou (bg-emerald-400, border-slate-400)

## [1.0.23] – 14.02.2026 17:30

### Změněno
- **Rezervační modal:** zavírací křížek – sticky, zůstává vidět při scrollování dolů

### Přidáno
- **Rezervační modal:** klávesa ESC zavře modal

## [1.0.22] – 14.02.2026 17:15

### Změněno
- **slots.php:** Google Calendar – jeden API call pro celý měsíc místo 20+ volání (rychlejší načítání)
- **Web kalendář:** ohraničení vybraného dne – použit ring-teal-500 (standardní Tailwind)

## [1.0.21] – 14.02.2026 17:00

### Přidáno
- **api/reset-password.php:** reset admin hesla – oprava přihlášení po upgradu PHP (7.4→8.2, známý bug v password_verify)

## [1.0.20] – 14.02.2026 16:45

### Změněno
- **composer.json:** ořezání google/apiclient – ponechán jen Calendar (odstraněno 100+ dalších API služeb). Vendor je nyní malý, deploy rychlý
- **Deploy:** vendor se opět nahrává (je malý díky ořezání)

## [1.0.19] – 14.02.2026 16:35

### Změněno
- **Deploy:** `vendor/` se nenasazuje přes SFTP – google/apiclient má tisíce souborů, upload trval 47+ minut. Nyní se spustí `composer install` pouze na serveru přes SSH (rychlé nasazení)

## [1.0.18] – 14.02.2026 16:20

### Přidáno
- **Deploy:** po nasazení se automaticky spustí `composer install` na serveru přes SSH (Google Calendar API)

## [1.0.17] – 14.02.2026 16:15

### Změněno
- **Google Calendar:** upřesněná chybová hláška – místo „composer require“ nyní „composer install“ (balíček je již v composer.json)
- **Admin Dostupnost:** v nápovědě při chybě přidán návod na spuštění composer install

## [1.0.16] – 14.02.2026 16:00

### Změněno
- **Kalendář (web i admin):** grafické zvýraznění vybraného dne – ring (okraj) kolem kliknutého data

## [1.0.15] – 14.02.2026 15:45

### Změněno
- **Admin Kalendář:** zamítnuté sloty – zelené pozadí se šedým okrajem, title se seznamem zamítnutých jmen
- **Admin Kalendář:** při více zamítnutých na jednom slotu – seznam jmen v modalu s tlačítkem Obnovit u každého
- **api/calendar-bookings.php:** vrací pole rezervací per slot (podpora více zamítnutých)

## [1.0.14] – 14.02.2026 15:30

### Přidáno
- **Rezervace:** při zamítnutí rezervace se automaticky odblokuje daný slot – termín je ihned k dispozici na webu pro ostatní klienty
- **api/availability.php:** funkce `removeExcludedSlot()` pro odebrání slotu z ruční blokace

## [1.0.13] – 14.02.2026 15:15

### Přidáno
- **Anti-caching:** Cache-Control hlavičky na API (slots, calendar-bookings, availability-block, bookings AJAX)
- **Anti-caching:** meta no-cache na admin stránkách (kalendář, rezervace, dostupnost, kontakty, dashboard)
- **Anti-caching:** `cache: 'no-store'` u fetch() volání pro dynamická data

## [1.0.12] – 14.02.2026 15:00

### Změněno
- **CHANGELOG:** český formát data a času (DD.MM.YYYY HH:MM)

## [1.0.11] – 14.02.2026 14:45

### Změněno
- **CHANGELOG:** přidán čas ke každému záznamu (český formát DD.MM.YYYY HH:MM)

## [1.0.10] – 14.02.2026 14:30

### Opraveno
- **Admin Kalendář:** slot s rezervací ve stavu „Čeká“ se zobrazoval červeně (blokováno) místo amber – rezervace má nyní přednost před ruční blokací

## [1.0.9] – 14.02.2026 12:15

### Přidáno
- **Admin Kalendář:** propojení se schvalováním rezervací – klik na slot s rezervací otevře modal s akcemi (Potvrdit/Zamítnout/Zrušit zamítnutí)
- **api/calendar-bookings.php:** endpoint vrací rezervace s booking ID pro admin kalendář

### Změněno
- **Admin Kalendář:** sjednocené barvy – amber pro „Čeká na potvrzení“, teal pro „Potvrzeno“, šedá pro „Zamítnuto“
- **Admin Kalendář:** po akci na rezervaci se kalendář automaticky obnoví

## [1.0.8] – 14.02.2026 11:45

### Přidáno
- **Rezervace:** tlačítko „Zrušit zamítnutí“ u zamítnutých rezervací – vrací stav na „Čeká“

### Změněno
- **Migrace:** odstranění redundantního sloupce `contact_id` z tabulky `bookings` (aplikace používá `contacts_id`)

## [1.0.7] – 14.02.2026 11:20

### Přidáno
- **CHANGELOG.md** – historie změn projektu
- **docs/APLIKACE.md** – popis aplikace, architektura, tok dat, konfigurace

## [1.0.6] – 14.02.2026 10:55

### Změněno
- **Deploy:** `composer install` běží v CI před nasazením, složka `vendor/` se nahrává na server (Google Calendar API funguje bez ručního composer na serveru)
- **Admin Dostupnost:** výběr Google Calendar – dropdown se seznamem sdílených kalendářů nebo ruční zadání ID
- **Admin Kalendář:** oprava JavaScript chyby – duplicitní deklarace proměnné `today`

## [1.0.5] – 14.02.2026 10:30

### Opraveno
- **Admin Kalendář:** oprava chyby „Identifier 'today' has already been declared“ – prázdná mřížka kalendáře

## [1.0.4] – 14.02.2026 10:00

### Přidáno
- **Google Calendar:** výběr kalendáře v admin Dostupnost – možnost testovat na vlastním kalendáři a přepnout na kalendář kolegy
- **GoogleCalendar:** metoda `getCalendarList()` – seznam kalendářů dostupných Service Accountu
- **GoogleCalendar:** konstruktor přijímá volitelný parametr `$calendarId` pro přepnutí kalendáře
- **availability.json:** nové pole `google_calendar_id` – uložený výběr kalendáře

## [1.0.3] – 14.02.2026 09:45

### Přidáno
- **Admin Dostupnost:** rozbalovací sekce „Události z kalendáře (kontrola)“ – výpis událostí z Google Calendar na příštích 14 dní pro ověření napojení
- **GoogleCalendar:** metoda `getEventsForDisplay()` pro zobrazení událostí

## [1.0.2] – 14.02.2026 09:30

### Změněno
- **Konfigurace:** verze pouze v `api/version.php` – web i admin berou verzi automaticky
- **index.html → index.php:** hlavní stránka načítá verzi z PHP, žádné natvrdo
- **Dokumentace:** `.cursor/rules/version-increment.mdc` – kritické pravidlo pro povýšení verze

## [1.0.1] – 14.02.2026 09:00

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

## [1.0.0] – 02.2026

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
