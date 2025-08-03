-- Database schema for Ganpati Donation System
-- Create database
CREATE DATABASE IF NOT EXISTS ganpati_donations CHARACTER SET utf8 COLLATE utf8_general_ci;
USE ganpati_donations;

-- Donations table
CREATE TABLE IF NOT EXISTS donations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    receipt_number VARCHAR(20) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    flat_number VARCHAR(20) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    amount_in_words VARCHAR(500) NOT NULL,
    payment_mode ENUM('Cash', 'UPI') NOT NULL,
    date_created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert default admin user (password: admin123)
INSERT INTO admins (username, password, full_name) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Create indexes for better performance
CREATE INDEX idx_receipt_number ON donations(receipt_number);
CREATE INDEX idx_date_created ON donations(date_created);
CREATE INDEX idx_flat_number ON donations(flat_number);