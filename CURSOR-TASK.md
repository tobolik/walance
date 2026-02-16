# ZADÁNÍ PRO CURSOR: Redesign index.php → homepage-v2.php

## Shrnutí

Přepiš stávající `index.php` (Tailwind CDN + Lucide CDN, 1050 řádků, 13+ sekcí) na nový `homepage-v2.php` v minimalistickém V2 designu (inline CSS, inline SVG, 8 sekcí).

**Vzorový soubor nového designu: `homepage-v2.html`** — obsahuje kompletní HTML + CSS + JS základ, ale chybí mu:
1. PHP integrace (config, verze)
2. Česká diakritika (většina textu je bez háčků/čárek)
3. Rezervační modál s kalendářem (existuje jen v `index.php`)
4. Správné napojení CTA tlačítek

**Zdrojový soubor funkční logiky: `index.php`** — obsahuje:
- Plně funkční rezervační modál s měsíčním kalendářem
- Kontaktní formulář napojený na API
- PHP config a verze
- JavaScript pro booking (slots, calendar, form submit)

**Cíl:** Zkombinuj design z `homepage-v2.html` s funkčností z `index.php` do jednoho souboru `homepage-v2.php`.

---

## Podkladové dokumenty (přečti si je!)

| Soubor | Co obsahuje |
|--------|-------------|
| `AUDIT-HOMEPAGE.md` | Kompletní technický + UX audit stávajícího webu — proč redesignujeme |
| `CONTENT-EXTRACTED.md` | Veškerý obsah extrahovaný z index.php — strukturovaně po sekcích |
| `CONTENT_CHECKLIST.md` | Přehled chybějícího obsahu (Lorem ipsum místa) |
| `homepage-v2.html` | **Nový design** — inline CSS, bez Tailwind, zjednodušená struktura |
| `index.php` | **Stávající web** — Tailwind + Lucide, fungující booking modál |

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

### KROK 1: Zkopíruj homepage-v2.html → homepage-v2.php

Doslova zkopíruj soubor. Pak začni upravovat.

### KROK 2: Přidej PHP hlavičku

Na úplný začátek souboru (před `<!DOCTYPE html>`):
```php
<?php require_once __DIR__ . '/api/config.php'; $v = defined('APP_VERSION') ? APP_VERSION : '1.0.0'; ?>
```

Za `<head>` tag přidej:
```html
<!-- VERSION: <?= htmlspecialchars($v) ?> -->
```

V Google Fonts URL přidej cache-busting:
```html
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,900&display=swap&v=<?= htmlspecialchars($v) ?>" rel="stylesheet">
```

### KROK 3: Oprav českou diakritiku

Celý soubor `homepage-v2.html` je psaný česky **bez diakritiky**. Existuje jedna výjimka na řádku ~1250 ("Mikrozměny, které běží..."). Vše sjednoť na správnou češtinu.

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

### KROK 4: Přidej rezervační modál s kalendářem

V `homepage-v2.html` chybí rezervační modál úplně. Musíš ho přidat. Zdrojem logiky je `index.php` (řádky 619–693 HTML, 836–1047 JS), ale **CSS musí být V2 styl** (inline CSS, žádný Tailwind).

#### 4a. HTML modálu — vlož před `</body>`:

