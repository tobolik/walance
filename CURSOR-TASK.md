# ZADÁNÍ: Konverze homepage-v2.html → homepage-v2.php

## Cíl
Převést statický `homepage-v2.html` na funkční `homepage-v2.php` se správnou českou diakritikou, PHP integrací a plně funkčním rezervačním modálem s kalendářem.

---

## Kontext projektu

### Architektura
- **Frontend**: Samostatný PHP soubor (single-page), inline CSS (bez Tailwind CDN), inline SVG ikony (bez Lucide)
- **Backend**: PHP API v `/api/` — kontaktní formulář, rezervace, sloty, Google Calendar
- **DB**: MySQL (soft-update pattern přes `crud.php`)
- **Konfigurace**: `api/config.php` → `api/version.php` + `api/config.public.php` + `api/config.local.php`

### Klíčové API endpointy
| Endpoint | Metoda | Popis |
|---|---|---|
| `api/contact.php` | POST JSON `{name, email, message}` | Kontaktní formulář → CRM + e-mail |
| `api/booking.php` | POST JSON `{name, email, phone, date, time, message}` | Rezervace → CRM + Google Calendar + e-mail |
| `api/slots.php?month=YYYY-MM` | GET | Vrací `{slots, availability, slots_detail}` pro měsíční kalendář |

### Zdrojové soubory k prostudování
1. `homepage-v2.html` — základ designu V2 (inline CSS, bez Tailwind)
2. `index.php` — stávající funkční stránka (Tailwind + Lucide) — zdroj pro rezervační modál a JS logiku
3. `api/contact.php` — API kontaktního formuláře
4. `api/booking.php` — API rezervací
5. `api/slots.php` — API časových slotů
6. `api/config.php` + `api/config.public.php` — konfigurace

---

## Úkol 1: PHP integrace

Na začátek souboru přidej:
```php
<?php require_once __DIR__ . '/api/config.php'; $v = defined('APP_VERSION') ? APP_VERSION : '1.0.0'; ?>
```

Do HTML hlavičky přidej komentář s verzí:
```html
<!-- VERSION: <?= htmlspecialchars($v) ?> -->
```

Do patičky přidej verzi:
```html
<span style="...">v<?= htmlspecialchars($v) ?></span>
```

Na Google Fonts URL přidej cache-busting:
```
?v=<?= htmlspecialchars($v) ?>
```

---

## Úkol 2: Oprava české diakritiky

Celý `homepage-v2.html` je psaný česky **bez diakritiky** (háčky, čárky). Existuje pár nekonzistencí, kde diakritika je (řádek 1250: "Mikrozměny, které běží..."). Vše sjednoť na **správnou češtinu s diakritikou**.

### Kompletní seznam oprav (sekce po sekci):

#### Navigace
| Původní | Správně |
|---|---|
| Problem | Problém |
| Pribeh | Příběh |
| Nabidka | Nabídka |
| Audit Zdarma | Audit zdarma |

#### Hero
| Původní | Správně |
|---|---|
| Anatomie udrzitelneho vykonu | Anatomie udržitelného výkonu |
| Vas lidsky | Váš lidský |
| hardware hori. | hardware hoří. |
| Zvyste vykon tymu i sebe ne tim, ze budete pracovat vic, ale tim, ze | Zvyšte výkon týmu i sebe ne tím, že budete pracovat víc, ale tím, že |
| prestanete bojovat s vlastni biologii | přestanete bojovat s vlastní biologií |
| ZASTAVIT VYHORENI | ZASTAVIT VYHOŘENÍ |
| PRIBEH ZAKLADATELKY | PŘÍBĚH ZAKLADATELKY |

#### Trust bar
| Původní | Správně |
|---|---|
| Piliru metody | Pilířů metody |
| Usetrenej cas denne | Ušetřený čas denně |
| Vrstvy pristupu | Vrstvy přístupu |
| 4 tydny | 4 týdny |

