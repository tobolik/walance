<?php
/**
 * Vygeneruje hash hesla. Použití: api/generate-hash.php?p=Jana2026
 * Spusťte PHP server a otevřete v prohlížeči.
 */
header('Content-Type: text/plain; charset=utf-8');
$p = $_GET['p'] ?? '';
if (empty($p)) {
    echo "Použití: api/generate-hash.php?p=VaseHeslo";
    exit;
}
echo password_hash($p, PASSWORD_DEFAULT);