```html
<!-- ============ BOOKING MODAL ============ -->
<div id="booking-modal" style="display:none; position:fixed; inset:0; z-index:200;">
    <!-- Backdrop -->
    <div onclick="closeBookingModal()" style="position:absolute; inset:0; background:rgba(30,41,59,0.6); backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px);"></div>
    <!-- Modal box -->
    <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:calc(100% - 32px); max-width:640px; max-height:90vh; overflow-y:auto; background:var(--cream); border-radius:24px; box-shadow:0 24px 64px rgba(0,0,0,0.2);">
        <!-- Header -->
        <div style="position:sticky; top:0; background:var(--cream); z-index:10; display:flex; justify-content:flex-end; padding:16px 20px 0;">
            <button onclick="closeBookingModal()" style="background:none; border:none; cursor:pointer; padding:8px; color:var(--ink);" aria-label="Zavřít">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
        <!-- Content -->
        <div style="padding:0 32px 32px;">
            <h2 class="font-display" style="font-size:1.75rem; font-weight:700; margin-bottom:8px;">Rezervace termínu</h2>
            <p style="color:var(--ink-light); font-size:0.875rem; margin-bottom:24px;">Zelená = volné termíny. Vyberte den, pak čas.</p>

            <!-- Calendar controls -->
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <button id="cal-prev" style="background:none; border:1px solid var(--mist); border-radius:12px; padding:8px 12px; cursor:pointer; color:var(--ink);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <span id="cal-month-title" class="font-display" style="font-weight:700; font-size:1.125rem;"></span>
                <button id="cal-next" style="background:none; border:1px solid var(--mist); border-radius:12px; padding:8px 12px; cursor:pointer; color:var(--ink);">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            </div>

            <!-- Day headers -->
            <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:4px; text-align:center; margin-bottom:4px;">
                <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">Po</span>
                <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">Út</span>
                <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">St</span>
                <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">Čt</span>
                <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">Pá</span>
                <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">So</span>
                <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">Ne</span>
            </div>

            <!-- Calendar grid -->
            <div id="cal-grid" style="display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:24px;"></div>

            <!-- Time slots -->
            <div id="cal-time-panel" style="display:none; margin-bottom:24px;">
                <p id="cal-time-label" style="font-weight:600; margin-bottom:12px;"></p>
                <div id="cal-time-slots" style="display:flex; flex-wrap:wrap; gap:8px;"></div>
            </div>

            <!-- Booking form -->
            <form id="booking-form">
                <input type="hidden" id="booking-date" name="date">
                <input type="hidden" id="booking-time" name="time">

                <div id="booking-fields" style="display:none;">
                    <div class="form-group">
                        <label for="b-name">Jméno *</label>
                        <input type="text" id="b-name" name="name" required placeholder="Vaše jméno">
                    </div>
                    <div class="form-group">
                        <label for="b-email">E-mail *</label>
                        <input type="email" id="b-email" name="email" required placeholder="vas@email.cz">
                    </div>
                    <div class="form-group">
                        <label for="b-phone">Telefon</label>
                        <input type="tel" id="b-phone" name="phone" placeholder="+420...">
                    </div>
                    <div class="form-group">
                        <label for="b-message">Poznámka</label>
                        <textarea id="b-message" name="message" rows="3" placeholder="Stručně popište vaši situaci..."></textarea>
                    </div>
                </div>

                <div id="booking-message" style="display:none; padding:12px 16px; border-radius:12px; font-size:0.875rem; margin-bottom:16px;"></div>

                <button type="submit" id="booking-submit" disabled class="btn-primary" style="width:100%; display:none;">
                    REZERVOVAT
                </button>
            </form>
        </div>
    </div>
</div>
```

#### 4b. CSS pro modál — přidej do `<style>` bloku:

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

#### 4c. JavaScript pro modál — přidej do `<script>` bloku:

Převezmi logiku z `index.php` (řádky 836–1047), ale adaptuj pro V2:

