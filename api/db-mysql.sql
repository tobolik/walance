-- WALANCE - migrační SQL pro MySQL (soft-update schema)
-- Spuštění: mysql -u UZIVATEL -p walancecz < api/db-mysql.sql
-- Nebo: php api/migrate.php
-- Předpoklad: databáze walancecz již existuje

USE walancecz;

CREATE TABLE IF NOT EXISTS contacts (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    contacts_id INT UNSIGNED NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50),
    message TEXT,
    source VARCHAR(50) DEFAULT 'contact',
    notes TEXT,
    merged_into_contacts_id INT UNSIGNED NULL,
    valid_from DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    valid_to DATETIME NULL DEFAULT NULL,
    valid_user_from INT UNSIGNED NULL,
    valid_user_to INT UNSIGNED NULL,
    INDEX idx_contacts_id (contacts_id, valid_to),
    INDEX idx_v (valid_to),
    INDEX idx_merged (merged_into_contacts_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS admin_users (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS activities (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activities_id INT UNSIGNED NULL,
    contacts_id INT UNSIGNED NULL,
    bookings_id INT UNSIGNED NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