#### Problém (sekce)
| Původní | Správně |
|---|---|
| Poznavate se? | Poznáváte se? |
| Vas byznys software je genialni. | Váš byznys software je geniální. |
| Snazite se ridit formuli 1 se zatazenou rucni brzdou. To neni stari. To je systemova chyba. | Snažíte se řídit formuli 1 se zataženou ruční brzdou. To není stáří. To je systémová chyba. |
| Mozkova mlha po obede. Rozhodovaci paralyza. Neschopnost "vypnout" praci doma. Jedete na autopilot. | Mozková mlha po obědě. Rozhodovací paralýza. Neschopnost „vypnout" práci doma. Jedete na autopilot. |
| Telo (Hardware) | Tělo (Hardware) |
| Bolest krcni patere vystreljici do rukou. Melke dychani. Chronicka unava, kterou spanek neresi. | Bolest krční páteře vystřelující do rukou. Mělké dýchání. Chronická únava, kterou spánek neřeší. |
| Tym (Vykon) | Tým (Výkon) |
| Prezentismus: lide sedi u pocitacu, ale jejich kognitivni vykon je na 40 %. Vyhoreli lide netvofi hodnoty. | Prezentismus: lidé sedí u počítačů, ale jejich kognitivní výkon je na 40 %. Vyhořelí lidé netvoří hodnoty. |
| To neni stari. To je systemova chyba. | To není stáří. To je systémová chyba. |
| Metoda WALANCE opravuje vsechny tri vrstvy najednou — telo, mysl i navyky. | Metoda WALANCE opravuje všechny tři vrstvy najednou — tělo, mysl i návyky. |

#### Metoda WALANCE
| Původní | Správně |
|---|---|
| Neucim vas cvicit. | Neučím vás cvičit. |
| Ucim vas pracovat. | Učím vás pracovat. |
| Metoda WALANCE neni wellness benefit. Je to provozni manual k vasemu telu. Propojuje fyzioterapii, neurovedu a leadership. | Metoda WALANCE není wellness benefit. Je to provozní manuál k vašemu tělu. Propojuje fyzioterapii, neurovědu a leadership. |
| Telo & Fyzio. Odstranime fyzicke treni. Konec bolesti zad znamena lepsi prokrveni mozku. Opravime sasi, aby motor mohl bezet naplno. | Tělo & Fyzio. Odstraníme fyzické tření. Konec bolestí zad znamená lepší prokrvení mozku. Opravíme šasi, aby motor mohl běžet naplno. |
| Job Crafting. Designujeme praci tak, aby vas nabijela, ne vysavala. Sladime vase silne stranky s naplni prace. | Job Crafting. Designujeme práci tak, aby vás nabíjela, ne vysávala. Sladíme vaše silné stránky s náplní práce. |
| Operacni system | Operační systém |
| Navyky & Ritualy | Návyky & Rituály |
| Automatizace zdravi, abyste na nej nemuseli myslet. | Automatizace zdraví, abyste na něj nemuseli myslet. |

#### Příběh (Story)
| Původní | Správně |
|---|---|
| Pripadova studie | Případová studie |
| Teorie konci, kdyz si zranite koleno. | Teorie končí, když si zraníte koleno. |
| Ja to ted ziju. | Já to teď žiju. |
| Jmenuji se Jana. Jsem fyzioterapeutka a manazerka. Roky jsem ucila firmy, jak nevyhoret. A pak prisla lekce pokory. | Jmenuji se Jana. Jsem fyzioterapeutka a manažerka. Roky jsem učila firmy, jak nevyhořet. A pak přišla lekce pokory. |
| V lednu 2026 me tezky uraz kolene upoutal na luzko. Mohla jsem zavrit firmu. Misto toho jsem se stala Klientem 0. | V lednu 2026 mě těžký úraz kolene upoutal na lůžko. Mohla jsem zavřít firmu. Místo toho jsem se stala Klientem 0. |
| Aplikuji metodu WALANCE v extremnich podminkach. Ridim byznys horizontalne. A funguje to. Mam cistsi hlavu nez vy v kancelari. | Aplikuji metodu WALANCE v extrémních podmínkách. Řídím byznys horizontálně. A funguje to. Mám čistší hlavu než vy v kanceláři. |
| Pokud dokazu udrzet vysoky vykon ja z postele, naucim to i vas. | Pokud dokážu udržet vysoký výkon já z postele, naučím to i vás. |
| Jana na luzku s ortezou a laptopem — autenticky zaber zakladatelky WALANCE | Jana na lůžku s ortézou a laptopem — autentický záběr zakladatelky WALANCE |