```javascript
// ---- BOOKING CALENDAR ----
const monthNames = ['Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec'];
const dayNames = ['neděle','pondělí','úterý','středa','čtvrtek','pátek','sobota'];
const dayNamesGen = ['ledna','února','března','dubna','května','června','července','srpna','září','října','listopadu','prosince'];

let calCurrentMonth = '';
let calData = { slots: {}, availability: {} };
let calSelectedDate = null;

function openBookingModal() {
    const modal = document.getElementById('booking-modal');
    modal.style.display = 'block';
    document.body.style.overflow = 'hidden';
    calSelectedDate = null;
    document.getElementById('cal-time-panel').style.display = 'none';
    document.getElementById('booking-fields').style.display = 'none';
    document.getElementById('booking-submit').style.display = 'none';
    document.getElementById('booking-message').style.display = 'none';
    const now = new Date();
    calCurrentMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
    loadCalendarMonth();
}

function closeBookingModal() {
    document.getElementById('booking-modal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeBookingModal();
});

async function loadCalendarMonth() {
    const grid = document.getElementById('cal-grid');
    grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:24px; color:var(--ink-muted);">Načítám...</div>';
    try {
        const r = await fetch('api/slots.php?month=' + calCurrentMonth, { cache: 'no-store' });
        calData = await r.json();
        renderCalendar();
    } catch (err) {
        grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:24px; color:#991b1b;">Chyba načtení kalendáře.</div>';
    }
}

function renderCalendar() {
    const [year, month] = calCurrentMonth.split('-').map(Number);
    document.getElementById('cal-month-title').textContent = monthNames[month - 1] + ' ' + year;
    const grid = document.getElementById('cal-grid');
    grid.innerHTML = '';

    const firstDay = new Date(year, month - 1, 1);
    const offset = (firstDay.getDay() + 6) % 7; // Monday = 0
    const daysInMonth = new Date(year, month, 0).getDate();
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // Empty cells for offset
    for (let i = 0; i < offset; i++) {
        const empty = document.createElement('div');
        grid.appendChild(empty);
    }

    for (let d = 1; d <= daysInMonth; d++) {
        const dateStr = year + '-' + String(month).padStart(2, '0') + '-' + String(d).padStart(2, '0');
        const dateObj = new Date(year, month - 1, d);
        const dayOfWeek = dateObj.getDay();
        const isPast = dateObj < today;
        const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;

        const cell = document.createElement('div');
        cell.textContent = d;

        const slots = calData.slots?.[dateStr];
        const avail = calData.availability?.[dateStr];

        if (isPast || isWeekend || !slots || slots.length === 0) {
            cell.style.background = 'rgba(226,232,240,0.2)';
            cell.style.color = 'var(--ink-muted)';
            if (isPast) cell.style.opacity = '0.5';
        } else {
            const pct = avail ? avail.percent : 100;
            const hasReservations = avail && (avail.pending > 0 || avail.confirmed > 0);

            if (!hasReservations) {
                if (pct >= 75) { cell.style.background = '#059669'; cell.style.color = '#fff'; }
                else if (pct >= 50) { cell.style.background = '#10b981'; cell.style.color = '#fff'; }
                else if (pct >= 25) { cell.style.background = '#6ee7b7'; cell.style.color = 'var(--ink)'; }
                else { cell.style.background = '#d1fae5'; cell.style.color = 'var(--ink)'; }
            } else {
                if (pct >= 75) { cell.style.background = '#34d399'; cell.style.color = '#fff'; }
                else if (pct >= 50) { cell.style.background = '#6ee7b7'; cell.style.color = 'var(--ink)'; }
                else if (pct >= 25) { cell.style.background = '#a7f3d0'; cell.style.color = 'var(--ink)'; }
                else { cell.style.background = '#d1fae5'; cell.style.color = 'var(--ink)'; }
            }

            cell.classList.add('cal-day-clickable');
            cell.addEventListener('click', () => selectDate(dateStr));
        }

        if (calSelectedDate === dateStr) cell.classList.add('cal-day-selected');
        grid.appendChild(cell);
    }
}

function selectDate(dateStr) {
    calSelectedDate = dateStr;
    renderCalendar(); // Re-render to update selection

    const slots = calData.slots[dateStr] || [];
    const timeSlotsEl = document.getElementById('cal-time-slots');
    timeSlotsEl.innerHTML = '';

    const dateObj = new Date(dateStr + 'T00:00:00');
    const dayName = dayNames[dateObj.getDay()];
    const day = dateObj.getDate();
    const monthGen = dayNamesGen[dateObj.getMonth()];
    document.getElementById('cal-time-label').textContent = 'Vyberte čas — ' + dayName + ' ' + day + '. ' + monthGen;

    slots.forEach(time => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'time-slot-btn';
        btn.textContent = time;
        btn.addEventListener('click', () => {
            document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('time-slot-selected'));
            btn.classList.add('time-slot-selected');
            document.getElementById('booking-date').value = dateStr;
            document.getElementById('booking-time').value = time;
            document.getElementById('booking-fields').style.display = 'block';
            document.getElementById('booking-submit').style.display = 'block';
            document.getElementById('booking-submit').disabled = false;
        });
        timeSlotsEl.appendChild(btn);
    });

    document.getElementById('cal-time-panel').style.display = 'block';
    document.getElementById('booking-fields').style.display = 'none';
    document.getElementById('booking-submit').style.display = 'none';
}

// Calendar navigation
document.getElementById('cal-prev').addEventListener('click', () => {
    const [y, m] = calCurrentMonth.split('-').map(Number);
    const d = new Date(y, m - 2, 1);
    calCurrentMonth = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
    loadCalendarMonth();
});

document.getElementById('cal-next').addEventListener('click', () => {
    const [y, m] = calCurrentMonth.split('-').map(Number);
    const d = new Date(y, m, 1);
    calCurrentMonth = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
    loadCalendarMonth();
});

// Booking form submit
document.getElementById('booking-form').addEventListener('submit', async (e) => {
    e.preventDefault();
    const btn = document.getElementById('booking-submit');
    const msg = document.getElementById('booking-message');
    btn.disabled = true;
    msg.style.display = 'none';

    const fd = new FormData(e.target);
    const data = Object.fromEntries(fd);

    try {
        const r = await fetch('api/booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const res = await r.json();
        msg.style.display = 'block';
        if (res.success) {
            msg.style.background = '#dcfce7';
            msg.style.color = '#166534';
            msg.textContent = res.message || 'Rezervace odeslána!';
            e.target.reset();
            document.getElementById('booking-fields').style.display = 'none';
            document.getElementById('booking-submit').style.display = 'none';
            document.getElementById('cal-time-panel').style.display = 'none';
            calSelectedDate = null;
            loadCalendarMonth();
            setTimeout(closeBookingModal, 2000);
        } else {
            msg.style.background = '#fef2f2';
            msg.style.color = '#991b1b';
            msg.textContent = res.error || 'Chyba při odesílání.';
            btn.disabled = false;
        }
    } catch (err) {
        msg.style.display = 'block';
        msg.style.background = '#fef2f2';
        msg.style.color = '#991b1b';
        msg.textContent = 'Chyba připojení. Zkuste to později nebo napište na info@walance.cz';
        btn.disabled = false;
    }
});
```

