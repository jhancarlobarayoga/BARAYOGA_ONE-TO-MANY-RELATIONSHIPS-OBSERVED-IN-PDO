-- Programmer_Client Database
CREATE DATABASE IF NOT EXISTS programmer_client_activity
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

-- Specified Database to insert new tables
USE programmer_client_activity;

-- Programmer Table
CREATE TABLE IF NOT EXISTS programmer (
    programmer_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NULL,
    specialization VARCHAR(150) NOT NULL,
    date_added TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (programmer_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Client Table
CREATE TABLE IF NOT EXISTS client (
    client_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    first_name VARCHAR(60) NOT NULL,
    last_name VARCHAR(60) NOT NULL,
    company_name VARCHAR(120) NULL,
    email_address VARCHAR(150) NOT NULL,
    contact_number VARCHAR(30) NULL,
    service_requested VARCHAR(120) NOT NULL,
    project_details TEXT NULL,
    programmer_id INT UNSIGNED NOT NULL,
    date_added TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (client_id),
    KEY idx_client_programmer_id (programmer_id),
    CONSTRAINT fk_client_programmer
        FOREIGN KEY (programmer_id)
        REFERENCES programmer (programmer_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