#### ROI sekce
| Původní | Správně |
|---|---|
| Matematika je neuprosna | Matematika je neúprosná |
| Tolik casu denne staci usetrit (diky absenci bolesti zad), aby se investice vratila. | Tolik času denně stačí ušetřit (díky absenci bolestí zad), aby se investice vrátila. |
| My cilime na | My cílíme na |
| denne navic | denně navíc |
| Vynasobte si to hodinovou sazbou vasich klicovych lidi. | Vynásobte si to hodinovou sazbou vašich klíčových lidí. |

#### Produkty
| Původní | Správně |
|---|---|
| Spoluprace | Spolupráce |
| Jak muzeme spolupracovat? | Jak můžeme spolupracovat? |
| Strategie sita na miru vasi aktualni situaci. | Strategie šitá na míru vaší aktuální situaci. |
| Pro tymy, ktere jedou na dluh a potrebuji zastavit uniky energie. | Pro týmy, které jedou na dluh a potřebují zastavit úniky energie. |
| Audit "energetickych der" | Audit „energetických děr" |
| Fyzio-ergonomie pracoviste | Fyzio-ergonomie pracoviště |
| 4 tydny hybridniho mentoringu | 4 týdny hybridního mentoringu |
| Tvrda data, zadne ezo | Tvrdá data, žádné ezo |
| Pro lidry (1-on-1) | Pro lídry (1-on-1) |
| Strategicka konzultace s Janou (Klient 0). Krizove rizeni vas samotnych. | Strategická konzultace s Janou (Klient 0). Krizové řízení vás samotných. |
| Hloubkova diagnostika | Hloubková diagnostika |
| Redesign pracovniho kalendare | Redesign pracovního kalendáře |
| Okamzita uleva od tlaku | Okamžitá úleva od tlaku |
| System, jak fungovat horizontalne | Systém, jak fungovat horizontálně |
| REZERVOVAT TERMIN | REZERVOVAT TERMÍN |
| Kapacita omezena (Uraz) | Kapacita omezena (Úraz) |
| Webinar: Jak ridit firmu a nezblaznit se, i kdyz telo stavkuje. | Webinář: Jak řídit firmu a nezbláznit se, i když tělo stávkuje. |
| 3 okamzite techniky | 3 okamžité techniky |
| E-book s manualem | E-book s manuálem |
| Pristup do komunity | Přístup do komunity |
| Zaznam masterclass | Záznam masterclass |
| STAHNOUT ZDARMA | STÁHNOUT ZDARMA |

#### Kontakt
| Původní | Správně |
|---|---|
| Napiste nam | Napište nám |
| Mate dotaz nebo chcete rezervovat termin? Ozvet se nam. | Máte dotaz nebo chcete rezervovat termín? Ozvěte se nám. |
| Kontaktni formular | Kontaktní formulář |
| Jmeno * | Jméno * |
| Vase jmeno | Vaše jméno |
| Zprava * | Zpráva * |
| Jak vam muzeme pomoci? | Jak vám můžeme pomoci? |
| ODESLAT ZPRAVU | ODESLAT ZPRÁVU |
| Rezervace terminu | Rezervace termínu |
| Vyberte si volny termin v kalendari. Rezervace je napojena na Google Calendar. | Vyberte si volný termín v kalendáři. Rezervace je napojena na Google Calendar. |
| OTEVRIT KALENDAR | OTEVŘÍT KALENDÁŘ |
| Nebo napiste primo na | Nebo napište přímo na |

