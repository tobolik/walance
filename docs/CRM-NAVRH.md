# Návrh CRM systému WALANCE

## 1. Přehled

Rozšíření stávajícího CRM o:
- **Emailing** – hromadné e-maily, šablony, kampaně
- **Aktivity** – telefonáty, e-maily, schůzky, poznámky (timeline u kontaktu)
- **Potvrzení rezervací** – již implementováno v admin/bookings.php

---

## 2. Datový model (soft-update)

### 2.1 Tabulka `activities`
Aktivity vázané na kontakt (entity_id = contacts_id).

| Sloupec | Typ | Popis |
|---------|-----|-------|
| id | INT | PK |
| activities_id | INT | entity_id |
| contacts_id | INT | FK na kontakt |
| type | VARCHAR(20) | call, email, meeting, note |
| subject | VARCHAR(255) | Předmět |
| body | TEXT | Obsah / poznámka |
| direction | VARCHAR(10) | in, out (pro call/email) |
| valid_from, valid_to, valid_user_* | | soft-update |

### 2.2 Tabulka `email_templates`
Šablony pro hromadné e-maily.

| Sloupec | Typ | Popis |
|---------|-----|-------|
| id | INT | PK |
| name | VARCHAR(255) | Název šablony |
| subject | VARCHAR(255) | Předmět |
| body | TEXT | Tělo (podpora {{name}}, {{email}}) |
| valid_from, valid_to | | soft-update |

### 2.3 Tabulka `email_campaigns`
Kampaně – odeslání šablony skupině kontaktů.

| Sloupec | Typ | Popis |
|---------|-----|-------|
| id | INT | PK |
| template_id | INT | FK |
| name | VARCHAR(255) | Název kampaně |
| sent_at | DATETIME | Kdy odesláno |
| total_sent | INT | Počet odeslaných |
| filter_source | VARCHAR(50) | Volitelný filtr (contact, booking) |

### 2.4 Tabulka `email_log`
Log odeslaných e-mailů (kvůli kampaním i jednotlivým).

| Sloupec | Typ | Popis |
|---------|-----|-------|
| id | INT | PK |
| campaign_id | INT NULL | FK, NULL = jednotlivý |
| contact_id | INT | Komu |
| subject | VARCHAR(255) | |
| sent_at | DATETIME | |
| status | VARCHAR(20) | sent, bounced, failed |

---

## 3. Funkční moduly

### 3.1 Aktivity (timeline)
- **Admin: Kontakt detail** – nová stránka `contact.php?id=X`
- Timeline: seznam aktivit (telefonáty, e-maily, schůzky, poznámky)
- Přidání aktivity: formulář (typ, předmět, obsah, směr)
- Filtrování podle typu, data

### 3.2 Emailing
- **Šablony** – CRUD v admin (email-templates.php)
- **Kampaň** – výběr šablony, výběr kontaktů (filtr: zdroj, datum), náhled, odeslání
- Odesílání: PHP `mail()` nebo SMTP (PHPMailer)
- Log: kdo, kdy, komu, stav

### 3.3 Rozšíření stávajícího
- **Dashboard** – přehled aktivit (poslední X), nadcházející rezervace
- **Kontakty** – odkaz na detail s aktivitami
- **Rezervace** – potvrzení/zamítnutí (hotovo)

---

## 4. Implementační pořadí

1. **Fáze 1:** Tabulka `activities`, migrace, admin stránka pro přidání aktivity
2. **Fáze 2:** Detail kontaktu s timeline aktivit
3. **Fáze 3:** Tabulky `email_templates`, `email_campaigns`, `email_log`
4. **Fáze 4:** CRUD šablon, odesílání kampaně
5. **Fáze 5:** SMTP (PHPMailer) pro spolehlivé doručení

---

## 5. Technické poznámky

- **Soft-update** – všechny nové tabulky dle cursor-rules
- **E-mailing** – zvážit službu (SendGrid, Mailgun) pro větší objemy
- **GDPR** – log odeslaných e-mailů, možnost odhlášení z kampaní
