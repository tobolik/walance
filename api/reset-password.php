<?php
/**
 * Reset hesla admin uživatele – oprava po upgradu PHP (např. 7.4 → 8.2)
 * CLI: php api/reset-password.php email@example.com nove_heslo
 * HTTP: api/reset-password.php?token=MIGRATE_TOKEN&email=email@example.com&password=nove_heslo
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

header('Content-Type: text/plain; charset=utf-8');

$email = '';
$password = '';

if (php_sapi_name() === 'cli') {
    $email = $argv[1] ?? '';
    $password = $argv[2] ?? '';
} else {
    if (defined('MIGRATE_TOKEN') && MIGRATE_TOKEN !== '') {
        $token = $_GET['token'] ?? $_POST['token'] ?? '';
        if (!hash_equals(MIGRATE_TOKEN, $token)) {
            http_response_code(403);
            echo "Přístup odepřen. Použijte token z config.local.php (MIGRATE_TOKEN).\n";
            exit(1);
        }
    }
    $email = trim($_GET['email'] ?? $_POST['email'] ?? '');
    $password = $_GET['password'] ?? $_POST['password'] ?? '';
}

if (empty($email) || empty($password)) {
    echo "Použití:\n";
    echo "  CLI: php api/reset-password.php email@example.com nove_heslo\n";
    echo "  HTTP: api/reset-password.php?token=TOKEN&email=email@example.com&password=nove_heslo\n";
    exit(1);
}

if (strlen($password) < 6) {
    echo "Heslo musí mít alespoň 6 znaků.\n";
    exit(1);
}

try {
    $db = getDb();
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $stmt = $db->prepare("UPDATE admin_users SET password_hash = ? WHERE email = ?");
    $stmt->execute([$hash, $email]);
    if ($stmt->rowCount() === 0) {
        echo "Uživatel s e-mailem '$email' nebyl nalezen.\n";
        exit(1);
    }
    echo "Heslo pro $email bylo úspěšně změněno.\n";
} catch (Exception $e) {
    echo "Chyba: " . $e->getMessage() . "\n";
    exit(1);
}
