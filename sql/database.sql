-- ============================================
-- Smart Delivery Verification System
-- Database Schema
-- ============================================

CREATE DATABASE IF NOT EXISTS smart_delivery;
USE smart_delivery;

-- ----------------------------
-- Table: users
-- Stores Customer, Rider, and Admin accounts (role-based)
-- ----------------------------
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('customer', 'rider', 'admin') NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------
-- Table: products
-- ----------------------------
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255) DEFAULT 'no-image.png',
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ----------------------------
-- Table: orders
-- status: Pending -> Assigned -> Delivered
-- ----------------------------
CREATE TABLE orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    rider_id INT DEFAULT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    delivery_address VARCHAR(255) NOT NULL,
    status ENUM('Pending', 'Assigned', 'Delivered') NOT NULL DEFAULT 'Pending',
    otp_code VARCHAR(6) DEFAULT NULL,
    proof_photo VARCHAR(255) DEFAULT NULL,
    rider_lat DECIMAL(10, 7) DEFAULT NULL,
    rider_lng DECIMAL(10, 7) DEFAULT NULL,
    location_updated_at TIMESTAMP NULL DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    delivered_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (customer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (rider_id) REFERENCES users(user_id) ON DELETE SET NULL
);

-- ----------------------------
-- Table: order_items
-- Line items for each order (cart contents at checkout time)
-- ----------------------------
CREATE TABLE order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

-- ----------------------------
-- Seed Data
-- ----------------------------
-- NOTE: Demo user accounts (admin / rider / customer) are NOT inserted here.
-- Run seed.php once in your browser after setup (see README) - it creates
-- them with PHP's password_hash() so the bcrypt hash is guaranteed to be
-- valid on YOUR PHP installation. Hardcoding a hash here could silently
-- break login if it was generated on a different PHP/bcrypt version.

-- Sample Products
INSERT INTO products (name, description, price, image, stock) VALUES
('Wireless Mouse', 'Ergonomic wireless mouse with USB receiver, 2.4GHz connectivity.', 650.00, 'no-image.png', 50),
('Mechanical Keyboard', 'RGB backlit mechanical keyboard with blue switches.', 2200.00, 'no-image.png', 30),
('Bluetooth Headphones', 'Over-ear Bluetooth 5.0 headphones with noise cancellation.', 1800.00, 'no-image.png', 25),
('USB-C Hub', '6-in-1 USB-C hub with HDMI, USB 3.0, and SD card reader.', 1200.00, 'no-image.png', 40),
('Laptop Stand', 'Adjustable aluminum laptop stand, foldable and portable.', 950.00, 'no-image.png', 20),
('Webcam 1080p', 'Full HD webcam with built-in microphone for video calls.', 1500.00, 'no-image.png', 35);
