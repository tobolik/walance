# ZADÁNÍ PRO CURSOR: Redesign index.php (in-place)

## Shrnutí

Přepiš stávající `index.php` (Tailwind CDN + Lucide CDN, 1050 řádků, 13+ sekcí) na minimalistický V2 design (inline CSS, inline SVG, 8 sekcí) **přímo v souboru `index.php`**.

**Vzorový soubor nového designu: `homepage-v2.html`** — použij jako referenci pro:
- Vizuální design (layout, typografie, barvy)
- Strukturu sekcí (8 sekcí místo 13)
- Inline CSS design system (CSS custom properties)
- Inline SVG ikony (bez Lucide CDN)

**Co už funguje v `index.php` a musí zůstat zachované:**
- PHP hlavička (`require_once __DIR__ . '/api/config.php'`)
- Rezervační modál s měsíčním kalendářem (převést z Tailwind na inline CSS)
- Kontaktní formulář napojený na API (převést z Tailwind na inline CSS)
- JavaScript logika pro booking (slots, calendar, form submit)
- Verze v komentáři a patičce

**Cíl:** Přepsat `index.php` tak, aby používal V2 design z `homepage-v2.html`, ale zachoval veškerou stávající funkčnost (booking modál, kontaktní formulář, PHP integrace).

---

## Podkladové dokumenty (přečti si je!)

| Soubor | Co obsahuje |
|--------|-------------|
| `AUDIT-HOMEPAGE.md` | Kompletní technický + UX audit stávajícího webu — proč redesignujeme |
| `CONTENT-EXTRACTED.md` | Veškerý obsah extrahovaný z index.php — strukturovaně po sekcích |
| `CONTENT_CHECKLIST.md` | Přehled chybějícího obsahu (Lorem ipsum místa) |
| `homepage-v2.html` | **Vzor nového designu** — inline CSS, bez Tailwind, zjednodušená struktura |
| `index.php` | **Soubor k úpravě** — stávající web s fungující logikou |

---

## Proč redesignujeme (klíčové body z auditu)

### Problémy index.php:
- **Tailwind CDN** (~300KB JS runtime) → PageSpeed Performance ~45-55/100
- **Lucide CDN** (~500KB celá knihovna, použito jen ~15 ikon)
- **13+ sekcí** — příliš dlouhá stránka, zákazník nedoscrolluje ani polovinu
- **Lorem ipsum na 15+ místech** — Reference, O metodě, Pro koho, FAQ odpovědi
- **Duplicitní sekce** — "O metodě" + "Metoda" + "7 pilířů" říkají totéž
- **Odhad bounce rate:** 70-80%

### Co řeší V2 design:
- **Inline CSS** místo Tailwind CDN → odhadovaný FCP < 1s
- **Inline SVG** místo Lucide CDN → úspora ~500KB
- **8 sekcí** místo 13 (odstraněny: O metodě, Pro koho, 7 pilířů, Reference)
- **Žádný Lorem ipsum** — všechny texty jsou reálné
- **Jednodušší konverzní cesta** — Hero → Problém → Řešení → Příběh → ROI → Produkty → Kontakt + FAQ

---

## Architektura projektu

### Backend API (nemění se)
| Endpoint | Metoda | Popis |
|---|---|---|
| `api/contact.php` | POST JSON `{name, email, message}` | Kontaktní formulář → CRM + e-mail |
| `api/booking.php` | POST JSON `{name, email, phone, date, time, message}` | Rezervace → CRM + Google Calendar + e-mail |
| `api/slots.php?month=YYYY-MM` | GET | Vrací `{slots, availability, slots_detail}` pro měsíční kalendář |

### Konfigurace
```
api/config.php          → hlavní config (includuje version.php + config.public.php + config.local.php)
api/version.php         → definuje APP_VERSION
api/config.public.php   → veřejné konstanty
api/config.local.php    → DB credentials, API klíče (ne v gitu)
```

---

## Přesný postup — krok za krokem

### KROK 1: Nahraď Tailwind CDN + Lucide → inline CSS + inline SVG

