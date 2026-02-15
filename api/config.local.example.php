<?php
/**
 * Lokální konfigurace – citlivé údaje
 * Zkopírujte jako config.local.php a vyplňte hodnoty.
 * config.local.php je v .gitignore a nebude publikován.
 */

// E-mail pro příjem zpráv z kontaktního formuláře a rezervací
define('CONTACT_EMAIL', 'info@walance.cz');

// Odesílatel systémových e-mailů (rezervace, potvrzení termínu) – bez nastavení se použije CONTACT_EMAIL
define('MAIL_FROM', 'info@walance.cz');

// MySQL – použije se když DB_TYPE = 'mysql' v config.public.php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'walancecz');
define('DB_USER', 'walancecz001');
define('DB_PASS', 'vaše-heslo');

// Token pro spuštění migrace přes HTTP (deploy)
// Nastavte stejnou hodnotu v GitHub Secrets jako MIGRATE_TOKEN
define('MIGRATE_TOKEN', 'váš-náhodný-token');

// Google Calendar – zapis do kalendáře jiného uživatele (Domain-Wide Delegation)
// Povolte, pokud chcete zapisovat události do kalendáře kolegy (např. jana@firma.cz)
// Vyžaduje nastavení Domain-Wide Delegation v Google Workspace Admin – viz docs/GOOGLE-CALENDAR-DELEGATION.md
// define('GOOGLE_CALENDAR_IMPERSONATION', true);
