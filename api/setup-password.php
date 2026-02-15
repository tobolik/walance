<?php
/**
 * Vygeneruje hash pro admin heslo.
 * Spusťte: php setup-password.php vase_heslo
 * Výstup vložte do tabulky admin_users (nebo použijte migraci)
 */
$password = $argv[1] ?? 'walance2026';
echo password_hash($password, PASSWORD_DEFAULT);
