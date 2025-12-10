-- Database Schema for Garbage Collection Management System

CREATE DATABASE IF NOT EXISTS garbage_collection_db;
USE garbage_collection_db;

-- 1. Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'driver', 'resident') NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    address TEXT, -- Nullable for admins/drivers, required for residents logically
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Schedules (Zones and Definitions)
-- A simplified model where residents belong to a 'zone' or just address-based.
-- For simplicity: Admin defines schedules like "Every Monday for Zone A"
CREATE TABLE IF NOT EXISTS schedules (
    id INT AUTO_INCREMENT PRIMARY KEY,
    zone_name VARCHAR(100) NOT NULL, -- e.g., "Downtown", "Suburb A"
    collection_day ENUM('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday') NOT NULL,
    waste_type ENUM('General', 'Recycling', 'Green', 'Bulk') NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Collection Routes (Daily instances of work for drivers)
CREATE TABLE IF NOT EXISTS routes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    driver_id INT,
    schedule_id INT, -- Links to the zone/day definition
    collection_date DATE NOT NULL,
    status ENUM('Pending', 'In Progress', 'Completed') DEFAULT 'Pending',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE CASCADE
);

-- 4. Service Requests (From residents)
CREATE TABLE IF NOT EXISTS service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    request_type ENUM('Missed Pickup', 'Bin Damage', 'Bulk Pickup', 'Other') NOT NULL,
    description TEXT,
    status ENUM('Open', 'In Progress', 'Resolved', 'Rejected') DEFAULT 'Open',
    admin_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Logs (Audit trail)
CREATE TABLE IF NOT EXISTS logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(255) NOT NULL,
    details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SAMPLE DATA
INSERT INTO users (username, email, password_hash, role, full_name, address) VALUES
('admin', 'admin@gc.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'System Administrator', 'HQ'),
('driver1', 'driver1@gc.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'driver', 'John Driver', NULL),
('resident1', 'res1@gc.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'resident', 'Jane Resident', '123 Maple St, Zone A');
-- Note: Password is 'password' for all

INSERT INTO schedules (zone_name, collection_day, waste_type) VALUES
('Zone A', 'Monday', 'General'),
('Zone A', 'Wednesday', 'Recycling'),
('Zone B', 'Tuesday', 'General');
