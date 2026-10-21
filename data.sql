-- Programmer and Client Database Management System
CREATE DATABASE IF NOT EXISTS programmer_client_activity;

-- Using the specified database to insert tables
USE programmer_client_activity;

-- Parent Entity: Programmer Table
CREATE TABLE IF NOT EXISTS programmer (
    programmer_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    date_of_birth DATE,
    specialization VARCHAR(150),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Child Entity: Client Table
CREATE TABLE IF NOT EXISTS client (
    client_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(60),
    last_name VARCHAR(60),
    company_name VARCHAR(120),
    email_address VARCHAR(150),
    contact_number VARCHAR(30),
    service_requested VARCHAR(120),
    project_details TEXT,
    programmer_id INT NOT NULL,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Foreign key usage of Child Entity to reference/link to Parent Entity
    FOREIGN KEY (programmer_id) REFERENCES programmer(programmer_id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
);
