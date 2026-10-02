-- Database Schema for AI-Powered Agriculture E-Marketplace
CREATE DATABASE IF NOT EXISTS e_agriculture_db;
USE e_agriculture_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20) NOT NULL,
    role ENUM('farmer', 'buyer', 'admin') NOT NULL,
    password VARCHAR(255) NOT NULL,
    profile_image VARCHAR(255),
    -- Common Loc
    state VARCHAR(50),
    district VARCHAR(50),
    location VARCHAR(100),
    -- Farmer Specific
    land_size DECIMAL(10,2),
    crops VARCHAR(255),
    farm_name VARCHAR(100),
    bank_details VARCHAR(255),
    -- Buyer Specific
    business_name VARCHAR(100),
    purchase_prefs VARCHAR(255),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Crops Table
CREATE TABLE IF NOT EXISTS crops (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    price_unit VARCHAR(20) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    stock_status ENUM('In Stock', 'Low Stock', 'Out of Stock') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


-- Orders Table
CREATE TABLE IF NOT EXISTS orders (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    buyer_id INT(6) UNSIGNED NOT NULL,
    farmer_id INT(6) UNSIGNED NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    status ENUM('Pending', 'Accepted', 'Packed', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (buyer_id) REFERENCES users(id),
    FOREIGN KEY (farmer_id) REFERENCES users(id)
);

-- Order Items Table
CREATE TABLE IF NOT EXISTS order_items (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT(6) UNSIGNED NOT NULL,
    crop_id INT(6) UNSIGNED NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    price_per_unit DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (crop_id) REFERENCES crops(id)
);

-- Farmer Profiles (Extended)
CREATE TABLE IF NOT EXISTS farmer_profiles (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    bio TEXT,
    verification_status ENUM('Pending', 'Verified', 'Rejected') DEFAULT 'Pending',
    verification_doc VARCHAR(255),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Reviews Table
CREATE TABLE IF NOT EXISTS reviews (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT(6) UNSIGNED NOT NULL,
    buyer_id INT(6) UNSIGNED NOT NULL,
    rating INT(1) NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES users(id),
    FOREIGN KEY (buyer_id) REFERENCES users(id)
);

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id INT(6) UNSIGNED NOT NULL,
    title VARCHAR(100) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('order', 'payment', 'system', 'alert') NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Disease Reports Table
CREATE TABLE IF NOT EXISTS disease_reports (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    farmer_id INT(6) UNSIGNED NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    diagnosis VARCHAR(255),
    severity VARCHAR(50),
    treatment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (farmer_id) REFERENCES users(id)
);

-- Crop Analytics (Mock Data Store)
CREATE TABLE IF NOT EXISTS crop_analytics (
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    crop_name VARCHAR(100) NOT NULL,
    region VARCHAR(100),
    demand_score INT(3),
    price_trend ENUM('Up', 'Down', 'Stable'),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample Data for Crops
INSERT INTO crops (name, price, price_unit, image_path, stock_status) VALUES
('Organic Wheat', 250.00, 'ton', 'images/crop-wheat.png', 'In Stock'),
('Sweet Corn', 190.00, 'ton', 'images/crop-corn.png', 'Low Stock'),
('Fresh Tomatoes', 1.50, 'kg', 'images/crop-tomato.png', 'In Stock');