Odstraň z `<head>`:
```html
<!-- SMAZAT: -->
<script src="https://cdn.tailwindcss.com?v=..."></script>
<script src="https://unpkg.com/lucide@latest?v=..."></script>
<script>tailwind.config = { ... }</script>
```

Místo toho převezmi celý `<style>` blok z `homepage-v2.html` (řádky 21–1105), který obsahuje:
- CSS reset a custom properties (design system)
- Layout, typografie, navigace
- Všechny sekce (hero, trust bar, problém, metoda, story, ROI, produkty, kontakt, FAQ, footer)
- Responzivní media queries
- Animace (fade-in, scroll-reveal)

Zachovej stávající PHP hlavičku:
```php
<?php require_once __DIR__ . '/api/config.php'; $v = defined('APP_VERSION') ? APP_VERSION : '1.0.0'; ?>
```

Zachovej/aktualizuj meta tagy a Open Graph z homepage-v2.html.

Zachovej cache-busting na Google Fonts:
```html
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,900&display=swap&v=<?= htmlspecialchars($v) ?>" rel="stylesheet">
```

### KROK 2: Přepiš HTML strukturu sekcí podle V2 designu

Převezmi HTML strukturu z `homepage-v2.html`. Všechny Tailwind třídy (`class="bg-cream text-ink ..."`) nahraď odpovídajícími CSS třídami z V2 stylu.

Lucide ikony (`<i data-lucide="brain">`) nahraď inline SVG z `homepage-v2.html`.

### KROK 3: Oprav českou diakritiku

Texty převzaté z `homepage-v2.html` jsou psané česky **bez diakritiky**. Při přepisu do `index.php` rovnou použij správnou češtinu.

#### Kompletní seznam oprav:

**Navigace:**
- `Problem` → `Problém`
- `Pribeh` → `Příběh`
- `Nabidka` → `Nabídka`
- `Audit Zdarma` → `Audit zdarma`
- (mobile-menu obsahuje stejné texty — oprav i tam)

**Hero:**
- `Anatomie udrzitelneho vykonu` → `Anatomie udržitelného výkonu`
- `Vas lidsky` → `Váš lidský`
- `hardware hori.` → `hardware hoří.`
- `Zvyste vykon tymu i sebe ne tim, ze budete pracovat vic, ale tim, ze` → `Zvyšte výkon týmu i sebe ne tím, že budete pracovat víc, ale tím, že`
- `prestanete bojovat s vlastni biologii` → `přestanete bojovat s vlastní biologií`
- `ZASTAVIT VYHORENI` → `ZASTAVIT VYHOŘENÍ`
- `PRIBEH ZAKLADATELKY` → `PŘÍBĚH ZAKLADATELKY`

**Trust bar:**
- `Piliru metody` → `Pilířů metody`
- `Usetrenej cas denne` → `Ušetřený čas denně`
- `Vrstvy pristupu` → `Vrstvy přístupu`
- `4 tydny` → `4 týdny`

**Sekce Problém:**
- `Poznavate se?` → `Poznáváte se?`
- `Vas byznys software je genialni.` → `Váš byznys software je geniální.`
- `Snazite se ridit formuli 1 se zatazenou rucni brzdou. To neni stari. To je systemova chyba.` → `Snažíte se řídit formuli 1 se zataženou ruční brzdou. To není stáří. To je systémová chyba.`
- `Mozkova mlha po obede. Rozhodovaci paralyza. Neschopnost "vypnout" praci doma. Jedete na autopilot.` → `Mozková mlha po obědě. Rozhodovací paralýza. Neschopnost „vypnout" práci doma. Jedete na autopilot.`
- `Telo (Hardware)` → `Tělo (Hardware)`
- `Bolest krcni patere vystreljici do rukou. Melke dychani. Chronicka unava, kterou spanek neresi.` → `Bolest krční páteře vystřelující do rukou. Mělké dýchání. Chronická únava, kterou spánek neřeší.`
- `Tym (Vykon)` → `Tým (Výkon)`
- `Prezentismus: lide sedi u pocitacu, ale jejich kognitivni vykon je na 40 %. Vyhoreli lide netvofi hodnoty.` → `Prezentismus: lidé sedí u počítačů, ale jejich kognitivní výkon je na 40 %. Vyhořelí lidé netvoří hodnoty.`
- `To neni stari. To je systemova chyba.` → `To není stáří. To je systémová chyba.`
- `Metoda WALANCE opravuje vsechny tri vrstvy najednou — telo, mysl i navyky.` → `Metoda WALANCE opravuje všechny tři vrstvy najednou — tělo, mysl i návyky.`

