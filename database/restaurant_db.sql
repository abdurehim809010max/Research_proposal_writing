-- ============================================
-- Restaurant Management System Database
-- Ethiopian-themed Restaurant: "Habesha Kitchen"
-- ============================================

DROP DATABASE IF EXISTS restaurant_db;
CREATE DATABASE restaurant_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE restaurant_db;

-- ============================================
-- Table 1: users
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    address TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Table 2: categories
-- ============================================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    image VARCHAR(255) DEFAULT 'default_category.jpg',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Table 3: menu_items
-- ============================================
CREATE TABLE menu_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    image VARCHAR(255) DEFAULT 'default_food.jpg',
    is_available TINYINT(1) DEFAULT 1,
    is_featured TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Table 4: orders
-- ============================================
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL DEFAULT 0,
    status ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending',
    order_type ENUM('dine-in', 'takeaway', 'delivery') DEFAULT 'dine-in',
    delivery_address TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Table 5: order_items (many-to-many bridge)
-- ============================================
CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    menu_item_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Table 6: reservations
-- ============================================
CREATE TABLE reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    reservation_date DATE NOT NULL,
    reservation_time TIME NOT NULL,
    guests INT NOT NULL DEFAULT 1,
    special_requests TEXT,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================
-- Table 7: contact_messages
-- ============================================
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================
-- Sample Data
-- ============================================

