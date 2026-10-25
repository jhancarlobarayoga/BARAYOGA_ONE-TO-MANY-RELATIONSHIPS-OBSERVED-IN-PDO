-- Simple database for the Programmer and Client Management System
CREATE DATABASE IF NOT EXISTS programmer_client_basic;
USE programmer_client_system;

-- Parent table: one programmer can have many clients.
CREATE TABLE IF NOT EXISTS programmer (
    programmer_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    specialization VARCHAR(100) NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Child table: each client belongs to one programmer.
CREATE TABLE IF NOT EXISTS client (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email_address VARCHAR(100),
    service_requested VARCHAR(100) NOT NULL,
    programmer_id INT NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_client_programmer
        FOREIGN KEY (programmer_id)
        REFERENCES programmer(programmer_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);
