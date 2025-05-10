
CREATE DATABASE IF NOT EXISTS blood_management_system;
USE blood_management_system;

-- Users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Donors table
CREATE TABLE donors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    age INT NOT NULL,
    address TEXT NOT NULL,
    phone VARCHAR(20) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    quantity DECIMAL(5,2) NOT NULL,
    donation_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    added_by INT,
    FOREIGN KEY (added_by) REFERENCES users(id)
);

-- Blood inventory table
CREATE TABLE blood_inventory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL UNIQUE,
    quantity DECIMAL(5,2) NOT NULL DEFAULT 0
);

-- Blood out records
CREATE TABLE blood_out (
    id INT AUTO_INCREMENT PRIMARY KEY,
    buyer_name VARCHAR(100) NOT NULL,
    blood_group ENUM('A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-') NOT NULL,
    quantity DECIMAL(5,2) NOT NULL,
    contact VARCHAR(20) NOT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    processed_by INT,
    FOREIGN KEY (processed_by) REFERENCES users(id)
);

-- Insert initial blood groups
INSERT INTO blood_inventory (blood_group, quantity) VALUES 
('A+', 0), ('A-', 0), ('B+', 0), ('B-', 0),
('AB+', 0), ('AB-', 0), ('O+', 0), ('O-', 0);