**Sekce Metoda WALANCE:**
- `Neucim vas cvicit.` → `Neučím vás cvičit.`
- `Ucim vas pracovat.` → `Učím vás pracovat.`
- `Metoda WALANCE neni wellness benefit. Je to provozni manual k vasemu telu. Propojuje fyzioterapii, neurovedu a leadership.` → `Metoda WALANCE není wellness benefit. Je to provozní manuál k vašemu tělu. Propojuje fyzioterapii, neurovědu a leadership.`
- `Telo & Fyzio. Odstranime fyzicke treni. Konec bolesti zad znamena lepsi prokrveni mozku. Opravime sasi, aby motor mohl bezet naplno.` → `Tělo & Fyzio. Odstraníme fyzické tření. Konec bolestí zad znamená lepší prokrvení mozku. Opravíme šasi, aby motor mohl běžet naplno.`
- `Job Crafting. Designujeme praci tak, aby vas nabijela, ne vysavala. Sladime vase silne stranky s naplni prace.` → `Job Crafting. Designujeme práci tak, aby vás nabíjela, ne vysávala. Sladíme vaše silné stránky s náplní práce.`
- `Operacni system` → `Operační systém`
- `Navyky & Ritualy` → `Návyky & Rituály`
- `Automatizace zdravi, abyste na nej nemuseli myslet.` → `Automatizace zdraví, abyste na něj nemuseli myslet.`

**Sekce Příběh:**
- `Pripadova studie` → `Případová studie`
- `Teorie konci, kdyz si zranite koleno.` → `Teorie končí, když si zraníte koleno.`
- `Ja to ted ziju.` → `Já to teď žiju.`
- `Jmenuji se Jana. Jsem fyzioterapeutka a manazerka. Roky jsem ucila firmy, jak nevyhoret. A pak prisla lekce pokory.` → `Jmenuji se Jana. Jsem fyzioterapeutka a manažerka. Roky jsem učila firmy, jak nevyhořet. A pak přišla lekce pokory.`
- `V lednu 2026 me tezky uraz kolene upoutal na luzko. Mohla jsem zavrit firmu. Misto toho jsem se stala Klientem 0.` → `V lednu 2026 mě těžký úraz kolene upoutal na lůžko. Mohla jsem zavřít firmu. Místo toho jsem se stala Klientem 0.`
- `Aplikuji metodu WALANCE v extremnich podminkach. Ridim byznys horizontalne. A funguje to. Mam cistsi hlavu nez vy v kancelari.` → `Aplikuji metodu WALANCE v extrémních podmínkách. Řídím byznys horizontálně. A funguje to. Mám čistší hlavu než vy v kanceláři.`
- `Pokud dokazu udrzet vysoky vykon ja z postele, naucim to i vas.` → `Pokud dokážu udržet vysoký výkon já z postele, naučím to i vás.`
- `alt="Jana na luzku s ortezou a laptopem — autenticky zaber zakladatelky WALANCE"` → `alt="Jana na lůžku s ortézou a laptopem — autentický záběr zakladatelky WALANCE"`

**Sekce ROI:**
- `Matematika je neuprosna` → `Matematika je neúprosná`
- `Tolik casu denne staci usetrit (diky absenci bolesti zad), aby se investice vratila.` → `Tolik času denně stačí ušetřit (díky absenci bolestí zad), aby se investice vrátila.`
- `My cilime na` → `My cílíme na`
- `denne navic` → `denně navíc`
- `Vynasobte si to hodinovou sazbou vasich klicovych lidi.` → `Vynásobte si to hodinovou sazbou vašich klíčových lidí.`