-- Admin user (password: Admin@123)
INSERT INTO users (full_name, email, phone, password, role, address) VALUES
('Admin User', 'admin@habesha.com', '+251911000000', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Addis Ababa, Bole'),
('Abebe Kebede', 'abebe@example.com', '+251912345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Addis Ababa, Piassa'),
('Tigist Haile', 'tigist@example.com', '+251923456789', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Addis Ababa, Megenagna'),
('Dawit Tesfaye', 'dawit@example.com', '+251934567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Hawassa, Main Road'),
('Sara Mohammed', 'sara@example.com', '+251945678901', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user', 'Dire Dawa, Kezira');

-- Categories
INSERT INTO categories (name, description, image) VALUES
('Traditional Ethiopian', 'Authentic Ethiopian dishes served with injera', 'traditional.jpg'),
('Grilled & Roasted', 'Grilled meats and roasted specialties', 'grilled.jpg'),
('Vegetarian/Fasting', 'Delicious fasting and vegetarian options', 'vegetarian.jpg'),
('Beverages', 'Traditional and modern drinks', 'beverages.jpg'),
('Desserts & Snacks', 'Sweet treats and light bites', 'desserts.jpg'),
('Breakfast', 'Morning meals and traditional breakfast items', 'breakfast.jpg');

-- Menu Items
INSERT INTO menu_items (category_id, name, description, price, image, is_available, is_featured) VALUES
-- Traditional Ethiopian
(1, 'Doro Wot', 'Spicy chicken stew with hard-boiled eggs, served with injera', 350.00, 'doro_wot.jpg', 1, 1),
(1, 'Kitfo', 'Ethiopian steak tartare with mitmita spice and herb butter', 400.00, 'kitfo.jpg', 1, 1),
(1, 'Tibs', 'Sauteed meat with vegetables and Ethiopian spices', 300.00, 'tibs.jpg', 1, 0),
(1, 'Zilzil Tibs', 'Strips of beef sauteed with onions, peppers and rosemary', 320.00, 'zilzil_tibs.jpg', 1, 0),
(1, 'Key Wot', 'Spicy beef stew in berbere sauce', 280.00, 'key_wot.jpg', 1, 0),
-- Grilled & Roasted
(2, 'Derek Tibs', 'Dry-fried cubed beef with jalapenos and onions', 350.00, 'derek_tibs.jpg', 1, 1),
(2, 'Grilled Fish', 'Fresh Nile tilapia grilled with lemon and herbs', 380.00, 'grilled_fish.jpg', 1, 0),
(2, 'Lamb Chops', 'Tender lamb chops marinated in Ethiopian spices', 450.00, 'lamb_chops.jpg', 1, 1),
-- Vegetarian/Fasting
(3, 'Shiro Wot', 'Chickpea flour stew with onions and berbere', 180.00, 'shiro.jpg', 1, 1),
(3, 'Misir Wot', 'Red lentil stew cooked with berbere spice', 170.00, 'misir_wot.jpg', 1, 0),
(3, 'Gomen', 'Collard greens sauteed with garlic and ginger', 150.00, 'gomen.jpg', 1, 0),
(3, 'Beyaynetu', 'Fasting platter with assorted vegetable dishes', 250.00, 'beyaynetu.jpg', 1, 1),
(3, 'Atkilt Wot', 'Cabbage, potato, and carrot stew', 160.00, 'atkilt.jpg', 1, 0),
-- Beverages
(4, 'Ethiopian Coffee (Buna)', 'Traditional Ethiopian coffee ceremony', 80.00, 'buna.jpg', 1, 1),
(4, 'Tej', 'Ethiopian honey wine', 120.00, 'tej.jpg', 1, 0),
(4, 'Fresh Juice Mix', 'Layered fresh fruit juices (spris)', 100.00, 'juice.jpg', 1, 1),
(4, 'Tella', 'Traditional Ethiopian homebrew beer', 60.00, 'tella.jpg', 1, 0),
-- Desserts & Snacks
(5, 'Sambusa', 'Crispy pastry filled with lentils or meat', 80.00, 'sambusa.jpg', 1, 0),
(5, 'Kategna', 'Toasted injera with berbere and butter', 90.00, 'kategna.jpg', 1, 0),
(5, 'Honey Cake', 'Traditional Ethiopian honey cake (Yemariam Dabo)', 120.00, 'honey_cake.jpg', 1, 0),
-- Breakfast
(6, 'Firfir', 'Shredded injera mixed with berbere sauce and butter', 150.00, 'firfir.jpg', 1, 1),
(6, 'Kinche', 'Cracked wheat porridge with butter', 100.00, 'kinche.jpg', 1, 0),
(6, 'Chechebsa', 'Shredded flatbread with spiced butter and berbere', 130.00, 'chechebsa.jpg', 1, 0),
(6, 'Foul', 'Spiced fava beans with olive oil and vegetables', 120.00, 'foul.jpg', 1, 0);

-- Sample Orders
INSERT INTO orders (user_id, total_amount, status, order_type, notes) VALUES
(2, 730.00, 'delivered', 'dine-in', 'Extra injera please'),
(3, 550.00, 'confirmed', 'takeaway', NULL),
(4, 400.00, 'preparing', 'delivery', 'Ring the bell twice'),
(2, 350.00, 'pending', 'dine-in', NULL),
(5, 280.00, 'delivered', 'dine-in', 'No spice');

-- Sample Order Items
INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price, subtotal) VALUES
(1, 1, 1, 350.00, 350.00),
(1, 9, 1, 180.00, 180.00),
(1, 16, 2, 100.00, 200.00),
(2, 2, 1, 400.00, 400.00),
(2, 11, 1, 150.00, 150.00),
(3, 2, 1, 400.00, 400.00),
(4, 1, 1, 350.00, 350.00),
(5, 5, 1, 280.00, 280.00);

-- Sample Reservations
INSERT INTO reservations (user_id, reservation_date, reservation_time, guests, special_requests, status) VALUES
(2, '2026-05-15', '19:00:00', 4, 'Birthday celebration, need a cake', 'confirmed'),
(3, '2026-05-16', '12:30:00', 2, NULL, 'pending'),
(4, '2026-05-14', '18:00:00', 6, 'Quiet corner please', 'confirmed'),
(5, '2026-05-17', '20:00:00', 3, 'Vegetarian only', 'pending');

-- Sample Contact Messages
INSERT INTO contact_messages (name, email, subject, message, is_read) VALUES
('Yohannes Gebre', 'yohannes@example.com', 'Catering Inquiry', 'I would like to inquire about catering services for 50 people. Please send me a quote.', 0),
('Helen Tadesse', 'helen@example.com', 'Great Experience', 'Thank you for the wonderful dining experience last Saturday. The Doro Wot was amazing!', 1),
('Mohammed Ali', 'mohammed@example.com', 'Event Booking', 'Can I book your restaurant for a corporate event on June 5th?', 0);
