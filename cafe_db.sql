-- 1. Database Creation
-- Creates the database if it doesn't exist and sets Turkish character support.
CREATE DATABASE IF NOT EXISTS cafe_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_turkish_ci;
USE cafe_db;

-- 2. USERS TABLE
-- Implements 'Soft Delete' logic via 'is_active' column.
-- We never physically delete a user to preserve order history.
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(20) NOT NULL,
    email VARCHAR(30) NOT NULL UNIQUE,
    password VARCHAR(16) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    is_active BOOLEAN DEFAULT 1, -- 1: Active User, 0: Deleted/Banned User
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. CATEGORIES TABLE
-- Categories can be hidden from the menu without deleting them.
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(20) NOT NULL,
    is_active BOOLEAN DEFAULT 1 -- 1: Visible in Menu, 0: Hidden
);

-- 4. PRODUCTS TABLE
-- Critical Logic: If a category is deleted, products become 'Uncategorized' (NULL)
-- but are NOT deleted from the database.
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NULL, 
    name VARCHAR(20) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    description TEXT,
    image VARCHAR(40) DEFAULT 'default.jpg',
    is_active BOOLEAN DEFAULT 1, -- 1: Available, 0: Discontinued (Out of stock/Removed)
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- 5. ORDERS TABLE
-- Tracks the main order information.
-- If a user account is 'soft deleted', the user_id remains here for reporting.
-- If a user is physically deleted (rare case), user_id becomes NULL.
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL, 
    table_no VARCHAR(20) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('hazırlanıyor', 'hazır', 'teslim edildi', 'iptal') DEFAULT 'hazırlanıyor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL 
);

-- 6. ORDER ITEMS TABLE
-- Stores specific items for each order.
-- Financial Safety: Even if a product is deleted from the menu, 
-- the sales record MUST remain for accounting purposes.
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    
    FOREIGN KEY (order_id)
        REFERENCES orders(id)
        ON DELETE CASCADE,
    
    FOREIGN KEY (product_id)
        REFERENCES products(id)
        ON DELETE SET NULL
);

-- --------------------------------------------------------
-- SAMPLE DATA INSERTION
-- --------------------------------------------------------

-- Insert Categories
INSERT INTO categories (name) VALUES 
('Sıcak İçecekler'), 
('Soğuk İçecekler'), 
('Tatlılar');

-- Insert Products
INSERT INTO products (category_id, name, price, description, image) VALUES 
(1, 'Türk Kahvesi', 40.00, 'Geleneksel lezzet, çifte kavrulmuş.', 'turk_kahvesi.jpg'),
(2, 'Caramel Latte', 65.00, 'Espresso, buharlanmış süt ve karamel sosu.', 'latte.jpg'),
(2, 'Cold Brew', 75.00, '12 saat demlenmiş soğuk kahve.', 'cold_brew.jpg'),
(3, 'San Sebastian', 120.00, 'Belçika çikolatalı sos eşliğinde.', 'san_sebastian.jpg');

-- Insert Admin user
INSERT INTO users (name, surname, email, password, role) VALUES 
('admin', '', 'admin@kafe.com', '12345', 'admin'),
('Hatice Kübra', 'Ülke', 'hk@kafe.com', '4242', 'admin'),
('Merve', 'Özdoğru', 'mo@kafe.com', '0000', 'user');

-- Anonymous user for guest orders
-- This allows storing orders in the database even when the user is not logged in.
SET SQL_MODE='';
INSERT INTO users (id, name, surname, email, password, role)
VALUES (0, 'Anonymous', '', 'anonymous@kafe.com', '0000', 'user');


-- --------------------------------------------------------
-- 7. CAFE REVIEWS TABLE (GENERAL CAFE FEEDBACK)
-- This table stores general feedback/comments about the cafe.
-- If a user is deleted, user_id becomes NULL (review remains).
-- --------------------------------------------------------
CREATE TABLE cafe_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Example data
INSERT INTO cafe_reviews (user_id, rating, comment)
VALUES
(1, 5, 'Çok güzel bir kafe, tekrar gelicem.');


-- --------------------------------------------------------
-- 8. ORDER REVIEWS TABLE (ORDER-BASED PRIVATE EVALUATION)
-- This table stores rating and feedback given after an order is delivered.
-- If an order is deleted, its reviews are also deleted.
-- If a user is deleted, user_id becomes NULL (review remains).
-- --------------------------------------------------------
CREATE TABLE order_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    order_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

-- Example data
INSERT INTO order_reviews (user_id, order_id, rating, comment)
VALUES
(1, 1, 3, 'Kahve oldukça lezzetliydi. Ellerinize sağlık <3');
