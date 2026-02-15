<?php
/**
 * Konfigurace WALANCE – načítá verzi, veřejnou a lokální část
 * version.php – verze aplikace (často se mění)
 * config.public.php – výchozí hodnoty slotů, cesty
 * config.local.php – v .gitignore (e-mail, DB hesla, tokeny)
 */
require_once __DIR__ . '/version.php';
require_once __DIR__ . '/config.public.php';

if (!file_exists(__DIR__ . '/config.local.php')) {
    die('Chybí api/config.local.php. Zkopírujte config.local.example.php jako config.local.php a vyplňte hodnoty.');
}
require_once __DIR__ . '/config.local.php';

if (!defined('MAIL_FROM')) {
    define('MAIL_FROM', CONTACT_EMAIL);
}
