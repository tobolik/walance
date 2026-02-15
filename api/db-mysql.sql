-- WALANCE - vytvoření databáze pro MySQL
-- 1. Vytvořte DB a uživatele (jako root):
--    CREATE DATABASE walance CHARACTER SET utf8mb4;
--    CREATE USER 'walance'@'localhost' IDENTIFIED BY 'vase_heslo';
--    GRANT ALL ON walance.* TO 'walance'@'localhost';
-- 2. Spusťte tento soubor: mysql -u walance -p walance < api/db-mysql.sql

USE walance;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
