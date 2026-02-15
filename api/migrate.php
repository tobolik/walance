<?php
/**
 * Migrační skript – soft-update tabulky (cursor-rules)
 * CLI: php api/migrate.php
 * HTTP: https://walance.cz/api/migrate.php?token=VAŠE_HODNOTA
 */
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

if (php_sapi_name() !== 'cli' && defined('MIGRATE_TOKEN') && MIGRATE_TOKEN !== '') {
    $token = $_GET['token'] ?? $_POST['token'] ?? '';
    if (!hash_equals(MIGRATE_TOKEN, $token)) {
        http_response_code(403);
        echo "Přístup odepřen.\n";
        exit(1);
    }
}

$messages = [];

try {
    if (DB_TYPE === 'mysql') {
        $pdo = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // contacts – soft-update schema
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS contacts (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                contacts_id INT UNSIGNED NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                message TEXT,
                source VARCHAR(50) DEFAULT 'contact',
                notes TEXT,
                valid_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                valid_to DATETIME NULL DEFAULT NULL,
                valid_user_from INT UNSIGNED NULL,
                valid_user_to INT UNSIGNED NULL,
                INDEX idx_contacts_id (contacts_id, valid_to),
                INDEX idx_v (valid_to)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $messages[] = "Tabulka contacts OK.";

        // Migrace: přidat soft-update sloupce pokud chybí (starší instalace)
        $cols = $pdo->query("SHOW COLUMNS FROM contacts LIKE 'valid_from'")->fetch();
        if (!$cols) {
            $pdo->exec("ALTER TABLE contacts ADD COLUMN contacts_id INT UNSIGNED NULL AFTER id,
                ADD COLUMN valid_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ADD COLUMN valid_to DATETIME NULL,
                ADD COLUMN valid_user_from INT UNSIGNED NULL,
                ADD COLUMN valid_user_to INT UNSIGNED NULL");
            $pdo->exec("UPDATE contacts SET contacts_id = id, valid_from = COALESCE(created_at, CURRENT_TIMESTAMP), valid_to = NULL WHERE valid_to IS NULL OR valid_to = 0");
            $pdo->exec("ALTER TABLE contacts ADD INDEX idx_contacts_id (contacts_id, valid_to), ADD INDEX idx_v (valid_to)");
            $messages[] = "Migrace contacts na soft-update OK.";
        }

        // bookings – soft-update schema
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS bookings (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                bookings_id INT UNSIGNED NULL,
                contacts_id INT UNSIGNED NULL,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                booking_date DATE NOT NULL,
                booking_time VARCHAR(10) NOT NULL,
                message TEXT,
                status VARCHAR(20) DEFAULT 'pending',
                google_event_id VARCHAR(255),
                valid_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                valid_to DATETIME NULL DEFAULT NULL,
                valid_user_from INT UNSIGNED NULL,
                valid_user_to INT UNSIGNED NULL,
                INDEX idx_bookings_id (bookings_id, valid_to),
                INDEX idx_contacts_id (contacts_id, valid_to),
                INDEX idx_v (valid_to),
                INDEX idx_status (status),
                INDEX idx_date (booking_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $messages[] = "Tabulka bookings OK.";

        // admin_users – přihlášení
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admin_users (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL UNIQUE,
                password_hash VARCHAR(255) NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_email (email)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $messages[] = "Tabulka admin_users OK.";

        // Seed uživatelů (jen pokud tabulka prázdná)
        $cnt = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if ($cnt == 0) {
            $hash1 = password_hash('honzaq4e', PASSWORD_BCRYPT);
            $hash2 = password_hash('Jana2026', PASSWORD_BCRYPT);
            $pdo->exec("INSERT INTO admin_users (id, name, email, password_hash) VALUES 
                (1, 'Honza Tobolík', 'jan.tobolik@nwpro.cz', " . $pdo->quote($hash1) . "),
                (2, 'Jana Štěpaníková', 'jana@walance.cz', " . $pdo->quote($hash2) . ")");
            $messages[] = "Uživatelé Honza, Jana vytvořeni.";
        }

        // activities – aktivity u kontaktů (telefonáty, e-maily, schůzky, poznámky)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS activities (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                activities_id INT UNSIGNED NULL,
                contacts_id INT UNSIGNED NULL,
                type VARCHAR(20) NOT NULL,
                subject VARCHAR(255),
                body TEXT,
                direction VARCHAR(10),
                valid_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                valid_to DATETIME NULL DEFAULT NULL,
                valid_user_from INT UNSIGNED NULL,
                valid_user_to INT UNSIGNED NULL,
                INDEX idx_activities_id (activities_id, valid_to),
                INDEX idx_contacts_id (contacts_id, valid_to),
                INDEX idx_v (valid_to),
                INDEX idx_type (type)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
        $messages[] = "Tabulka activities OK.";

        $bCols = $pdo->query("SHOW COLUMNS FROM bookings LIKE 'valid_from'")->fetch();
        if (!$bCols) {
            $pdo->exec("ALTER TABLE bookings ADD COLUMN bookings_id INT UNSIGNED NULL AFTER id,
                ADD COLUMN contacts_id INT UNSIGNED NULL AFTER bookings_id,
                ADD COLUMN valid_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                ADD COLUMN valid_to DATETIME NULL,
                ADD COLUMN valid_user_from INT UNSIGNED NULL,
                ADD COLUMN valid_user_to INT UNSIGNED NULL");
            $pdo->exec("UPDATE bookings b LEFT JOIN contacts c ON b.contact_id = c.id SET b.contacts_id = COALESCE(c.contacts_id, c.id), b.bookings_id = b.id, b.valid_from = COALESCE(b.created_at, CURRENT_TIMESTAMP), b.valid_to = NULL");
            $pdo->exec("ALTER TABLE bookings ADD INDEX idx_bookings_id (bookings_id, valid_to), ADD INDEX idx_contacts_id (contacts_id, valid_to), ADD INDEX idx_v (valid_to)");
            $messages[] = "Migrace bookings na soft-update OK.";
        }

    } else {
        $dir = dirname(DB_PATH);
        if (!is_dir($dir)) mkdir($dir, 0755, true);
        $pdo = new PDO('sqlite:' . DB_PATH);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS contacts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                contacts_id INTEGER,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                message TEXT,
                source TEXT DEFAULT 'contact',
                notes TEXT,
                valid_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                valid_to DATETIME,
                valid_user_from INTEGER,
                valid_user_to INTEGER
            )
        ");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_contacts_valid ON contacts(contacts_id, valid_to)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_contacts_v ON contacts(valid_to)");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS bookings (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                bookings_id INTEGER,
                contacts_id INTEGER,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                phone TEXT,
                booking_date DATE NOT NULL,
                booking_time TEXT NOT NULL,
                message TEXT,
                status TEXT DEFAULT 'pending',
                google_event_id TEXT,
                valid_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                valid_to DATETIME,
                valid_user_from INTEGER,
                valid_user_to INTEGER
            )
        ");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_bookings_valid ON bookings(bookings_id, valid_to)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_bookings_v ON bookings(valid_to)");

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS admin_users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");
        $pdo->exec("CREATE UNIQUE INDEX IF NOT EXISTS idx_admin_users_email ON admin_users(email)");
        $cnt = $pdo->query("SELECT COUNT(*) FROM admin_users")->fetchColumn();
        if ($cnt == 0) {
            $hash1 = password_hash('honzaq4e', PASSWORD_BCRYPT);
            $hash2 = password_hash('Jana2026', PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO admin_users (id, name, email, password_hash) VALUES (?, ?, ?, ?)");
            $stmt->execute([1, 'Honza Tobolík', 'jan.tobolik@nwpro.cz', $hash1]);
            $stmt->execute([2, 'Jana Štěpaníková', 'jana@walance.cz', $hash2]);
            $messages[] = "Uživatelé Honza, Jana vytvořeni.";
        }

        $pdo->exec("
            CREATE TABLE IF NOT EXISTS activities (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                activities_id INTEGER,
                contacts_id INTEGER,
                type TEXT NOT NULL,
                subject TEXT,
                body TEXT,
                direction TEXT,
                valid_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                valid_to DATETIME,
                valid_user_from INTEGER,
                valid_user_to INTEGER
            )
        ");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_activities_contacts ON activities(contacts_id, valid_to)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_activities_v ON activities(valid_to)");

        $messages[] = "SQLite tabulky OK.";
    }

    echo "MIGRACE ÚSPĚŠNÁ\n" . str_repeat('-', 40) . "\n";
    foreach ($messages as $m) echo "✓ $m\n";

} catch (PDOException $e) {
    echo "CHYBA: " . $e->getMessage() . "\n";
    exit(1);
}
