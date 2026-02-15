<?php
/**
 * Konfigurace WALANCE
 * Upravte hodnoty podle vašeho prostředí
 */

// Verze aplikace (vMAJOR.MINOR.PATCH) – při změně zvyš PATCH, cache busting ?v=
define('APP_VERSION', '1.0.0');

// E-mail pro příjem zpráv
define('CONTACT_EMAIL', 'info@walance.cz');

// Databáze: 'sqlite' nebo 'mysql'
define('DB_TYPE', 'mysql'); // změňte na 'mysql' pro MySQL

// SQLite (použije se když DB_TYPE = 'sqlite')
define('DB_PATH', __DIR__ . '/../data/contacts.db');

// MySQL (použije se když DB_TYPE = 'mysql') - viz api/db-mysql.sql pro vytvoření DB
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'walancecz');
define('DB_USER', 'walancecz001');
define('DB_PASS', 'ThlCRD9v');

// Token pro spuštění migrace přes HTTP (deploy) – nastavte stejnou hodnotu v GitHub Secrets jako MIGRATE_TOKEN
define('MIGRATE_TOKEN', 'Jana2026'); // např. 'váš-náhodný-token-xyz'

// Google Calendar (volitelné - pro napojení rezervací)
// 1. Vytvořte projekt na https://console.cloud.google.com/
// 2. Povolte Google Calendar API
// 3. Vytvořte Service Account a stáhněte JSON
// 4. Umístěte do api/credentials/google-calendar.json
define('GOOGLE_CALENDAR_ENABLED', file_exists(__DIR__ . '/credentials/google-calendar.json'));
define('GOOGLE_CALENDAR_CREDENTIALS', __DIR__ . '/credentials/google-calendar.json');
define('GOOGLE_CALENDAR_ID', 'primary'); // nebo konkrétní ID kalendáře

// Dostupné časové sloty (9:00-17:00, každých 30 min, Po-Pá)
define('SLOT_START', 9);
define('SLOT_END', 17);
define('SLOT_INTERVAL', 30); // minut
define('BOOKING_DAYS_AHEAD', 14);
