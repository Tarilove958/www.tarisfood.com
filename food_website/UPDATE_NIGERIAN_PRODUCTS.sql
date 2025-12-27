-- ============================================================================
-- UPDATE EXISTING PRODUCTS TO NIGERIAN FOODS
-- Run this script to update the database with authentic Nigerian foods
-- ============================================================================

-- Update Breakfast Products
UPDATE products SET product_name = 'Jollof Rice Breakfast', description = 'Premium jollof rice infused with fresh tomatoes, spices, and perfectly cooked long-grain rice', price = 2500.00, discount_price = 2200.00, stock_quantity = 50 WHERE product_id = 1;
UPDATE products SET product_name = 'Ackee and Saltfish', description = 'Authentic prepared ackee with salted cod, fresh tomatoes, onions, and traditional spices', price = 3000.00, discount_price = 2700.00, stock_quantity = 35 WHERE product_id = 2;
UPDATE products SET product_name = 'Moi Moi with Pap', description = 'Steamed bean pudding made from fermented beans, eggs, and spices, served with creamy pap', price = 1800.00, discount_price = NULL, stock_quantity = 42 WHERE product_id = 3;
UPDATE products SET product_name = 'Waffle with Local Honey', description = 'Crispy waffle served with pure Nigerian honey and fresh sliced fruits', price = 2200.00, discount_price = 1900.00, stock_quantity = 28 WHERE product_id = 4;
UPDATE products SET product_name = 'Full Breakfast Platter', description = 'Eggs, Nigerian sausage, beans, bread, fresh fruits, and a glass of fresh juice', price = 4500.00, discount_price = 4000.00, stock_quantity = 20 WHERE product_id = 5;

-- Update Lunch Products
UPDATE products SET product_name = 'Pepper Rice with Beef', description = 'Aromatic pepper rice cooked with diced beef, bell peppers, and authentic Nigerian seasoning', price = 3500.00, discount_price = 3200.00, stock_quantity = 40 WHERE product_id = 6;
UPDATE products SET product_name = 'Chicken Fried Rice', description = 'Golden fried rice with tender chicken pieces, mixed vegetables, and aromatic spices', price = 3200.00, discount_price = NULL, stock_quantity = 38 WHERE product_id = 7;
UPDATE products SET product_name = 'Tuwo and Miyan Kuka', description = 'Traditional northern Nigerian dish of pounded rice served with rich peanut butter soup', price = 2800.00, discount_price = 2500.00, stock_quantity = 25 WHERE product_id = 8;
UPDATE products SET product_name = 'Goat Meat Suya', description = 'Grilled goat meat skewers seasoned with peanut spice mixture - authentic street style', price = 4200.00, discount_price = 3800.00, stock_quantity = 22 WHERE product_id = 9;
UPDATE products SET product_name = 'Efo Riro with Fish', description = 'Nutritious spinach and tomato stew with flaked catfish, perfectly seasoned and rich', price = 3800.00, discount_price = 3400.00, stock_quantity = 30 WHERE product_id = 10;

-- Update Dinner Products
UPDATE products SET product_name = 'Amala with Gbegiri and Ewedu', description = 'Silky smooth pounded yam served with rich bean soup and light leafy vegetable soup', price = 4500.00, discount_price = 4000.00, stock_quantity = 18 WHERE product_id = 11;
UPDATE products SET product_name = 'Jollof Rice and Grilled Chicken', description = 'Exquisite aromatic jollof rice paired with perfectly seasoned grilled free-range chicken', price = 5500.00, discount_price = NULL, stock_quantity = 12 WHERE product_id = 12;
UPDATE products SET product_name = 'Egusi Soup with Fufu', description = 'Traditional melon seed soup with beef, fish, and vegetables served with smooth cassava fufu', price = 4800.00, discount_price = 4300.00, stock_quantity = 16 WHERE product_id = 13;
UPDATE products SET product_name = 'Edikaikong with Fish and Meat', description = 'Classic cross-river dish of mixed vegetables, fish, and meat in creamy palm oil broth', price = 5200.00, discount_price = 4700.00, stock_quantity = 14 WHERE product_id = 14;
UPDATE products SET product_name = 'Afang Soup with Eba', description = 'Premium leafy soup prepared with spices, vegetables, and meat served with cassava meal', price = 4200.00, discount_price = 3800.00, stock_quantity = 20 WHERE product_id = 15;

