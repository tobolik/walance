# AUDIT HOMEPAGE — WALANCE.CZ
**Datum:** 16.02.2026
**Autor:** Claude (AI audit pro webinář)

---

## 1. TECHNICKÝ AUDIT (Performance)

### Kritické problémy

| Problém | Dopad | Řešení |
|---------|-------|--------|
| **Tailwind CSS přes CDN** | ~300KB+ JS runtime, blokuje render, FOUC | Build process s purge (výsledek ~10KB) |
| **Lucide Icons přes CDN** | ~500KB+ celá knihovna, použito ~15 ikon | SVG inline nebo icon font s jen potřebnými |
| **Google Fonts — 2 rodiny, 8+ řezů** | 3+ HTTP requesty, ~200KB | Self-host, subset, preload |
| **Žádný preconnect/preload** | Pomalé DNS+TLS pro CDN domény | `<link rel="preconnect">` pro fonts, CDN |
| **Obrázky pouze JPG** | hero-story.jpg 128KB, cat-healer 371KB | WebP/AVIF + srcset + responsive sizes |
| **Veškerý JS inline v HTML** | ~350 řádků JS, žádná minifikace | Externalizovat, minifikovat |
| **Scroll reveal = opacity:0** | CLS (Cumulative Layout Shift), obsah neviditelný bez JS | CSS-only animace nebo noscript fallback |

### Odhad PageSpeed skóre (na základě kódu)
- **Performance:** ~45-55/100 (CDN Tailwind je killer)
- **Accessibility:** ~70-80/100 (chybí aria labels, skip nav, focus styles)
- **Best Practices:** ~75-85/100 (CDN deps, no CSP headers)
- **SEO:** ~80-90/100 (meta OK, ale chybí structured data, Open Graph)

### Odhadované metriky
- **FCP (First Contentful Paint):** ~2.5-3.5s (čeká na Tailwind CDN parse)
- **LCP (Largest Contentful Paint):** ~4-6s (hero blob + font load)
- **CLS:** ~0.15-0.3 (opacity:0 → 1 přechody, font swap)
- **TBT (Total Blocking Time):** ~500-800ms (Tailwind JIT v prohlížeči)

---

## 2. UX/DESIGN AUDIT — POHLED ZÁKAZNÍKA

### Co zákazník vidí v prvních 3 sekundách

**Dobře:**
- Silný headline "Ale váš lidský hardware hoří." — okamžitě upoutá
- Jasný CTA "ZASTAVIT VYHOŘENÍ"
- Čistá barevná paleta (cream/teal/ink)
- Profesionální typografie (Fraunces + DM Sans)

**Špatně:**
- Velké dekorativní "W" v pozadí — nevysvětlené, ruší
- Blob tvary — generický "startup" look, neodpovídá serióznosti fyzioterapie
- Subtitulka "Váš byznys software je geniální" — matoucí metafora na úvod
- Druhý CTA "PŘÍBĚH KLIENTA 0" — klient neví, co je "Klient 0"

### Průchod stránkou (customer journey)

#### Navigace
- 6 položek + CTA "Audit Zdarma" → OK ale "Audit Zdarma" vede na #products, ne na formulář
- Chybí jasné "Kdo jsme" / "O nás"

#### Sekce "O metodě" (2. sekce)
- **PROBLÉM:** Lorem ipsum texty — zákazník okamžitě ztrácí důvěru
- Benefity 1, 2, 3 jsou placeholder — fatální pro konverzi
- Zákazník se ptá "Co je WALANCE?" a dostane neúplnou odpověď

#### Sekce "Poznáváte se?" (3. sekce)
- **DOBŘE:** Tři pain pointy (Hlava, Tělo, Tým) jsou silné a relevantní
- **DOBŘE:** "VERDIKT: To není stáří. To je systémová chyba." — výborná copy
- **ŠPATNĚ:** Rozšiřující text je opět Lorem ipsum

#### Sekce "Pro koho" (4. sekce)
- **PROBLÉM:** Všechny 4 karty mají Lorem ipsum popisy
- Zákazník vidí 4 prázdné boxy → odchází

#### Sekce "Příběh" (5. sekce)
- **DOBŘE:** Autentický příběh Jany — velmi silný emotivní moment
- **DOBŘE:** Fotka je reálná, ne stock
- **ŠPATNĚ:** Credentials (Vzdělání, Certifikace, Zkušenosti) = "DOPLNIT"
- **ŠPATNĚ:** Fotka kočky — rozmělňuje profesionalitu