**Sekce Produkty:**
- `Spoluprace` → `Spolupráce`
- `Jak muzeme spolupracovat?` → `Jak můžeme spolupracovat?`
- `Strategie sita na miru vasi aktualni situaci.` → `Strategie šitá na míru vaší aktuální situaci.`
- `Pro tymy, ktere jedou na dluh a potrebuji zastavit uniky energie.` → `Pro týmy, které jedou na dluh a potřebují zastavit úniky energie.`
- `Audit "energetickych der"` → `Audit „energetických děr"`
- `Fyzio-ergonomie pracoviste` → `Fyzio-ergonomie pracoviště`
- `4 tydny hybridniho mentoringu` → `4 týdny hybridního mentoringu`
- `Tvrda data, zadne ezo` → `Tvrdá data, žádné ezo`
- `Pro lidry (1-on-1)` → `Pro lídry (1-on-1)`
- `Strategicka konzultace s Janou (Klient 0). Krizove rizeni vas samotnych.` → `Strategická konzultace s Janou (Klient 0). Krizové řízení vás samotných.`
- `Hloubkova diagnostika` → `Hloubková diagnostika`
- `Redesign pracovniho kalendare` → `Redesign pracovního kalendáře`
- `Okamzita uleva od tlaku` → `Okamžitá úleva od tlaku`
- `System, jak fungovat horizontalne` → `Systém, jak fungovat horizontálně`
- `REZERVOVAT TERMIN` → `REZERVOVAT TERMÍN`
- `Kapacita omezena (Uraz)` → `Kapacita omezena (Úraz)`
- `Webinar: Jak ridit firmu a nezblaznit se, i kdyz telo stavkuje.` → `Webinář: Jak řídit firmu a nezbláznit se, i když tělo stávkuje.`
- `3 okamzite techniky` → `3 okamžité techniky`
- `E-book s manualem` → `E-book s manuálem`
- `Pristup do komunity` → `Přístup do komunity`
- `Zaznam masterclass` → `Záznam masterclass`
- `STAHNOUT ZDARMA` → `STÁHNOUT ZDARMA`

**Sekce Kontakt:**
- `Napiste nam` → `Napište nám`
- `Mate dotaz nebo chcete rezervovat termin? Ozvet se nam.` → `Máte dotaz nebo chcete rezervovat termín? Ozvěte se nám.`
- `Kontaktni formular` → `Kontaktní formulář`
- `Jmeno *` → `Jméno *`
- `Vase jmeno` → `Vaše jméno`
- `Zprava *` → `Zpráva *`
- `Jak vam muzeme pomoci?` → `Jak vám můžeme pomoci?`
- `ODESLAT ZPRAVU` → `ODESLAT ZPRÁVU`
- `Rezervace terminu` → `Rezervace termínu`
- `Vyberte si volny termin v kalendari. Rezervace je napojena na Google Calendar.` → `Vyberte si volný termín v kalendáři. Rezervace je napojena na Google Calendar.`
- `OTEVRIT KALENDAR` → `OTEVŘÍT KALENDÁŘ`
- `Nebo napiste primo na` → `Nebo napište přímo na`

**Sekce FAQ:**
- `Caste dotazy` → `Časté dotazy`
- `Jak dlouho trva typicka spoluprace?` → `Jak dlouho trvá typická spolupráce?`
- `4tydenni` → `4týdenní`
- `individualni a prizpusobuje se vasi situaci — od jednorázove konzultace po dlouhodoby mentoring` → `individuální a přizpůsobuje se vaší situaci — od jednorázové konzultace po dlouhodobý mentoring`
- `Potrebuji byt fyzicky v Praze?` → `Potřebuji být fyzicky v Praze?`
- `Spoluprace probiha hybridne — online konzultace, video hovory a fyzio cviceni na video. Osobni setkani je mozne, ale neni nutne.` → `Spolupráce probíhá hybridně — online konzultace, video hovory a fyzio cvičení na video. Osobní setkání je možné, ale není nutné.`
- `Jak se lisi WALANCE od klasickeho koucingu?` → `Jak se liší WALANCE od klasického koučingu?`
- `WALANCE propojuje fyzioterapii s leadershipem. Nepracujeme jen s myslenim, ale i s telem. Zacinate od hardware (telo) a postupujete k software (mysl) a operacnimu systemu (navyky). Zadny jiny kouc toto nepropojuje.` → `WALANCE propojuje fyzioterapii s leadershipem. Nepracujeme jen s myšlením, ale i s tělem. Začínáte od hardware (tělo) a postupujete k software (mysl) a operačnímu systému (návyky). Žádný jiný kouč toto nepropojuje.`
- `Jak zacit?` → `Jak začít?`
- `Napiste nam pres formular nebo si rovnou rezervujte termin v kalendari. Zacneme kratkym auditem vasi situace — zdarma a nezavazne.` → `Napište nám přes formulář nebo si rovnou rezervujte termín v kalendáři. Začneme krátkým auditem vaší situace — zdarma a nezávazně.`