-- Update Beverage Products
UPDATE products SET product_name = 'Fresh Zobo Drink', description = 'Refreshing hibiscus flower drink served chilled with ginger and spices', price = 800.00, discount_price = NULL, stock_quantity = 65 WHERE product_id = 16;
UPDATE products SET product_name = 'Freshly Squeezed Orange Juice', description = 'Pure orange juice from fresh local oranges with no additives or preservatives', price = 1200.00, discount_price = 1000.00, stock_quantity = 55 WHERE product_id = 17;
UPDATE products SET product_name = 'Cucumber and Ginger Smoothie', description = 'Cooling blend of fresh cucumber, ginger, lime juice, and honey - naturally refreshing', price = 1500.00, discount_price = 1300.00, stock_quantity = 40 WHERE product_id = 18;
UPDATE products SET product_name = 'Nigerian Ginger Tea', description = 'Hot brewed traditional ginger tea with turmeric and honey for wellness', price = 700.00, discount_price = NULL, stock_quantity = 75 WHERE product_id = 19;
UPDATE products SET product_name = 'Pineapple and Carrot Juice', description = 'Nutritious blend of fresh pineapple and carrot juice with natural honey', price = 1600.00, discount_price = 1400.00, stock_quantity = 35 WHERE product_id = 20;

-- Update Dessert Products
UPDATE products SET product_name = 'Chin Chin with Honey Drizzle', description = 'Crispy golden chin chin bites finished with premium local honey', price = 1500.00, discount_price = 1300.00, stock_quantity = 48 WHERE product_id = 21;
UPDATE products SET product_name = 'Puff-Puff with Sweet Sauce', description = 'Fluffy fried dough balls served with vanilla sauce - a Nigerian favorite', price = 1200.00, discount_price = NULL, stock_quantity = 52 WHERE product_id = 22;
UPDATE products SET product_name = 'Coconut Cake Slice', description = 'Moist homemade coconut cake with cream cheese frosting - locally prepared', price = 1600.00, discount_price = 1400.00, stock_quantity = 32 WHERE product_id = 23;
UPDATE products SET product_name = 'Plantain Chips and Honey', description = 'Crispy baked plantain chips dusted with cinnamon and drizzled with honey', price = 1300.00, discount_price = 1100.00, stock_quantity = 40 WHERE product_id = 24;
UPDATE products SET product_name = 'Banana Bread with Spices', description = 'Traditional banana bread with nutmeg and cinnamon flavors - freshly baked daily', price = 1400.00, discount_price = 1200.00, stock_quantity = 38 WHERE product_id = 25;

-- Update Snack Products
UPDATE products SET product_name = 'Samosa with Pepper Sauce', description = 'Crispy triangular pastry filled with potatoes, peas, and spices served with hot sauce', price = 1500.00, discount_price = 1300.00, stock_quantity = 45 WHERE product_id = 26;
UPDATE products SET product_name = 'Spring Rolls with Sweet Sauce', description = 'Golden fried spring rolls filled with vegetables and served with honey sauce', price = 1800.00, discount_price = 1600.00, stock_quantity = 38 WHERE product_id = 27;
UPDATE products SET product_name = 'Yam Fries with Pepper Mix', description = 'Crispy fried yam strips dusted with spiced seasoning - a healthy alternative', price = 1200.00, discount_price = NULL, stock_quantity = 50 WHERE product_id = 28;
UPDATE products SET product_name = 'Meat Pie with Gravy', description = 'Flaky pastry filled with spiced minced beef served warm with savory gravy', price = 1600.00, discount_price = 1400.00, stock_quantity = 35 WHERE product_id = 29;
UPDATE products SET product_name = 'Fish Cake with Lemon', description = 'Authentic fried fish cake served with fresh lemon wedge and hot pepper sauce', price = 1400.00, discount_price = 1200.00, stock_quantity = 42 WHERE product_id = 30;

-- ============================================================================
-- SUMMARY OF CHANGES
-- ============================================================================
-- All 30 products have been updated with authentic Nigerian foods:
--
-- Breakfast (5): Jollof Rice, Ackee & Saltfish, Moi Moi, Waffle with Honey, Full Breakfast
-- Lunch (5): Pepper Rice, Fried Rice, Tuwo & Kuka, Suya, Efo Riro
-- Dinner (5): Amala, Jollof & Chicken, Egusi Soup, Edikaikong, Afang Soup
-- Beverages (5): Zobo, Orange Juice, Cucumber Smoothie, Ginger Tea, Pineapple-Carrot Juice
-- Desserts (5): Chin Chin, Puff-Puff, Coconut Cake, Plantain Chips, Banana Bread
-- Snacks (5): Samosa, Spring Rolls, Yam Fries, Meat Pie, Fish Cake
--
-- All prices are in Nigerian Naira (₦)
-- Professional descriptions highlight traditional preparation and ingredients
-- Stock quantities adjusted for realistic inventory management
-- ============================================================================
