-- CREATE DATABASE IF NOT EXISTS simple_shop_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE simple_shop_db;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_session_id VARCHAR(255) NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_session_id VARCHAR(255) NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_details TEXT,
    status VARCHAR(50) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    quantity INT NOT NULL,
    price_at_order DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
);

INSERT INTO products (name, description, price, image_url) VALUES
('Кружка "Кодер"', 'Идеальная кружка для долгих ночей кодинга.', 15.99, 'https://via.placeholder.com/150/0000FF/FFFFFF?text=Mug'),
('Футболка "Баг"', 'Не баг, а фича! Отличная футболка.', 25.50, 'https://via.placeholder.com/150/FF0000/FFFFFF?text=T-Shirt'),
('Стикер "SQLi"', 'Наклейка для ноутбука настоящего хакера.', 5.00, 'https://via.placeholder.com/150/00FF00/000000?text=Sticker');
