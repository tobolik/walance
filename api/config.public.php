<?php
/**
 * Veřejná konfigurace WALANCE – bezpečná pro publikaci v gitu
 * Výchozí hodnoty slotů, cesty, příznaky (verze je v version.php)
 */

// Databáze: 'sqlite' nebo 'mysql'
define('DB_TYPE', 'mysql');

// SQLite – cesta k databázovému souboru (použije se když DB_TYPE = 'sqlite')
define('DB_PATH', __DIR__ . '/../data/contacts.db');

// Dostupné časové sloty – výchozí hodnoty (admin může přepsat v Dostupnost)
define('SLOT_START', 9);
define('SLOT_END', 17);
define('SLOT_INTERVAL', 30); // minut
define('BOOKING_DAYS_AHEAD', 14);

// Google Calendar – cesty a ID (credentials JSON je v .gitignore)
define('GOOGLE_CALENDAR_CREDENTIALS', __DIR__ . '/credentials/google-calendar.json');
define('GOOGLE_CALENDAR_ID', 'primary');
define('GOOGLE_CALENDAR_ENABLED', file_exists(__DIR__ . '/credentials/google-calendar.json'));
