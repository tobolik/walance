# Návrh: Kalendář – zabrané časy, admin sloty, Google Calendar

## 1. Problém

- **Zabrané časy nejsou v mřížce vidět** – při výběru dne se zobrazí jen volné sloty; uživatel nevidí, které časy jsou obsazené (pending/confirmed)
- **Admin nemůže nastavit dostupnost** – pracovní doba je pevně v config (9–17, 30 min)
- **Google Calendar** – zatím jen zápis nových rezervací; nečte se z něj obsazenost

---

## 2. Návrh řešení

### 2.1 Zabrané časy v mřížce (rychlá úprava)

**API slots.php** – rozšířit odpověď o stav každého slotu:
```json
"slots_detail": {
  "2026-02-26": {
    "09:00": "free",
    "09:30": "pending",
    "10:00": "confirmed",
    "10:30": "free",
    ...
  }
}
```

**Frontend** – zobrazit všechny sloty dne:
- **Volné** – zelený/šedý, klikatelné
- **Čeká na potvrzení** – amber, neklikatelné, tooltip „Rezervováno (čeká)“
- **Potvrzeno** – teal, neklikatelné, tooltip „Obsazeno“

---

### 2.2 Admin nastavení slotů

**Tabulka `availability_settings`** (nebo config v DB):
- Pracovní dny (Po–Pá nebo vlastní výběr)
- Časový rozsah: od–do (např. 9:00–17:00)
- Interval slotů (15, 30, 60 min)
- Výjimky: volné dny, dovolená (datum od–do)

**Admin stránka** `admin/settings.php` nebo `admin/availability.php`:
- Formulář: pracovní dny, od–do, interval
- Volitelně: kalendář výjimek (blokovat konkrétní dny)

**Alternativa:** Uchovávat v `api/config.php` – jednodušší, bez DB. Pro pokročilé výjimky by byla potřeba tabulka.

---

### 2.3 Google Calendar – obousměrná synchronizace

**A) Jen čtení obsazenosti z Google Calendar**

- Při načtení měsíce/dne: volat Google Calendar API, načíst události v daném rozsahu
- Události z GC = obsazené sloty (blokovat je v mřížce)
- Kombinace: DB rezervace + GC události = celková obsazenost

**B) Zápis + čtení (aktuální stav rozšířený)**

- Zápis: už je (createEvent při rezervaci)
- Čtení: přidat metodu `getBusySlots($date)` – vrací pole obsazených časů
- slots.php: sloučit `bookedByDate` (z DB) + `gcBusyByDate` (z Google)

**Implementace:**
- `GoogleCalendar::getEventsForDate($date)` – vrací události pro daný den
- Mapování na sloty: událost 10:00–11:00 → blokovat 10:00, 10:30 (podle intervalu)
- Cache (volitelně): ukládat GC obsazenost na X minut, aby se nevolalo API při každém načtení

---

## 3. Doporučené pořadí

| Fáze | Úkol | Složitost |
|------|------|-----------|
| 1 | Zabrané časy v mřížce (slots_detail + frontend) | Nízká |
| 2 | Admin nastavení slotů (config nebo DB) | Střední |
| 3 | Google Calendar – čtení obsazenosti | Střední |

---

## 4. Technické detaily

### 4.1 slots.php – rozšíření

Pro měsíční režim doplnit do odpovědi:
```php
$slotsDetail[$dateStr] = [];
foreach ($allDaySlots as $t) {
    if (in_array($t, $confirmedByDate[$dateStr] ?? [])) {
        $slotsDetail[$dateStr][$t] = 'confirmed';
    } elseif (in_array($t, $pendingByDate[$dateStr] ?? [])) {
        $slotsDetail[$dateStr][$t] = 'pending';
    } else {
        $slotsDetail[$dateStr][$t] = 'free';
    }
}
```

### 4.2 Google Calendar API – getEvents

```php
public function getEventsForDate(string $date): array {
    $start = $date . 'T00:00:00';
    $end = $date . 'T23:59:59';
    $events = $this->service->events->listEvents($this->calendarId, [
        'timeMin' => $start,
        'timeMax' => $end,
        'singleEvents' => true,
    ]);
    // Vrátit pole [ '10:00', '10:30', ... ] - obsazené časy
}
```

### 4.3 Admin availability

- Možnost A: Tabulka `availability_settings` (id, key, value) – flexibilní
- Možnost B: Soubor `api/availability.json` – jednoduché, bez migrace
- Možnost C: Rozšíření config.php – nejjednodušší, bez UI

---

## 5. Shrnutí pro schválení

1. **Zabrané časy** – zobrazit všechny sloty, volné = klikatelné, pending/confirmed = vyznačené a neklikatelné
2. **Admin sloty** – nová stránka pro nastavení pracovní doby a intervalu (volitelně výjimky)
3. **Google Calendar** – přidat čtení událostí a blokovat tyto časy v rezervačním kalendáři

Po odsouhlasení lze přistoupit k implementaci.