### KROK 5: Napoj CTA tlačítka na modál

1. **"REZERVOVAT TERMÍN"** v produktu Crisis Mentoring — změň:
```html
<!-- Z: -->
<a href="#contact" class="product-cta product-cta--primary">REZERVOVAT TERMÍN</a>
<!-- Na: -->
<button type="button" onclick="openBookingModal()" class="product-cta product-cta--primary">REZERVOVAT TERMÍN</button>
```

2. **"OTEVŘÍT KALENDÁŘ"** v kontaktní sekci — změň:
```html
<!-- Z: -->
<button type="button" class="contact-booking-btn" onclick="if(typeof openBookingModal==='function')openBookingModal();else alert('...');">
<!-- Na: -->
<button type="button" class="contact-booking-btn" onclick="openBookingModal()">
```

3. **Odstraň alert() fallback** — s modálem v souboru je `openBookingModal` vždy definována.

### KROK 6: Přidej verzi do patičky

V `<footer>` přidej za copyright:
```html
<div class="footer-copy">
    &copy; 2026 WALANCE. Všechna práva vyhrazena.
    <span style="margin-left: 8px; opacity: 0.5;">v<?= htmlspecialchars($v) ?></span>
</div>
```

---

## Struktura sekcí — V1 (index.php) vs V2 (homepage-v2.php)