**Patička:**
- `Byznys neni sprint. Je to maraton v pohybu.` → `Byznys není sprint. Je to maraton v pohybu.`
- `Vsechna prava vyhrazena.` → `Všechna práva vyhrazena.`

**JavaScript chybové hlášky:**
- `Chyba pri odesilani.` → `Chyba při odesílání.`
- `Chyba pripojeni. Zkuste to pozdeji nebo napiste na info@walance.cz` → `Chyba připojení. Zkuste to později nebo napište na info@walance.cz`

### KROK 4: Převeď booking modál z Tailwind na V2 inline CSS

Stávající booking modál v `index.php` (řádky 619–693 HTML, 836–1047 JS) **funguje správně**. Převeď jeho HTML z Tailwind tříd na inline CSS / V2 CSS třídy.

Přidej do `<style>` bloku CSS pro modál:

```css
/* ---- BOOKING MODAL ---- */
#cal-grid > div {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.15s ease;
}

.cal-day-clickable { cursor: pointer; }
.cal-day-clickable:hover { opacity: 0.85; transform: scale(1.05); }
.cal-day-selected { box-shadow: 0 0 0 2px var(--accent) !important; }

.time-slot-btn {
    padding: 10px 20px;
    border-radius: 12px;
    border: 1px solid var(--mist);
    background: var(--mist-light);
    font-size: 0.875rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.15s ease;
    color: var(--ink);
    font-family: var(--font-body);
}

.time-slot-btn:hover { border-color: var(--accent); color: var(--accent); }
.time-slot-selected { background: var(--accent) !important; color: var(--cream) !important; border-color: var(--accent) !important; }

.btn-primary:disabled { opacity: 0.5; cursor: not-allowed; }
```

JavaScript logiku bookingu zachovej ze stávajícího `index.php` — jen uprav CSS třídy na V2 (inline styly místo Tailwind).

### KROK 5: Napoj CTA tlačítka na modál

1. **"REZERVOVAT TERMÍN"** v produktu Crisis Mentoring:
```html
<button type="button" onclick="openBookingModal()" class="product-cta product-cta--primary">REZERVOVAT TERMÍN</button>
```

2. **"OTEVŘÍT KALENDÁŘ"** v kontaktní sekci:
```html
<button type="button" class="contact-booking-btn" onclick="openBookingModal()">OTEVŘÍT KALENDÁŘ</button>
```

### KROK 6: Zachovej verzi v patičce

```html
<span style="margin-left: 8px; opacity: 0.5;">v<?= htmlspecialchars($v) ?></span>
```

---

## Struktura sekcí — co odebrat, co zachovat

### Co se ODSTRAŇUJE z index.php:
| Sekce | Důvod odstranění |
|---|---|
| O metodě (id="about") | Duplicitní s Metoda WALANCE, texty = Lorem ipsum |
| Pro koho | Všechny 4 karty = Lorem ipsum |
| 7 pilířů (interaktivní) | Příliš detailní na landing page, patří na podstránku |
| Reference | Všechny 3 = Lorem ipsum — lepší nemít žádné než falešné |

