-- ============================================================================
-- FOOD WEBSITE - COMPLETE DATABASE SCHEMA & SAMPLE DATA
-- ============================================================================
-- Database: food_website
-- Character Set: utf8mb4 (supports emojis and special characters)
-- Engine: InnoDB (supports transactions and foreign keys)
-- ============================================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS food_website CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE food_website;

-- ============================================================================
-- TABLE 1: USERS (Customers and Admins)
-- ============================================================================
CREATE TABLE users (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    address TEXT,
    city VARCHAR(50),
    state VARCHAR(50),
    user_type ENUM('customer', 'admin') DEFAULT 'customer',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_user_type (user_type),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 2: CATEGORIES
-- ============================================================================
CREATE TABLE categories (
    category_id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_order (display_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 3: PRODUCTS
-- ============================================================================
CREATE TABLE products (
    product_id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2) DEFAULT NULL,
    image VARCHAR(255),
    stock_quantity INT DEFAULT 0,
    is_featured BOOLEAN DEFAULT FALSE,
    rating DECIMAL(3, 2) DEFAULT 0,
    status ENUM('available', 'unavailable', 'out_of_stock') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE,
    INDEX idx_category (category_id),
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_price (price)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 5: ORDERS
-- ============================================================================
CREATE TABLE orders (
    order_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL DEFAULT 0,
    tax DECIMAL(10, 2) NOT NULL DEFAULT 0,
    delivery_fee DECIMAL(10, 2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_reference VARCHAR(100),
    order_status ENUM('pending', 'confirmed', 'processing', 'out_for_delivery', 'delivered', 'cancelled') DEFAULT 'pending',
    delivery_address TEXT NOT NULL,
    delivery_city VARCHAR(50),
    delivery_state VARCHAR(50),
    delivery_phone VARCHAR(20),
    delivery_notes TEXT,
    special_instructions TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expected_delivery_date DATE,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_order_number (order_number),
    INDEX idx_payment_status (payment_status),
    INDEX idx_order_status (order_status),
    INDEX idx_order_date (order_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 6: ORDER_ITEMS
-- ============================================================================
CREATE TABLE order_items (
    order_item_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(150) NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Cart Table (Session-based or User-based)
CREATE TABLE cart (
    cart_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    session_id VARCHAR(100),
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_session (session_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 7: TESTIMONIALS
-- ============================================================================
CREATE TABLE testimonials (
    testimonial_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT DEFAULT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100),
    rating INT CHECK (rating BETWEEN 1 AND 5),
    testimonial_text TEXT NOT NULL,
    image VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    is_featured BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_featured (is_featured),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 8: CONTACT_MESSAGES
-- ============================================================================
CREATE TABLE contact_messages (
    message_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    response_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_email (email),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 9: SITE_SETTINGS
-- ============================================================================
CREATE TABLE site_settings (
    setting_id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_key (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- TABLE 10: PAYMENT_TRANSACTIONS
-- ============================================================================
CREATE TABLE payment_transactions (
    transaction_id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    payment_reference VARCHAR(100) UNIQUE NOT NULL,
    payment_gateway VARCHAR(50) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    currency VARCHAR(10) DEFAULT 'NGN',
    status ENUM('pending', 'success', 'failed') DEFAULT 'pending',
    transaction_data LONGTEXT,
    response_code VARCHAR(50),
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(order_id) ON DELETE CASCADE,
    INDEX idx_reference (payment_reference),
    INDEX idx_order (order_id),
    INDEX idx_status (status),
    INDEX idx_gateway (payment_gateway),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin User (password: admin123 - bcrypt hash with cost 10)
INSERT INTO users (full_name, email, phone, password, user_type, status) VALUES
('Admin User', 'admin@foodwebsite.com', '+234-XXX-XXX-XXXX', '$2y$10$5qJlRLsEi.C8NLYlfQYrCOL5LAVzg8UvdEoM9KvhPyI.U/d3GYKa6', 'admin', 'active');

-- ============================================================================
-- INSERT DATA: SAMPLE CATEGORIES
-- ============================================================================
INSERT INTO categories (category_name, description, status, display_order) VALUES
('Breakfast', '🌅 Start your day right with our delicious breakfast options - fresh pastries, omelets, and more!', 'active', 1),
('Lunch', '🥗 Satisfying lunch meals for your midday cravings - sandwiches, salads, and hearty portions', 'active', 2),
('Dinner', '🍽️ End your day with our exquisite dinner selections - gourmet dishes cooked to perfection', 'active', 3),
('Beverages', '☕ Refreshing drinks to complement your meal - coffee, juice, smoothies, and more', 'active', 4),
('Desserts', '🍰 Sweet treats to satisfy your sweet tooth - cakes, pastries, and creamy delights', 'active', 5),
('Snacks', '🍕 Quick bites and light snacks - perfect for when you need something delicious', 'active', 6);

-- ============================================================================
-- INSERT DATA: SAMPLE PRODUCTS (Breakfast)
-- ============================================================================
INSERT INTO products (category_id, product_name, description, price, discount_price, stock_quantity, is_featured, status) VALUES
(1, 'Jollof Rice Breakfast', 'Premium jollof rice infused with fresh tomatoes, spices, and perfectly cooked long-grain rice', 2500.00, 2200.00, 50, TRUE, 'available'),
(1, 'Ackee and Saltfish', 'Authentic prepared ackee with salted cod, fresh tomatoes, onions, and traditional spices', 3000.00, 2700.00, 35, TRUE, 'available'),
(1, 'Moi Moi with Pap', 'Steamed bean pudding made from fermented beans, eggs, and spices, served with creamy pap', 1800.00, NULL, 42, FALSE, 'available'),
(1, 'Waffle with Local Honey', 'Crispy waffle served with pure Nigerian honey and fresh sliced fruits', 2200.00, 1900.00, 28, FALSE, 'available'),
(1, 'Full Breakfast Platter', 'Eggs, Nigerian sausage, beans, bread, fresh fruits, and a glass of fresh juice', 4500.00, 4000.00, 20, TRUE, 'available');

-- ============================================================================
-- INSERT DATA: SAMPLE PRODUCTS (Lunch)
-- ============================================================================
INSERT INTO products (category_id, product_name, description, price, discount_price, stock_quantity, is_featured, status) VALUES
(2, 'Pepper Rice with Beef', 'Aromatic pepper rice cooked with diced beef, bell peppers, and authentic Nigerian seasoning', 3500.00, 3200.00, 40, TRUE, 'available'),
(2, 'Chicken Fried Rice', 'Golden fried rice with tender chicken pieces, mixed vegetables, and aromatic spices', 3200.00, NULL, 38, FALSE, 'available'),
(2, 'Tuwo and Miyan Kuka', 'Traditional northern Nigerian dish of pounded rice served with rich peanut butter soup', 2800.00, 2500.00, 25, FALSE, 'available'),
(2, 'Goat Meat Suya', 'Grilled goat meat skewers seasoned with peanut spice mixture - authentic street style', 4200.00, 3800.00, 22, FALSE, 'available'),
(2, 'Efo Riro with Fish', 'Nutritious spinach and tomato stew with flaked catfish, perfectly seasoned and rich', 3800.00, 3400.00, 30, TRUE, 'available');

-- ============================================================================
-- INSERT DATA: SAMPLE PRODUCTS (Dinner)
-- ============================================================================
INSERT INTO products (category_id, product_name, description, price, discount_price, stock_quantity, is_featured, status) VALUES
(3, 'Amala with Gbegiri and Ewedu', 'Silky smooth pounded yam served with rich bean soup and light leafy vegetable soup', 4500.00, 4000.00, 18, TRUE, 'available'),
(3, 'Jollof Rice and Grilled Chicken', 'Exquisite aromatic jollof rice paired with perfectly seasoned grilled free-range chicken', 5500.00, NULL, 12, TRUE, 'available'),
(3, 'Egusi Soup with Fufu', 'Traditional melon seed soup with beef, fish, and vegetables served with smooth cassava fufu', 4800.00, 4300.00, 16, FALSE, 'available'),
(3, 'Edikaikong with Fish and Meat', 'Classic cross-river dish of mixed vegetables, fish, and meat in creamy palm oil broth', 5200.00, 4700.00, 14, FALSE, 'available'),
(3, 'Afang Soup with Eba', 'Premium leafy soup prepared with spices, vegetables, and meat served with cassava meal', 4200.00, 3800.00, 20, FALSE, 'available');

-- ============================================================================
-- INSERT DATA: SAMPLE PRODUCTS (Beverages)
-- ============================================================================
INSERT INTO products (category_id, product_name, description, price, discount_price, stock_quantity, is_featured, status) VALUES
(4, 'Fresh Zobo Drink', 'Refreshing hibiscus flower drink served chilled with ginger and spices', 800.00, NULL, 65, FALSE, 'available'),
(4, 'Freshly Squeezed Orange Juice', 'Pure orange juice from fresh local oranges with no additives or preservatives', 1200.00, 1000.00, 55, TRUE, 'available'),
(4, 'Cucumber and Ginger Smoothie', 'Cooling blend of fresh cucumber, ginger, lime juice, and honey - naturally refreshing', 1500.00, 1300.00, 40, FALSE, 'available'),
(4, 'Nigerian Ginger Tea', 'Hot brewed traditional ginger tea with turmeric and honey for wellness', 700.00, NULL, 75, FALSE, 'available'),
(4, 'Pineapple and Carrot Juice', 'Nutritious blend of fresh pineapple and carrot juice with natural honey', 1600.00, 1400.00, 35, TRUE, 'available');

-- ============================================================================
-- INSERT DATA: SAMPLE PRODUCTS (Desserts)
-- ============================================================================
INSERT INTO products (category_id, product_name, description, price, discount_price, stock_quantity, is_featured, status) VALUES
(5, 'Chin Chin with Honey Drizzle', 'Crispy golden chin chin bites finished with premium local honey', 1500.00, 1300.00, 48, TRUE, 'available'),
(5, 'Puff-Puff with Sweet Sauce', 'Fluffy fried dough balls served with vanilla sauce - a Nigerian favorite', 1200.00, NULL, 52, FALSE, 'available'),
(5, 'Coconut Cake Slice', 'Moist homemade coconut cake with cream cheese frosting - locally prepared', 1600.00, 1400.00, 32, FALSE, 'available'),
(5, 'Plantain Chips and Honey', 'Crispy baked plantain chips dusted with cinnamon and drizzled with honey', 1300.00, 1100.00, 40, FALSE, 'available'),
(5, 'Banana Bread with Spices', 'Traditional banana bread with nutmeg and cinnamon flavors - freshly baked daily', 1400.00, 1200.00, 38, TRUE, 'available');

-- ============================================================================
-- INSERT DATA: SAMPLE PRODUCTS (Snacks)
-- ============================================================================
INSERT INTO products (category_id, product_name, description, price, discount_price, stock_quantity, is_featured, status) VALUES
(6, 'Samosa with Pepper Sauce', 'Crispy triangular pastry filled with potatoes, peas, and spices served with hot sauce', 1500.00, 1300.00, 45, TRUE, 'available'),
(6, 'Spring Rolls with Sweet Sauce', 'Golden fried spring rolls filled with vegetables and served with honey sauce', 1800.00, 1600.00, 38, FALSE, 'available'),
(6, 'Yam Fries with Pepper Mix', 'Crispy fried yam strips dusted with spiced seasoning - a healthy alternative', 1200.00, NULL, 50, FALSE, 'available'),
(6, 'Meat Pie with Gravy', 'Flaky pastry filled with spiced minced beef served warm with savory gravy', 1600.00, 1400.00, 35, FALSE, 'available'),
(6, 'Fish Cake with Lemon', 'Authentic fried fish cake served with fresh lemon wedge and hot pepper sauce', 1400.00, 1200.00, 42, TRUE, 'available');

-- ============================================================================
-- INSERT DATA: SAMPLE TESTIMONIALS
-- ============================================================================
INSERT INTO testimonials (customer_name, customer_email, rating, testimonial_text, status, is_featured) VALUES
('John Doe', 'john@example.com', 5, 'Best food I have ever tasted! The quality and freshness are unmatched. I recommend it to everyone!', 'approved', TRUE),
('Sarah Johnson', 'sarah@example.com', 5, 'Amazing service and delicious meals. The delivery was prompt and the food arrived hot and fresh. Highly recommended!', 'approved', TRUE),
('Michael Brown', 'michael@example.com', 4, 'Great food and reasonable prices. The only issue was the delivery took a bit longer than expected, but overall very satisfied.', 'approved', FALSE),
('Emma Wilson', 'emma@example.com', 5, 'I have been ordering from here for months now. The consistency in quality is amazing. Keep up the excellent work!', 'approved', TRUE),
('David Lee', 'david@example.com', 4, 'Good food and nice presentation. Would love to see more vegetarian options though.', 'approved', FALSE);

-- ============================================================================
-- INSERT DATA: SITE SETTINGS
-- ============================================================================
INSERT INTO site_settings (setting_key, setting_value, setting_type) VALUES
('site_name', 'FoodBrand - Your Favorite Food Delivery Service', 'text'),
('site_tagline', 'Delicious meals delivered to your door', 'text'),
('site_email', 'info@foodbrand.com', 'text'),
('site_phone', '+234-XXX-XXX-XXXX', 'text'),
('site_address', 'Your Restaurant Address Here, Lagos, Nigeria', 'text'),
('site_url', 'http://localhost/food_website', 'text'),
('currency', 'NGN', 'text'),
('currency_symbol', '₦', 'text'),
('delivery_fee', '500', 'number'),
('free_delivery_threshold', '10000', 'number'),
('tax_rate', '0.05', 'number'),
('min_order_amount', '1000', 'number'),
('business_hours_open', '09:00', 'text'),
('business_hours_close', '22:00', 'text'),
('paystack_public_key', 'pk_test_your_public_key_here', 'text'),
('paystack_secret_key', 'sk_test_your_secret_key_here', 'text'),
('flutterwave_public_key', 'FLWPUBK_TEST_your_key_here', 'text'),
('flutterwave_secret_key', 'FLWSECK_TEST_your_key_here', 'text'),
('google_analytics_id', '', 'text'),
('facebook_pixel_id', '', 'text'),
('smtp_host', 'smtp.gmail.com', 'text'),
('smtp_port', '587', 'number'),
('smtp_username', 'your_email@gmail.com', 'text'),
('smtp_password', '', 'text'),
('max_upload_size_mb', '5', 'number'),
('allowed_image_types', 'jpg,jpeg,png,gif', 'text'),
('timezone', 'Africa/Lagos', 'text'),
('date_format', 'Y-m-d', 'text'),
('time_format', 'H:i:s', 'text');

-- ============================================================================
-- TABLE 11: ACTIVITY_LOGS (Admin Action Tracking)
-- ============================================================================
CREATE TABLE activity_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- FINAL NOTES & INSTRUCTIONS
-- ============================================================================
-- 1. Admin Login Credentials:
--    Email: admin@foodwebsite.com
--    Password: admin123
--
-- 2. Sample Products: 24 products across 6 categories
--    - Breakfast: 5 products
--    - Lunch: 5 products
--    - Dinner: 5 products
--    - Beverages: 5 products
--    - Desserts: 5 products
--    - Snacks: 5 products
--
-- 3. Featured Products: 12 featured items across all categories
--
-- 4. Sample Testimonials: 5 approved testimonials (3 featured)
--
-- 5. Complete Site Settings: 26 configuration options
--
-- 6. Tables Included:
--    - users (Admin + Customer accounts)
--    - categories (Product categories)
--    - products (Food items with pricing & stock)
--    - cart (Shopping cart items)
--    - orders (Customer orders)
--    - order_items (Items in each order)
--    - testimonials (Customer reviews & ratings)
--    - contact_messages (Contact form submissions)
--    - site_settings (Configuration & API keys)
--    - payment_transactions (Payment records)
--    - activity_logs (Admin action tracking)
--
-- 7. All foreign key relationships are properly configured for data integrity
--
-- 8. Indexes are created for optimal query performance on frequently searched fields
--
-- 9. Character set is utf8mb4 to support emojis and international characters
--
-- 10. Engine is InnoDB for transaction support and foreign key constraints
--
-- ============================================================================
-- END OF DATABASE SETUP
-- ============================================================================