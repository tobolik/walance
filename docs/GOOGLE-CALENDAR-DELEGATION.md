# Domain-Wide Delegation – zápis do kalendáře kolegy

Pokud chcete zapisovat rezervace do kalendáře jiného uživatele (např. kolegyně) a posílat klientům pozvánky na událost, potřebujete **Domain-Wide Delegation**. Bez toho Google neumožní Service Accountu oprávnění „Změnit události“ při sdílení kalendáře.

## Požadavky

- **Google Workspace** (ne osobní Gmail) – doména např. `sensio.cz`
- Přístup do **Google Cloud Console** a **Google Workspace Admin**

## Shrnutí – co je potřeba udělat

1. **Google Cloud Console** – u Service Accountu povolit **„Enable Google Workspace Domain-wide Delegation“**
2. **Google Workspace Admin** – přidat **Client ID** Service Accountu a scope `https://www.googleapis.com/auth/calendar`

---

## Postup nastavení

### 1. Google Cloud Console – povolit Domain-Wide Delegation u Service Accountu

#### Krok 1a: Otevřít Service Accounts

1. Otevřete [Google Cloud Console](https://console.cloud.google.com/)
2. V horním řádku vyberte **váš projekt** (např. kalendar-pro-walance)
3. Otevřete levé menu (☰) → **IAM a správa** (IAM & Admin) → **Service Accounts** (Účty služeb)
   - Přímý odkaz: [console.cloud.google.com/iam-admin/serviceaccounts](https://console.cloud.google.com/iam-admin/serviceaccounts)
4. V seznamu najděte váš Service Account (např. `walance-calendar@kalendar-pro-walance.iam.gserviceaccount.com`) a **klikněte na něj** (na e-mail, ne na šipku vpravo)

#### Krok 1b: Zkopírovat Client ID

5. Na stránce Service Accountu v záložce **Details** (Podrobnosti) najděte řádek **Client ID**
   - Je to dlouhé číslo (např. `123456789012345678901`) – **zkopírujte ho** (budete ho potřebovat v kroku 2)
   - Pokud Client ID nevidíte, rozbalte **Advanced settings** (Rozšířené nastavení)

#### Krok 1c: Povolit Domain-wide Delegation

6. Klikněte na tlačítko **Upravit** (Edit) v horní části stránky
7. Sjeďte dolů k sekci **Google Workspace Domain-wide Delegation**
8. Zaškrtněte **Enable Google Workspace Domain-wide Delegation** (Povolit delegaci v rámci domény Google Workspace)
9. Klikněte **Uložit** (Save)

### 2. Google Workspace Admin – autorizovat Service Account

**Poznámka:** Potřebujete účet **super administrátora** Workspace. Změny mohou trvat až 24 hodin, obvykle ale proběhnou rychleji.

#### Krok 2a: Otevřít správu Domain Wide Delegation

1. Přihlaste se na [admin.google.com](https://admin.google.com/) jako super administrátor
2. V levém menu klikněte na **Zabezpečení** (Security)
   - Pokud menu nevidíte, klikněte na „hamburger“ ikonu (☰) vlevo nahoře
3. V podmenu rozbalte **Přístup a správa dat** (Access and data control)
4. Klikněte na **API Controls** (řízení API)
5. Na stránce API Controls najděte sekci **Domain-wide delegation** (delegace v rámci domény)
6. Klikněte na **Spravovat Domain Wide Delegation** (Manage Domain Wide Delegation)
   - Anglické rozhraní: *Manage Domain Wide Delegation*
   - Přímý odkaz: [admin.google.com/ac/owl/domainwidedelegation](https://admin.google.com/ac/owl/domainwidedelegation)

#### Krok 2b: Přidat Client ID a scope

7. Klikněte na tlačítko **Přidat nový** (Add new)
8. Otevře se dialog. Vyplňte:
   - **Client ID:** Vložte číselné Client ID z kroku 1 (např. `123456789012345678901`)
   - **OAuth Scopes:** Zadejte přesně (jeden scope na řádek, nebo oddělené čárkou):
     ```
     https://www.googleapis.com/auth/calendar
     ```
9. Klikněte **Autorizovat** (Authorize)
10. Pokud se zobrazí chyba, zkontrolujte, že Client ID je správně zkopírované (jen čísla, bez mezer)

#### Krok 2c: Ověření

11. V seznamu by se měl objevit nový záznam s vaším Client ID
12. Klikněte na něj → **Zobrazit podrobnosti** (View details) a ověřte, že scope `https://www.googleapis.com/auth/calendar` je v seznamu
13. Pokud scope chybí: **Upravit** (Edit) → doplňte scope → **Autorizovat**

### 3. WALANCE – povolit impersonation

#### Kde je soubor

Soubor `api/config.local.php` leží v kořeni projektu WALANCE, ve složce `api/`:

```
walance/
├── api/
│   ├── config.local.php    ← tento soubor upravíte
│   ├── config.local.example.php
│   └── ...
```

#### Co přidat

1. Otevřete `api/config.local.php` v editoru
2. Na konec souboru (před uzavírací `?>` pokud tam je, nebo prostě na konec) přidejte nový řádek:

```php
define('GOOGLE_CALENDAR_IMPERSONATION', true);
```

3. Uložte soubor

#### Příklad celého config.local.php

```php
<?php
define('CONTACT_EMAIL', 'info@walance.cz');
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'walancecz');
define('DB_USER', 'walancecz001');
define('DB_PASS', 'vaše-heslo');
define('MIGRATE_TOKEN', 'váš-token');

// Zápis do kalendáře kolegy (Domain-Wide Delegation)
define('GOOGLE_CALENDAR_IMPERSONATION', true);
```

#### Pokud soubor neexistuje

Zkopírujte `api/config.local.example.php` jako `api/config.local.php`, vyplňte údaje a přidejte řádek s `GOOGLE_CALENDAR_IMPERSONATION`.

### 4. Nastavení kalendáře v adminu

V **Dostupnost** → **Calendar ID** zadejte jako **první** v pořadí e-mail kalendáře, do kterého chcete zapisovat (např. `jana.stepanikova@sensio.cz`). Rezervace se zapisují do prvního kalendáře v seznamu.

## Výsledek

- Při potvrzení rezervace se vytvoří událost v kalendáři kolegyně
- Klient dostane e-mailovou pozvánku na událost (Google Calendar pozvánka)
- Konzultant (CONTACT_EMAIL) je také v účastnících

## Řešení problémů

**403 Forbidden / Service accounts cannot invite attendees**  
→ Domain-Wide Delegation není správně nastavené. Zkontrolujte kroky 1 a 2.

**404 Not Found**  
→ Kalendář není sdílen s Service Accountem (alespoň pro čtení). Kolegyně musí v nastavení kalendáře přidat `walance-calendar@...` s oprávněním „Zobrazit všechny podrobnosti události“.