### Co ZŮSTÁVÁ (8 sekcí V2):
| # | Sekce | Zdroj designu | Zdroj funkčnosti |
|---|---|---|---|
| 1 | Navigace | `homepage-v2.html` | Mobile menu toggle JS |
| 2 | Hero | `homepage-v2.html` | — |
| 3 | Trust bar | `homepage-v2.html` | — |
| 4 | Problém | `homepage-v2.html` | — |
| 5 | Metoda WALANCE | `homepage-v2.html` | — |
| 6 | Příběh | `homepage-v2.html` | — |
| 7 | ROI | `homepage-v2.html` | — |
| 8 | Produkty | `homepage-v2.html` | Booking modál onclick |
| 9 | Kontakt + FAQ | `homepage-v2.html` | Contact form JS + booking btn |
| 10 | Footer | `homepage-v2.html` | — |
| 11 | Booking modál | Stávající `index.php` (převést CSS) | Stávající `index.php` JS |

---

## Designová pravidla

### NEPOUŽÍVEJ:
- Tailwind CSS (`class="bg-accent text-cream"` apod.)
- CDN knihovny (Lucide, Font Awesome)
- Externě linkované CSS soubory
- Tailwind `@apply` direktivy
- Jakékoliv `<script src="...cdn...">` tagy

### POUŽÍVEJ:
- Inline `<style>` blok v `<head>` (převzít z `homepage-v2.html`)
- CSS custom properties (`var(--accent)`, `var(--cream)`, ...)
- Inline SVG ikony (převzít z `homepage-v2.html`)
- Nativní HTML elementy (`<details>` pro FAQ, `<form>`, ...)
- `font-family: var(--font-body)` a `font-family: var(--font-display)`

### CSS custom properties (design system):
```css
--cream: #FAF9F7;         /* pozadí stránky */
--ink: #1e293b;           /* primární text */
--ink-light: #475569;     /* sekundární text */
--ink-muted: #94a3b8;     /* tlumený text, labely */
--accent: #0d9488;        /* akční barva (teal) */
--accent-dark: #0a7a70;   /* hover stav akcí */
--accent-light: #14b8a6;  /* světlejší accent */
--sage: #5a7d5a;          /* zelená (tělo/fyzio) */
--mist: #e2e8f0;          /* bordery, oddělení */
--mist-light: #f1f5f9;    /* jemné pozadí */
--white: #ffffff;          /* bílé pozadí karet */
--font-body: 'DM Sans', sans-serif;
--font-display: 'Fraunces', Georgia, serif;
```

---

## Výstup

Upravený soubor: **`index.php`** v root adresáři projektu.

Soubor `homepage-v2.html` zachovej beze změny (jako referenci designu).

### Checklist před dokončením:
- [ ] Úprava proběhla přímo v `index.php` (ne nový soubor)
- [ ] PHP hlavička zachována: `require_once __DIR__ . '/api/config.php'`
- [ ] Verze v HTML komentáři a v patičce
- [ ] Cache-busting `?v=` na Google Fonts URL
- [ ] **Kompletní česká diakritika** — žádný text bez háčků/čárek (projdi celý soubor!)
- [ ] Tailwind CDN odstraněn, Lucide CDN odstraněn
- [ ] Inline CSS z homepage-v2.html + booking modál CSS
- [ ] Inline SVG místo Lucide ikon
- [ ] Rezervační modál s měsíčním kalendářem (fungující, převedený na V2 CSS)
- [ ] Kontaktní formulář → `api/contact.php` (POST JSON)
- [ ] Rezervační formulář → `api/booking.php` (POST JSON)
- [ ] Kalendářní data → `api/slots.php?month=YYYY-MM` (GET)
- [ ] "REZERVOVAT TERMÍN" → `openBookingModal()`
- [ ] "OTEVŘÍT KALENDÁŘ" → `openBookingModal()`
- [ ] Escape klávesa zavře modál
- [ ] Mobile responsive (modál, kalendář, formuláře)
- [ ] Žádný `<script src="...cdn...">` tag
- [ ] Fade-in animace (Intersection Observer) zachovány
- [ ] Nav shadow on scroll zachován
- [ ] Open Graph meta tagy přítomny
- [ ] Odstraněné sekce: O metodě, Pro koho, 7 pilířů, Reference

### Testování:
```bash
php -S localhost:8000
# Otevři http://localhost:8000/index.php
```

API vyžaduje MySQL databázi a `api/config.local.php`. Pokud chybí, modál zobrazí "Chyba načtení kalendáře." — to je OK.