#### FAQ
| Původní | Správně |
|---|---|
| Caste dotazy | Časté dotazy |
| Jak dlouho trva typicka spoluprace? | Jak dlouho trvá typická spolupráce? |
| 4tydenni | 4týdenní |
| individualni a prizpusobuje se vasi situaci — od jednorázove konzultace po dlouhodoby mentoring | individuální a přizpůsobuje se vaší situaci — od jednorázové konzultace po dlouhodobý mentoring |
| Potrebuji byt fyzicky v Praze? | Potřebuji být fyzicky v Praze? |
| Spoluprace probiha hybridne — online konzultace, video hovory a fyzio cviceni na video. Osobni setkani je mozne, ale neni nutne. | Spolupráce probíhá hybridně — online konzultace, video hovory a fyzio cvičení na video. Osobní setkání je možné, ale není nutné. |
| Jak se lisi WALANCE od klasickeho koucingu? | Jak se liší WALANCE od klasického koučingu? |
| WALANCE propojuje fyzioterapii s leadershipem. Nepracujeme jen s myslenim, ale i s telem. Zacinate od hardware (telo) a postupujete k software (mysl) a operacnimu systemu (navyky). Zadny jiny kouc toto nepropojuje. | WALANCE propojuje fyzioterapii s leadershipem. Nepracujeme jen s myšlením, ale i s tělem. Začínáte od hardware (tělo) a postupujete k software (mysl) a operačnímu systému (návyky). Žádný jiný kouč toto nepropojuje. |
| Jak zacit? | Jak začít? |
| Napiste nam pres formular nebo si rovnou rezervujte termin v kalendari. Zacneme kratkym auditem vasi situace — zdarma a nezavazne. | Napište nám přes formulář nebo si rovnou rezervujte termín v kalendáři. Začneme krátkým auditem vaší situace — zdarma a nezávazně. |

#### Patička
| Původní | Správně |
|---|---|
| Byznys neni sprint. Je to maraton v pohybu. | Byznys není sprint. Je to maraton v pohybu. |
| Vsechna prava vyhrazena. | Všechna práva vyhrazena. |

#### JavaScript chybové hlášky
| Původní | Správně |
|---|---|
| Chyba pri odesilani. | Chyba při odesílání. |
| Chyba pripojeni. Zkuste to pozdeji nebo napiste na info@walance.cz | Chyba připojení. Zkuste to později nebo napište na info@walance.cz |

---

## Úkol 3: Přidání rezervačního modálu s měsíčním kalendářem

Převeď rezervační modál z `index.php` do V2 designu. Klíčové požadavky:

### HTML struktura modálu
- Fixed overlay s backdrop-blur
- Centrovaný bílý box s rounded corners (24px), max-width 640px
- Sticky header s tlačítkem zavřít (SVG "X")
- Měsíční kalendář s navigací (předchozí/další měsíc)
- Dny v týdnu: Po, Út, St, Čt, Pá, So, Ne
- Po výběru dne: panel s časovými sloty
- Po výběru času: formulář (jméno, e-mail, telefon, poznámka)
- Submit tlačítko "REZERVOVAT"

### CSS pro modál (inline, bez Tailwind!)
Modál musí používat **stejný design systém** jako zbytek V2 stránky:
- `var(--cream)` pro pozadí
- `var(--accent)` / `var(--accent-dark)` pro akční prvky
- `var(--ink)` pro text
- `var(--mist)` pro borders
- `.form-group` třídy pro formulářové prvky (už existují v CSS)
- Border-radius: 24px pro modál, 12px pro inputy, 16px pro kalendářní dny
- Fonty: `var(--font-body)` a `var(--font-display)`

### Barevné kódování kalendáře
Použij inline CSS barvy místo Tailwind tříd:
- **Volné termíny (hodně)**: `background: #059669` (emerald-600) → hover `#047857`
- **Volné termíny (méně)**: `background: #10b981` (emerald-500) → hover `#059669`
- **Volné s rezervacemi**: `background: #6ee7b7` (emerald-300)
- **Málo volných**: `background: #d1fae5` (emerald-100)
- **Nedostupné / víkend**: `background: rgba(226,232,240,0.2)` (mist/20)
- **Minulé dny**: `opacity: 0.6`
- **Vybraný den**: `box-shadow: 0 0 0 2px var(--accent);`
- **Vybraný čas**: `background: var(--accent); color: var(--cream);`

