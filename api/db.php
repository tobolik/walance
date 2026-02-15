<?php
/**
 * Inicializace databáze - podpora SQLite i MySQL
 */
require_once __DIR__ . '/config.php';

function getDb(): PDO {
    if (DB_TYPE === 'mysql') {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
    } else {
        $dir = dirname(DB_PATH);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        $pdo = new PDO('sqlite:' . DB_PATH);
    }
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (DB_TYPE === 'mysql') {
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
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS bookings (
                id INT AUTO_INCREMENT PRIMARY KEY,
                contact_id INT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) NOT NULL,
                phone VARCHAR(50),
                booking_date DATE NOT NULL,
                booking_time VARCHAR(10) NOT NULL,
                message TEXT,
                status VARCHAR(20) DEFAULT 'pending',
                google_event_id VARCHAR(255),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (contact_id) REFERENCES contacts(id) ON DELETE SET NULL,
                INDEX idx_status (status),
                INDEX idx_date (booking_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    } else {
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
    }

    return $pdo;
}
