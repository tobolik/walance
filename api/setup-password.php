<?php
/**
 * Vygeneruje hash pro admin heslo.
 * Spusťte: php setup-password.php vase_heslo
 * Výstup vložte do config.php jako ADMIN_PASSWORD_HASH
 */
$password = $argv[1] ?? 'walance2026';
echo password_hash($password, PASSWORD_DEFAULT);
