-- VOGUEX E-Commerce Management System
-- Import this file in phpMyAdmin (Import tab) or paste it in the SQL tab.

CREATE DATABASE IF NOT EXISTS voguex_db
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE voguex_db;

-- 1. Users (customers + admin)
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 2. Categories (Men, Women, Accessories, Footwear)
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL UNIQUE
);

-- 3. Products
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  old_price DECIMAL(10,2) DEFAULT NULL,
  image VARCHAR(255) DEFAULT 'placeholder.jpg',
  badge VARCHAR(20) DEFAULT NULL,
  stock INT NOT NULL DEFAULT 50,
  is_bestseller TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

-- 4. Orders
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  address VARCHAR(255) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  status ENUM('Pending','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 5. Order items
CREATE TABLE IF NOT EXISTS order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity INT NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

-- Sample data
INSERT INTO categories (name) VALUES
  ('Men'), ('Women'), ('Accessories'), ('Footwear');

INSERT INTO products (category_id, name, description, price, old_price, image, badge, stock, is_bestseller) VALUES
  (1, 'Classic Polo T-Shirt', 'Soft cotton polo with a clean, classic fit.', 29.99, NULL,  'polo.jpg',      'NEW',  60, 1),
  (1, 'Cotton Overshirt',     'Lightweight overshirt, perfect for layering.', 47.99, 59.99, 'overshirt.jpg', '-20%', 40, 1),
  (1, 'Essential Hoodie',     'Everyday hoodie in premium heavyweight fabric.', 39.99, NULL, 'hoodie.jpg',   'NEW',  55, 1),
  (4, 'Minimal Sneakers',     'Clean white sneakers that go with everything.', 67.99, 79.99, 'sneakers.jpg', '-15%', 30, 1),
  (3, 'Daily Backpack',       'Water-resistant backpack with laptop sleeve.', 34.99, NULL,  'backpack.jpg',  NULL,   25, 0),
  (3, 'Black Cap',            'Adjustable cotton cap.',                         12.99, NULL,  'cap.jpg',       NULL,   80, 0),
  (2, 'Relaxed Linen Shirt',  'Breathable linen shirt for warm days.',         36.99, NULL,  'linen.jpg',     'NEW',  35, 0),
  (2, 'Oversized Tee',        'Boxy oversized tee in heavy cotton.',           24.99, 29.99, 'tee.jpg',       '-15%', 70, 0);

-- Default admin account  ->  email: admin@voguex.com   password: admin123
INSERT INTO users (name, email, password, role) VALUES
  ('Admin', 'admin@voguex.com', '$2y$10$6.onGLUvTz6rqzP/4BvKJedagt7xr3Du1GRjWAQBVx54YbJdteKAS', 'admin');