### Co se ODSTRAŇUJE z V1 (nepoužívej):
| Sekce | Důvod odstranění |
|---|---|
| O metodě (id="about") | Duplicitní s Metoda WALANCE, texty = Lorem ipsum |
| Pro koho | Všechny 4 karty = Lorem ipsum |
| 7 pilířů (interaktivní) | Příliš detailní na landing page, patří na podstránku |
| Reference | Všechny 3 = Lorem ipsum — lepší nemít žádné než falešné |

### Co ZŮSTÁVÁ (8 sekcí V2):
| # | Sekce | Zdroj designu | Zdroj funkčnosti |
|---|---|---|---|
| 1 | Navigace | `homepage-v2.html` řádky 1107–1138 | Mobile menu toggle JS |
| 2 | Hero | `homepage-v2.html` řádky 1141–1157 | — |
| 3 | Trust bar | `homepage-v2.html` řádky 1159–1181 | — |
| 4 | Problém | `homepage-v2.html` řádky 1183–1223 | — |
| 5 | Metoda WALANCE | `homepage-v2.html` řádky 1225–1254 | — |
| 6 | Příběh | `homepage-v2.html` řádky 1256–1279 | — |
| 7 | ROI | `homepage-v2.html` řádky 1281–1293 | — |
| 8 | Produkty | `homepage-v2.html` řádky 1295–1398 | Booking modál onclick |
| 9 | Kontakt + FAQ | `homepage-v2.html` řádky 1400–1489 | Contact form + booking btn |
| 10 | Footer | `homepage-v2.html` řádky 1491–1508 | — |
| 11 | Booking modál | **NOVÝ** (viz Krok 4) | `index.php` řádky 619–693 + 836–1047 |

---

## Designová pravidla

### NEPOUŽÍVEJ:
- Tailwind CSS (`class="bg-accent text-cream"` apod.)
- CDN knihovny (Lucide, Font Awesome)
- Externě linkované CSS soubory
- Tailwind `@apply` direktivy
- Jakékoliv `<script src="...cdn...">` tagy

### POUŽÍVEJ:
- Inline `<style>` blok v `<head>` (rozšiř stávající)
- CSS custom properties (`var(--accent)`, `var(--cream)`, ...)
- Inline SVG ikony (už jsou v `homepage-v2.html` — brain, activity, users, check, chevron, calendar, linkedin, mail, arrow-right, hamburger, close)
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

Jeden soubor: **`homepage-v2.php`** v root adresáři projektu.

Soubor `homepage-v2.html` zachovej beze změny (jako referenci).

### Checklist před dokončením:
- [ ] Soubor je `homepage-v2.php` (ne .html)
- [ ] PHP hlavička s `require_once __DIR__ . '/api/config.php'`
- [ ] Verze v HTML komentáři a v patičce
- [ ] Cache-busting `?v=` na Google Fonts URL
- [ ] **Kompletní česká diakritika** — žádný text bez háčků/čárek (projdi celý soubor!)
- [ ] Rezervační modál s měsíčním kalendářem (inline CSS, ne Tailwind)
- [ ] Modál: navigace měsíců, výběr dne, výběr času, formulář, submit
- [ ] Kontaktní formulář → `api/contact.php` (POST JSON)
- [ ] Rezervační formulář → `api/booking.php` (POST JSON)
- [ ] Kalendářní data → `api/slots.php?month=YYYY-MM` (GET)
- [ ] "REZERVOVAT TERMÍN" → `openBookingModal()`
- [ ] "OTEVŘÍT KALENDÁŘ" → `openBookingModal()`
- [ ] Escape klávesa zavře modál
- [ ] Mobile responsive (modál, kalendář, formuláře)
- [ ] Žádný Tailwind, žádný Lucide CDN, žádný `<script src="...cdn...">`
- [ ] Všechny SVG ikony inline
- [ ] Fade-in animace (Intersection Observer) zachovány
- [ ] Nav shadow on scroll zachován
- [ ] Open Graph meta tagy přítomny

### Testování:
```bash
php -S localhost:8000
# Otevři http://localhost:8000/homepage-v2.php
```

API vyžaduje MySQL databázi a `api/config.local.php`. Pokud chybí, modál zobrazí "Chyba načtení kalendáře." — to je OK.
