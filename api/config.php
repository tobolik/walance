<?php
/**
 * Konfigurace WALANCE
 * Upravte hodnoty podle vašeho prostředí
 */

// E-mail pro příjem zpráv
define('CONTACT_EMAIL', 'info@walance.cz');

// Databáze: 'sqlite' nebo 'mysql'
define('DB_TYPE', 'sqlite'); // změňte na 'mysql' pro MySQL

// SQLite (použije se když DB_TYPE = 'sqlite')
define('DB_PATH', __DIR__ . '/../data/contacts.db');

// MySQL (použije se když DB_TYPE = 'mysql') - viz api/db-mysql.sql pro vytvoření DB
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'netwalkingcz');
define('DB_USER', 'netwalkingcz001');
define('DB_PASS', '61hAEefn');

// Admin - heslo: Jana2026
define('ADMIN_PASSWORD_HASH', '$2b$10$jtmKFFne7iDWtCFsupaKlezBLJoEIUjy62SCJKSQZY4ybZRgKPVye');

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