### JavaScript logika (převzít z index.php, adaptovat)
Klíčové funkce:
```javascript
// Globální proměnné
const apiBase = 'api';
let calCurrentMonth = '...'; // YYYY-MM
let calData = { slots: {}, availability: {} };
let calSelectedDate = null;

// Funkce
openBookingModal()    // Otevře modál, načte aktuální měsíc
closeBookingModal()   // Zavře modál
loadCalendarMonth()   // GET api/slots.php?month=YYYY-MM
renderCalendar()      // Vykreslí mřížku dnů
selectDate(dateStr)   // Po kliknutí na den — zobrazí časy
addSlotBtn(container, time) // Vytvoří tlačítko času

// Event listenery
cal-prev / cal-next   // Navigace měsíců
booking-form submit   // POST api/booking.php
Escape key            // Zavře modál
```

České názvy měsíců pro kalendář:
```javascript
const monthNames = ['Leden','Únor','Březen','Duben','Květen','Červen',
                    'Červenec','Srpen','Září','Říjen','Listopad','Prosinec'];
```

### Napojení tlačítek na modál
1. **"REZERVOVAT TERMÍN"** v produktu Crisis Mentoring → `onclick="openBookingModal()"`
2. **"OTEVŘÍT KALENDÁŘ"** v kontaktní sekci → `onclick="openBookingModal()"`
3. Odstraň stávající `alert()` fallback

---

## Úkol 4: Kontaktní formulář (už funguje, jen ověřit)

Stávající JS v `homepage-v2.html` už posílá POST na `api/contact.php`. Ověř:
- Fetch URL: `'api/contact.php'` (relativní cesta, funguje z root)
- JSON format: `{name, email, message}`
- Success/error zprávy se zobrazují v `#contact-message`
- Po úspěchu: reset formuláře
- Chybové hlášky: se správnou diakritikou (viz Úkol 2)

---

## Designová pravidla

### NEPOUŽÍVEJ:
- Tailwind CSS (žádné `class="bg-accent text-cream"` apod.)
- CDN knihovny (Lucide, Font Awesome)
- Externě linkované CSS soubory

### POUŽÍVEJ:
- Inline `<style>` blok v `<head>` (rozšiř stávající)
- CSS custom properties (`var(--accent)`, `var(--cream)`, ...)
- Inline SVG ikony (už jsou v homepage-v2.html)
- Nativní HTML elementy (`<details>` pro FAQ, `<form>`, ...)

### Design system (existující proměnné):
```css
--cream: #FAF9F7;
--ink: #1e293b;
--ink-light: #475569;
--ink-muted: #94a3b8;
--accent: #0d9488;
--accent-dark: #0a7a70;
--accent-light: #14b8a6;
--sage: #5a7d5a;
--mist: #e2e8f0;
--mist-light: #f1f5f9;
--white: #ffffff;
--font-body: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
--font-display: 'Fraunces', Georgia, serif;
```

---

## Výstup

Jeden soubor: **`homepage-v2.php`** v root adresáři projektu.

### Checklist před dokončením:
- [ ] PHP hlavička s `require_once __DIR__ . '/api/config.php'`
- [ ] Verze v komentáři a patičce
- [ ] Kompletní česká diakritika (žádný text bez háčků/čárek)
- [ ] Rezervační modál s měsíčním kalendářem (inline CSS, ne Tailwind)
- [ ] Modál: navigace měsíců, výběr dne, výběr času, formulář, submit
- [ ] Kontaktní formulář → `api/contact.php`
- [ ] Rezervační formulář → `api/booking.php`
- [ ] Kalendářní data → `api/slots.php?month=YYYY-MM`
- [ ] Escape klávesa zavře modál
- [ ] Mobile responsive (modál, kalendář, formuláře)
- [ ] Žádný Tailwind, žádný Lucide CDN
- [ ] Všechny SVG ikony inline
- [ ] `openBookingModal()` / `closeBookingModal()` funkce fungují
- [ ] Fade-in animace (Intersection Observer) zachovány

---

## Poznámky

- Soubor `homepage-v2.html` zachovej beze změny (jako referenci)
- Nový soubor `homepage-v2.php` je standalone — funguje po otevření v prohlížeči přes PHP server
- Testování: `php -S localhost:8000` v root projektu, pak otevřít `http://localhost:8000/homepage-v2.php`
- API vyžaduje MySQL databázi a `api/config.local.php` — pokud chybí, modál zobrazí chybovou hlášku "Chyba načtení"