#### Sekce "Metoda" (6. sekce)
- **DOBŘE:** Hardware/Software/OS metafora je srozumitelná
- **ŠPATNĚ:** Rozšiřující text je Lorem ipsum
- **ŠPATNĚ:** "Synergie" box = placeholder

#### Sekce "7 pilířů" (7. sekce)
- **PROBLÉM PRO ZÁKAZNÍKA:** Příliš detailní na landing page
- Interaktivní element je hezký, ale zákazník v decision-making fázi nepotřebuje 7 pilířů
- Patří na podstránku "Metoda" — ne na homepage

#### Sekce "Reference" (8. sekce)
- **FATÁLNÍ:** Všechny 3 reference = Lorem ipsum s "Jméno Příjmení"
- Loga firem = "DOPLNIT: Logo firmy 1/2/3"
- **Toto kompletně zničí důvěryhodnost** — lepší sekci úplně smazat

#### Sekce "Produkty" (9. sekce)
- **DOBŘE:** 3 jasné nabídky s CTA
- **ŠPATNĚ:** "STÁHNOUT ZDARMA" vede na #products (na sebe sama)
- **ŠPATNĚ:** Chybí ceny nebo alespoň "od X Kč"

#### Sekce "8 MINUT" (10. sekce)
- **DOBŘE:** Vizuálně silná
- **ŠPATNĚ:** Desátá sekce — zákazník sem nedoscrolluje

#### Kontakt + FAQ (11.-12. sekce)
- FAQ odpovědi = Lorem ipsum
- Kontaktní formulář OK, ale je příliš daleko od hero

### Celkový verdikt z pohledu zákazníka

> **"Přišel jsem na web, který mě zaujal nadpisem. Pak jsem scrolloval a viděl Lorem ipsum. Zavřel jsem to."**

**Bounce rate odhad:** 70-80% (kvůli Lorem ipsum a délce stránky)

---

## 3. STRUKTURÁLNÍ PROBLÉMY

1. **Stránka je příliš dlouhá** — 13+ sekcí, zákazník nedobrowsuje ani polovinu
2. **Lorem ipsum na 15+ místech** — fatální pro důvěryhodnost
3. **Neexistuje jasná konverzní cesta** — CTA vedou na špatná místa
4. **Chybí social proof** — žádné reálné reference, čísla, loga
5. **7 pilířů na homepage** — informační přetížení
6. **Duplicitní informace** — "O metodě" + "Metoda" + "7 pilířů" říkají totéž

---

## 4. DOPORUČENÍ PRO NOVOU HOMEPAGE

### Princip: "Less is more" — 5 sekcí místo 13

1. **Hero** — Headline + 1 CTA + krátký popis (3 věty max)
2. **Problém → Řešení** — 3 pain pointy → 3 řešení (Hardware/Software/OS)
3. **Příběh + Důvěra** — Jana kompaktně + credentials (až budou)
4. **Nabídka** — 3 produkty s jasnými CTA
5. **Kontakt** — Formulář + rezervace + FAQ (3 hlavní otázky)

### Design principy
- **Minimalistický** — hodně bílého prostoru
- **Typografie-first** — méně dekorací, víc textu
- **Mobile-first** — 70%+ návštěvníků je z mobilu
- **Bez Lorem ipsum** — raději kratší reálný text než dlouhý placeholder
- **Performance-first** — žádné CDN, self-hosted, optimalizované obrázky

---

## 5. TECHNICKÁ DOPORUČENÍ PRO NOVÝ BUILD

| Oblast | Současný stav | Doporučení |
|--------|---------------|------------|
| CSS | Tailwind CDN (runtime) | Tailwind CLI build (purged) nebo inline critical CSS |
| Ikony | Lucide CDN (~500KB) | Inline SVG (jen potřebné) |
| Fonty | Google Fonts CDN | Self-hosted, woff2, preload, display:swap |
| Obrázky | JPG only | WebP + fallback, srcset, lazy loading |
| JS | Inline 350 řádků | Minimální vanilla JS, defer |
| HTML | 1050 řádků monolitní | Sémantické HTML5, strukturované |
| SEO | Základní meta | Open Graph, JSON-LD, sitemap |
