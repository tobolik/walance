<?php
/**
 * Migrační skript – vytvoření tabulek v databázi
 * CLI: php api/migrate.php
 * HTTP: https://walance.cz/api/migrate.php?token=VAŠE_HODNOTA (vyžaduje MIGRATE_TOKEN v config)
 */
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

// Při volání přes HTTP vyžadovat token (pokud je v config nastaven)
if (php_sapi_name() !== 'cli' && defined('MIGRATE_TOKEN') && MIGRATE_TOKEN !== '') {
    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    if (!hash_equals(MIGRATE_TOKEN, $token)) {
        http_response_code(403);
        echo "Přístup odepřen. Vyžadován platný token.\n";
        exit(1);
    }
}

$errors = [];
$messages = [];

try {
    if (DB_TYPE === 'mysql') {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $messages[] = "Připojeno k MySQL databázi '" . DB_NAME . "'.";

        // Tabulka contacts
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS contacts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                message TEXT,
                source VARCHAR(50) DEFAULT 'contact',
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $messages[] = "Tabulka contacts OK.";

        // Tabulka bookings (FK na contacts – vytváříme po ní)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS bookings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                contact_id INT NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                booking_date DATE NOT NULL,
                booking_time VARCHAR(10) NOT NULL,
                message TEXT,
                status VARCHAR(20) DEFAULT 'pending',
                google_event_id VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_status (status),
                INDEX idx_date (booking_date),
                INDEX idx_contact (contact_id),
                CONSTRAINT fk_bookings_contact FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $messages[] = "Tabulka bookings OK.";

    } else {
        // SQLite
        $dir = dirname(DB_PATH);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
            $messages[] = "Složka data/ vytvořena.";
        }
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $messages[] = "SQLite databáze '" . DB_PATH . "' připravena.";

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS contacts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                message TEXT,
                source TEXT DEFAULT 'contact',
                notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $messages[] = "Tabulka contacts OK.";

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                contact_id INTEGER,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                booking_date DATE NOT NULL,
                booking_time TEXT NOT NULL,
                message TEXT,
                status TEXT DEFAULT 'pending',
                google_event_id TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (contact_id) REFERENCES contacts(id)
            )
        ");
        $messages[] = "Tabulka bookings OK.";
    }

    echo "MIGRACE ÚSPĚŠNÁ\n";
    echo str_repeat('-', 40) . "\n";
    foreach ($messages as $m) {
        echo "✓ $m\n";
    }

} catch (PDOException $e) {
    echo "CHYBA MIGRACE\n";
    echo str_repeat('-', 40) . "\n";
    echo $e->getMessage() . "\n";
    exit(1);
}
