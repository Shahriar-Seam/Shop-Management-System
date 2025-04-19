-- Sample Data for Shop Management System (Khulna, Bangladesh)
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Company Data (25 records) - MUST BE INSERTED FIRST
-- -----------------------------------------------------
INSERT INTO Company (company_name, company_phone_number, company_address) VALUES
('Tech Solutions BD', '01711-123456', '123 KDA Avenue, Khulna, Bangladesh'),
('Global Supplies Khulna', '01812-234567', '456 Sonadanga R/A, Khulna, Bangladesh'),
('Eco Products Bangladesh', '01913-345678', '789 Green Road, Khalishpur, Khulna, Bangladesh'),
('Modern Furniture Khulna', '01614-456789', '101 Boyra Main Road, Khulna, Bangladesh'),
('Fresh Foods Bazar', '01515-567890', '202 New Market, Khulna, Bangladesh'),
('Fashion House BD', '01716-678901', '303 Dak Bangla Road, Khulna, Bangladesh'),
('Home Essentials Khulna', '01817-789012', '404 Gallamari, Khulna, Bangladesh'),
('Sports Zone Bangladesh', '01918-890123', '505 Khan Jahan Ali Road, Khulna, Bangladesh'),
('Electronics Hub Khulna', '01619-901234', '606 Rupsha Stand Road, Khulna, Bangladesh'),
('Office Supplies BD', '01520-012345', '707 Shib Bari Crossing, Khulna, Bangladesh'),
('Beauty World Khulna', '01721-123789', '808 Daulatpur, Khulna, Bangladesh'),
('Pet Care Bangladesh', '01822-234890', '909 Fulbari Gate, Khulna, Bangladesh'),
('Garden World Khulna', '01923-345901', '110 Gollamari, Khulna, Bangladesh'),
('Auto Parts Bangladesh', '01624-456012', '211 Shiromoni, Khulna, Bangladesh'),
('Toy House Khulna', '01525-567123', '312 Lower Jessore Road, Khulna, Bangladesh'),
('Book Corner BD', '01726-678234', '413 Khulna University Road, Khulna, Bangladesh'),
('Music World Bangladesh', '01827-789345', '514 Nirala R/A, Khulna, Bangladesh'),
('Craft House Khulna', '01928-890456', '615 Khalishpur Industrial Area, Khulna, Bangladesh'),
('Jewelry Palace BD', '01629-901567', '716 Royal More, Khulna, Bangladesh'),
('Health Care Khulna', '01530-012678', '817 Tutpara Central Road, Khulna, Bangladesh'),
('Kitchen Bazar BD', '01731-123789', '918 Moilapota, Khulna, Bangladesh'),
('Outdoor Shop Khulna', '01832-234890', '019 Labanchara, Khulna, Bangladesh'),
('Baby Care Bangladesh', '01933-345901', '120 Boikali, Khulna, Bangladesh'),
('Tech Gadgets Khulna', '01634-456012', '221 Rupsha Ferry Ghat Road, Khulna, Bangladesh'),
('Gourmet Foods BD', '01535-567123', '322 Goalkhali, Khulna, Bangladesh');

-- AFTER Company data is inserted, THEN insert Salesperson data
-- -----------------------------------------------------
-- Salesperson Data (25 records - one active per company)
-- -----------------------------------------------------
INSERT INTO Salesperson (name, phone, status, archived_on, company_id) VALUES
('Rahim Khan', '01711-222333', 'Active', NULL, 1),
('Fatima Begum', '01812-333444', 'Active', NULL, 2),
('Kamal Hossain', '01913-444555', 'Active', NULL, 3),
('Nusrat Jahan', '01614-555666', 'Active', NULL, 4),
('Masud Ahmed', '01515-666777', 'Active', NULL, 5),
('Sabina Yasmin', '01716-777888', 'Active', NULL, 6),
('Rafiq Islam', '01817-888999', 'Active', NULL, 7),
('Jasmine Akter', '01918-999000', 'Active', NULL, 8),
('Wahid Rahman', '01619-000111', 'Active', NULL, 9),
('Laila Khatun', '01520-111222', 'Active', NULL, 10),
('Jamal Uddin', '01721-333111', 'Active', NULL, 11),
('Amina Haque', '01822-444222', 'Active', NULL, 12),
('Delwar Hossain', '01923-555333', 'Active', NULL, 13),
('Maliha Rahman', '01624-666444', 'Active', NULL, 14),
('Chowdhury Karim', '01525-777555', 'Active', NULL, 15),
('Asma Begum', '01726-888666', 'Active', NULL, 16),
('Mahbub Alam', '01827-999777', 'Active', NULL, 17),
('Farzana Islam', '01928-000888', 'Active', NULL, 18),
('Anwar Hossain', '01629-111999', 'Active', NULL, 19),
('Shahana Parveen', '01530-222000', 'Active', NULL, 20),
('Kamrul Hassan', '01731-444111', 'Active', NULL, 21),
('Nasreen Sultana', '01832-555222', 'Active', NULL, 22),
('Belal Ahmed', '01933-666333', 'Active', NULL, 23),
('Rabeya Khatun', '01634-777444', 'Active', NULL, 24),
('Shahadat Hossain', '01535-888555', 'Active', NULL, 25);

-- Add some archived salespersons for a few companies
INSERT INTO Salesperson (name, phone, status, archived_on, company_id) VALUES
('Tarek Rahman', '01711-999111', 'Archived', '2023-01-15 10:30:00', 1),
('Lubna Akter', '01812-888222', 'Archived', '2023-02-20 14:45:00', 2),
('Monir Khan', '01913-777333', 'Archived', '2023-03-10 09:15:00', 3),
('Khadija Begum', '01614-666444', 'Archived', '2023-04-05 16:20:00', 4),
('Polash Miah', '01515-555555', 'Archived', '2023-05-12 11:30:00', 5);

-- -----------------------------------------------------
-- Customer Data (25 records - for debt tracking only)
-- -----------------------------------------------------
INSERT INTO Customer (name, contact_info) VALUES
('Abdul Karim', 'abdul.k@gmail.com, 01711-123456'),
('Maria Akter', 'maria.a@gmail.com, 01812-234567'),
('Rahim Miah', 'rahim.m@gmail.com, 01913-345678'),
('Sadia Khatun', 'sadia.k@gmail.com, 01614-456789'),
('Jahangir Alam', 'jahangir.a@gmail.com, 01515-567890'),
('Liza Rahman', 'liza.r@gmail.com, 01716-678901'),
('Tanvir Hossain', 'tanvir.h@gmail.com, 01817-789012'),
('Parvin Akter', 'parvin.a@gmail.com, 01918-890123'),
('Mizan Khan', 'mizan.k@gmail.com, 01619-901234'),
('Esrat Jahan', 'esrat.j@gmail.com, 01520-012345'),
('Didar Hossain', 'didar.h@gmail.com, 01721-123789'),
('Jannatul Ferdous', 'jannatul.f@gmail.com, 01822-234890'),
('Chandan Kumar', 'chandan.k@gmail.com, 01923-345901'),
('Mahmuda Begum', 'mahmuda.b@gmail.com, 01624-456012'),
('Jahid Islam', 'jahid.i@gmail.com, 01525-567123'),
('Sumaiya Akter', 'sumaiya.a@gmail.com, 01726-678234'),
('Emon Ali', 'emon.a@gmail.com, 01827-789345'),
('Nargis Akter', 'nargis.a@gmail.com, 01928-890456'),
('Golam Mostafa', 'golam.m@gmail.com, 01629-901567'),
('Kona Begum', 'kona.b@gmail.com, 01530-012678'),
('Riaz Uddin', 'riaz.u@gmail.com, 01731-123890'),
('Dalia Khatun', 'dalia.k@gmail.com, 01832-234901'),
('Kader Miah', 'kader.m@gmail.com, 01933-345012'),
('Lima Akter', 'lima.a@gmail.com, 01634-456123'),
('Sohel Rana', 'sohel.r@gmail.com, 01535-567234');

-- -----------------------------------------------------
-- Product Data (25 records)
-- -----------------------------------------------------
INSERT INTO Product (name, company_id, stock_quantity, unit_price, restock_threshold) VALUES
('Walton Laptop Pro', 1, 50, 65000.00, 10),
('Office Paper Pack', 2, 200, 350.00, 50),
('Eco-Friendly Water Bottle', 3, 150, 850.00, 30),
('Modern Office Chair', 4, 30, 9500.00, 5),
('Organic Hilsha Fish', 5, 500, 150.00, 100),
('Deshi Cotton T-Shirt', 6, 100, 1800.00, 20),
('Kitchen Towel Set', 7, 80, 650.00, 15),
('Professional Cricket Bat', 8, 60, 2200.00, 12),
('Wireless Headphones', 9, 75, 5500.00, 15),
('Stapler Set', 10, 120, 600.00, 25),
('Facial Cleanser', 11, 90, 950.00, 20),
('Premium Cat Food', 12, 110, 2400.00, 25),
('Garden Trowel', 13, 70, 550.00, 15),
('Motorcycle Brake Pads', 14, 85, 3500.00, 20),
('Educational Board Game', 15, 65, 1400.00, 15),
('Bengali Novel', 16, 95, 1100.00, 20),
('Acoustic Guitar', 17, 25, 15000.00, 5),
('Nakshi Kantha Kit', 18, 55, 1800.00, 10),
('Silver Necklace', 19, 40, 6500.00, 8),
('Vitamin Supplement', 20, 130, 1600.00, 30),
('Non-Stick Frying Pan', 21, 60, 2800.00, 12),
('Hiking Backpack', 22, 45, 4200.00, 10),
('Baby Onesie Set', 23, 75, 1200.00, 15),
('Bluetooth Speaker', 24, 55, 3500.00, 10),
('Sundarbans Honey', 25, 100, 900.00, 20);

-- -----------------------------------------------------
-- ProductOrder Data (25 records)
-- -----------------------------------------------------
INSERT INTO ProductOrder (company_id, order_time) VALUES
(1, '2023-01-15 10:00:00'),
(2, '2023-02-20 14:30:00'),
(3, '2023-03-10 09:15:00'),
(4, '2023-03-15 11:45:00'),
(5, '2023-03-20 16:00:00'),
(6, '2023-03-25 10:30:00'),
(7, '2023-04-01 14:15:00'),
(8, '2023-04-05 09:45:00'),
(9, '2023-04-10 13:30:00'),
(10, '2023-04-15 11:00:00'),
(11, '2023-04-20 15:45:00'),
(12, '2023-04-25 10:15:00'),
(13, '2023-05-01 14:30:00'),
(14, '2023-05-05 09:00:00'),
(15, '2023-05-10 13:15:00'),
(16, '2023-05-15 11:30:00'),
(17, '2023-05-20 15:00:00'),
(18, '2023-05-25 10:45:00'),
(19, '2023-06-01 14:00:00'),
(20, '2023-06-05 09:30:00'),
(21, '2023-06-10 13:45:00'),
(22, '2023-06-15 11:15:00'),
(23, '2023-06-20 15:30:00'),
(24, '2023-06-25 10:00:00'),
(25, '2023-06-30 14:45:00');

-- -----------------------------------------------------
-- OrderDetail Data (multiple records per order)
-- -----------------------------------------------------
INSERT INTO OrderDetail (order_id, product_id, quantity, cost) VALUES
-- Order 1 details
(1, 1, 5, 325000.00),
-- Order 2 details
(2, 2, 100, 35000.00),
-- Order 3 details
(3, 3, 50, 42500.00),
-- Order 4 details
(4, 4, 5, 47500.00),
-- Order 5 details
(5, 5, 500, 75000.00),
-- Order 6 details
(6, 6, 50, 90000.00),
-- Order 7 details
(7, 7, 50, 32500.00),
-- Order 8 details
(8, 8, 30, 66000.00),
-- Order 9 details
(9, 9, 20, 110000.00),
-- Order 10 details
(10, 10, 50, 30000.00),
-- Order 11 details
(11, 11, 50, 47500.00),
-- Order 12 details
(12, 12, 30, 72000.00),
-- Order 13 details
(13, 13, 50, 27500.00),
-- Order 14 details
(14, 14, 20, 70000.00),
-- Order 15 details
(15, 15, 30, 42000.00),
-- Order 16 details
(16, 16, 50, 55000.00),
-- Order 17 details
(17, 17, 5, 75000.00),
-- Order 18 details
(18, 18, 25, 45000.00),
-- Order 19 details
(19, 19, 10, 65000.00),
-- Order 20 details
(20, 20, 30, 48000.00),
-- Order 21 details
(21, 21, 20, 56000.00),
-- Order 22 details
(22, 22, 15, 63000.00),
-- Order 23 details
(23, 23, 30, 36000.00),
-- Order 24 details
(24, 24, 20, 70000.00),
-- Order 25 details
(25, 25, 50, 45000.00);

-- -----------------------------------------------------
-- Sale Data (25 records - mix of paid, partial, unpaid)
-- -----------------------------------------------------
-- Fully paid sales (no customer association)
INSERT INTO Sale (customer_id, sale_time, total_amount, discount_amount) VALUES
(NULL, '2023-01-07 13:45:00', 65000.00, 0.00),
(NULL, '2023-01-12 14:30:00', 1750.00, 0.00),
(NULL, '2023-01-18 10:15:00', 2550.00, 0.00),
(NULL, '2023-01-23 11:30:00', 9500.00, 1000.00),
(NULL, '2023-01-28 15:45:00', 750.00, 0.00),
(NULL, '2023-02-03 12:30:00', 3600.00, 400.00),
(NULL, '2023-02-08 14:15:00', 1300.00, 0.00),
(NULL, '2023-02-13 09:45:00', 6600.00, 700.00),
(NULL, '2023-02-18 16:30:00', 11000.00, 1500.00),
(NULL, '2023-02-23 10:00:00', 1200.00, 0.00),
(NULL, '2023-02-28 13:15:00', 1900.00, 0.00),
(NULL, '2023-03-03 15:30:00', 4800.00, 500.00),
(NULL, '2023-03-08 11:45:00', 1100.00, 0.00),
(NULL, '2023-03-13 14:00:00', 7000.00, 800.00),
(NULL, '2023-03-18 09:30:00', 2800.00, 0.00),

-- Sales with customer association
(1, '2023-03-23 12:45:00', 22000.00, 0.00),
(2, '2023-03-28 14:30:00', 9000.00, 0.00),
(3, '2023-04-03 10:15:00', 28000.00, 0.00),
(4, '2023-04-08 11:30:00', 7000.00, 0.00),
(5, '2023-04-13 15:45:00', 35000.00, 0.00),
(6, '2023-04-18 12:30:00', 9000.00, 0.00),
(7, '2023-04-23 14:15:00', 6500.00, 0.00),
(8, '2023-04-28 09:45:00', 12500.00, 0.00),
(9, '2023-05-03 16:30:00', 18000.00, 0.00),
(10, '2023-05-08 10:00:00', 4500.00, 0.00);

-- -----------------------------------------------------
-- SaleDetail Data (multiple records per sale)
-- -----------------------------------------------------
INSERT INTO SaleDetail (sale_id, product_id, quantity, price_per_unit) VALUES
-- Sale 1 details
(1, 1, 1, 65000.00),
-- Sale 2 details
(2, 2, 5, 350.00),
-- Sale 3 details
(3, 3, 3, 850.00),
-- Sale 4 details
(4, 4, 1, 9500.00),
-- Sale 5 details
(5, 5, 5, 150.00),
-- Sale 6 details
(6, 6, 2, 1800.00),
-- Sale 7 details
(7, 7, 2, 650.00),
-- Sale 8 details
(8, 8, 3, 2200.00),
-- Sale 9 details
(9, 9, 2, 5500.00),
-- Sale 10 details
(10, 10, 2, 600.00),
-- Sale 11 details
(11, 11, 2, 950.00),
-- Sale 12 details
(12, 12, 2, 2400.00),
-- Sale 13 details
(13, 13, 2, 550.00),
-- Sale 14 details
(14, 14, 2, 3500.00),
-- Sale 15 details
(15, 15, 2, 1400.00),
-- Sale 16 details (Partial payment)
(16, 16, 5, 1100.00),
(16, 17, 1, 15000.00),
-- Sale 17 details (Partial payment)
(17, 18, 5, 1800.00),
(17, 1, 0, 0.00), -- Placeholder to match debt amount
-- Sale 18 details (Partial payment)
(18, 19, 2, 6500.00),
(18, 20, 10, 1600.00),
-- Sale 19 details (Partial payment)
(19, 21, 2, 2800.00),
(19, 22, 0, 0.00), -- Placeholder to match debt amount
-- Sale 20 details (Partial payment)
(20, 23, 5, 1200.00),
(20, 24, 5, 3500.00),
(20, 25, 10, 900.00),
-- Sale 21 details (Unpaid)
(21, 1, 0, 0.00), -- Placeholder to match debt amount
(21, 6, 5, 1800.00),
-- Sale 22 details (Unpaid)
(22, 7, 5, 650.00),
(22, 8, 1, 2200.00),
(22, 9, 0, 0.00), -- Placeholder to match debt amount
-- Sale 23 details (Unpaid)
(23, 10, 5, 600.00),
(23, 11, 5, 950.00),
(23, 12, 1, 2400.00),
-- Sale 24 details (Unpaid)
(24, 13, 5, 550.00),
(24, 14, 2, 3500.00),
(24, 15, 5, 1400.00),
-- Sale 25 details (Unpaid)
(25, 16, 3, 1100.00),
(25, 17, 0, 0.00); -- Placeholder to match debt amount

-- -----------------------------------------------------
-- Damage Data (25 records)
-- -----------------------------------------------------
INSERT INTO DamagedProduct (product_id, quantity_damaged, damage_cost, refund_amount, action_taken, reason, damage_date) VALUES
(1, 1, 65000.00, 65000.00, 'Refunded', 'Defective screen', '2023-01-08 10:30:00'),
(2, 10, 3500.00, NULL, 'No Action', 'Water damage during monsoon', '2023-01-15 11:45:00'),
(3, 5, 4250.00, 4250.00, 'Refunded', 'Manufacturing defect', '2023-01-22 14:15:00'),
(4, 1, 9500.00, NULL, 'Replaced', 'Broken wheel', '2023-01-29 09:30:00'),
(5, 20, 3000.00, NULL, 'No Action', 'Spoiled fish', '2023-02-05 16:00:00'),
(6, 3, 5400.00, 5400.00, 'Refunded', 'Wrong size delivered', '2023-02-12 13:30:00'),
(7, 2, 1300.00, NULL, 'Replaced', 'Torn packaging', '2023-02-19 10:45:00'),
(8, 2, 4400.00, 4400.00, 'Refunded', 'Cracked bat', '2023-02-26 15:15:00'),
(9, 1, 5500.00, NULL, 'Replaced', 'Audio issues', '2023-03-05 11:30:00'),
(10, 5, 3000.00, NULL, 'No Action', 'Damaged during shipping', '2023-03-12 14:00:00'),
(11, 3, 2850.00, 2850.00, 'Refunded', 'Leaking bottles', '2023-03-19 09:15:00'),
(12, 2, 4800.00, NULL, 'Replaced', 'Expired product', '2023-03-26 16:30:00'),
(13, 4, 2200.00, 2200.00, 'Refunded', 'Broken handles', '2023-04-02 12:45:00'),
(14, 1, 3500.00, NULL, 'Replaced', 'Wrong part shipped', '2023-04-09 10:00:00'),
(15, 3, 4200.00, 4200.00, 'Refunded', 'Missing pieces', '2023-04-16 15:30:00'),
(16, 5, 5500.00, NULL, 'No Action', 'Damaged covers', '2023-04-23 11:45:00'),
(17, 1, 15000.00, 15000.00, 'Refunded', 'Cracked body', '2023-04-30 14:15:00'),
(18, 2, 3600.00, NULL, 'Replaced', 'Incomplete kits', '2023-05-07 09:30:00'),
(19, 1, 6500.00, 6500.00, 'Refunded', 'Broken clasp', '2023-05-14 16:00:00'),
(20, 3, 4800.00, NULL, 'No Action', 'Damaged labels', '2023-05-21 12:30:00'),
(21, 1, 2800.00, 2800.00, 'Refunded', 'Scratched surface', '2023-05-28 10:45:00'),
(22, 1, 4200.00, NULL, 'Replaced', 'Torn strap', '2023-06-04 15:15:00'),
(23, 2, 2400.00, 2400.00, 'Refunded', 'Fabric defect', '2023-06-11 11:30:00'),
(24, 1, 3500.00, NULL, 'Replaced', 'Sound distortion', '2023-06-18 14:00:00'),
(25, 5, 4500.00, NULL, 'No Action', 'Damaged packaging', '2023-06-25 09:15:00');

-- -----------------------------------------------------
-- Transaction Data (for all financial transactions)
-- -----------------------------------------------------
-- Order Payment Transactions (reference_id points to ProductOrder.order_id)
INSERT INTO Transaction (reference_id, transaction_type, amount_paid, timestamp) VALUES
(1, 'Order Payment', 325000.00, '2023-01-05 09:35:00'),
(2, 'Order Payment', 35000.00, '2023-01-10 10:20:00'),
(3, 'Order Payment', 42500.00, '2023-01-15 11:05:00'),
(4, 'Order Payment', 47500.00, '2023-01-20 13:50:00'),
(5, 'Order Payment', 75000.00, '2023-01-25 14:35:00'),
(6, 'Order Payment', 90000.00, '2023-02-01 09:05:00'),
(7, 'Order Payment', 32500.00, '2023-02-05 10:35:00'),
(8, 'Order Payment', 66000.00, '2023-02-10 11:20:00'),
(9, 'Order Payment', 110000.00, '2023-02-15 13:05:00'),
(10, 'Order Payment', 30000.00, '2023-02-20 14:50:00'),
(11, 'Order Payment', 47500.00, '2023-02-25 15:35:00'),
(12, 'Order Payment', 72000.00, '2023-03-01 09:20:00'),
(13, 'Order Payment', 27500.00, '2023-03-05 10:05:00'),
(14, 'Order Payment', 70000.00, '2023-03-10 11:50:00'),
(15, 'Order Payment', 42000.00, '2023-03-15 13:35:00'),
(16, 'Order Payment', 55000.00, '2023-03-20 14:20:00'),
(17, 'Order Payment', 75000.00, '2023-03-25 15:05:00'),
(18, 'Order Payment', 45000.00, '2023-04-01 09:50:00'),

-- Sale Payment Transactions (reference_id points to Sale.sale_id)
(1, 'Sale Payment', 65000.00, '2023-01-07 13:50:00'),
(2, 'Sale Payment', 1750.00, '2023-01-12 14:35:00'),
(3, 'Sale Payment', 2550.00, '2023-01-18 10:20:00'),
(4, 'Sale Payment', 8500.00, '2023-01-23 11:35:00'),
(5, 'Sale Payment', 750.00, '2023-01-28 15:50:00'),
(6, 'Sale Payment', 3200.00, '2023-02-03 12:35:00'),
(7, 'Sale Payment', 1300.00, '2023-02-08 14:20:00'),
(8, 'Sale Payment', 5900.00, '2023-02-13 09:50:00'),
(9, 'Sale Payment', 9500.00, '2023-02-18 16:35:00'),
(10, 'Sale Payment', 1200.00, '2023-02-23 10:05:00'),
(11, 'Sale Payment', 1900.00, '2023-02-28 13:20:00'),
(12, 'Sale Payment', 4300.00, '2023-03-03 15:35:00'),
(13, 'Sale Payment', 1100.00, '2023-03-08 11:50:00'),
(14, 'Sale Payment', 6200.00, '2023-03-13 14:05:00'),
(15, 'Sale Payment', 2800.00, '2023-03-18 09:35:00'),
(16, 'Sale Payment', 11000.00, '2023-03-23 12:50:00'),
(17, 'Sale Payment', 3500.00, '2023-03-28 14:35:00'),
(18, 'Sale Payment', 14000.00, '2023-04-03 10:20:00'),
(19, 'Sale Payment', 3500.00, '2023-04-08 11:35:00'),
(20, 'Sale Payment', 14000.00, '2023-04-13 15:50:00'),

-- Refund Transactions (reference_id points to DamagedProduct.damage_id)
(1, 'Refund', 65000.00, '2023-01-08 10:35:00'),
(3, 'Refund', 4250.00, '2023-01-22 14:20:00'),
(6, 'Refund', 5400.00, '2023-02-12 13:35:00'),
(8, 'Refund', 4400.00, '2023-02-26 15:20:00'),
(11, 'Refund', 2850.00, '2023-03-19 09:20:00'),
(13, 'Refund', 2200.00, '2023-04-02 12:50:00'),
(15, 'Refund', 4200.00, '2023-04-16 15:35:00'),
(17, 'Refund', 15000.00, '2023-04-30 14:20:00'),
(19, 'Refund', 6500.00, '2023-05-14 16:05:00'),
(21, 'Refund', 2800.00, '2023-05-28 10:50:00'),
(23, 'Refund', 2400.00, '2023-06-11 11:35:00'),

-- Debt Payment Transactions (reference_id points to Sale.sale_id)
(16, 'Debt Payment', 3500.00, '2023-03-25 13:00:00'),
(17, 'Debt Payment', 1750.00, '2023-03-30 14:45:00'),
(18, 'Debt Payment', 7000.00, '2023-04-05 10:30:00'),
(19, 'Debt Payment', 1400.00, '2023-04-10 11:45:00'),
(20, 'Debt Payment', 10500.00, '2023-04-15 16:00:00');

-- -----------------------------------------------------
-- Update Product stock_quantity to reflect sales, orders and damages
-- -----------------------------------------------------

-- This ensures the stock quantities are consistent with all transactions
UPDATE Product SET stock_quantity = 53 WHERE product_id = 1;  -- Initial 50 + 5 ordered - 1 sold - 1 damaged
UPDATE Product SET stock_quantity = 285 WHERE product_id = 2; -- Initial 200 + 100 ordered - 5 sold - 10 damaged
UPDATE Product SET stock_quantity = 192 WHERE product_id = 3; -- Initial 150 + 50 ordered - 3 sold - 5 damaged
UPDATE Product SET stock_quantity = 33 WHERE product_id = 4;  -- Initial 30 + 5 ordered - 1 sold - 1 damaged
UPDATE Product SET stock_quantity = 975 WHERE product_id = 5; -- Initial 500 + 500 ordered - 5 sold - 20 damaged
UPDATE Product SET stock_quantity = 142 WHERE product_id = 6; -- Initial 100 + 50 ordered - 2 sold - 3 damaged - 5 sold
UPDATE Product SET stock_quantity = 121 WHERE product_id = 7; -- Initial 80 + 50 ordered - 2 sold - 2 damaged - 5 sold
UPDATE Product SET stock_quantity = 84 WHERE product_id = 8;  -- Initial 60 + 30 ordered - 3 sold - 2 damaged - 1 sold
UPDATE Product SET stock_quantity = 92 WHERE product_id = 9;  -- Initial 75 + 20 ordered - 2 sold - 1 damaged
UPDATE Product SET stock_quantity = 158 WHERE product_id = 10; -- Initial 120 + 50 ordered - 2 sold - 5 damaged - 5 sold
UPDATE Product SET stock_quantity = 130 WHERE product_id = 11; -- Initial 90 + 50 ordered - 2 sold - 3 damaged - 5 sold
UPDATE Product SET stock_quantity = 135 WHERE product_id = 12; -- Initial 110 + 30 ordered - 2 sold - 2 damaged - 1 sold
UPDATE Product SET stock_quantity = 109 WHERE product_id = 13; -- Initial 70 + 50 ordered - 2 sold - 4 damaged - 5 sold
UPDATE Product SET stock_quantity = 100 WHERE product_id = 14; -- Initial 85 + 20 ordered - 2 sold - 1 damaged - 2 sold
UPDATE Product SET stock_quantity = 85 WHERE product_id = 15; -- Initial 65 + 30 ordered - 2 sold - 3 damaged - 5 sold
UPDATE Product SET stock_quantity = 132 WHERE product_id = 16; -- Initial 95 + 50 ordered - 5 sold - 5 damaged - 3 sold
UPDATE Product SET stock_quantity = 28 WHERE product_id = 17; -- Initial 25 + 5 ordered - 1 sold - 1 damaged
UPDATE Product SET stock_quantity = 73 WHERE product_id = 18; -- Initial 55 + 25 ordered - 5 sold - 2 damaged
UPDATE Product SET stock_quantity = 47 WHERE product_id = 19; -- Initial 40 + 10 ordered - 2 sold - 1 damaged
UPDATE Product SET stock_quantity = 147 WHERE product_id = 20; -- Initial 130 + 30 ordered - 10 sold - 3 damaged
UPDATE Product SET stock_quantity = 77 WHERE product_id = 21; -- Initial 60 + 20 ordered - 2 sold - 1 damaged
UPDATE Product SET stock_quantity = 59 WHERE product_id = 22; -- Initial 45 + 15 ordered - 1 damaged
UPDATE Product SET stock_quantity = 98 WHERE product_id = 23; -- Initial 75 + 30 ordered - 5 sold - 2 damaged
UPDATE Product SET stock_quantity = 69 WHERE product_id = 24; -- Initial 55 + 20 ordered - 5 sold - 1 damaged
UPDATE Product SET stock_quantity = 135 WHERE product_id = 25; -- Initial 100 + 50 ordered - 10 sold - 5 damaged

-- Verify all reference_ids exist in their respective tables
-- This ensures data integrity for the Transaction table
SELECT 'ProductOrder' as table_name, COUNT(*) as missing_references
FROM Transaction t
LEFT JOIN ProductOrder po ON t.reference_id = po.order_id
WHERE t.transaction_type = 'Order Payment' AND po.order_id IS NULL;

SELECT 'Sale' as table_name, COUNT(*) as missing_references
FROM Transaction t
LEFT JOIN Sale s ON t.reference_id = s.sale_id
WHERE t.transaction_type IN ('Sale Payment', 'Debt Payment') AND s.sale_id IS NULL;

SELECT 'DamagedProduct' as table_name, COUNT(*) as missing_references
FROM Transaction t
LEFT JOIN DamagedProduct dp ON t.reference_id = dp.damage_id
WHERE t.transaction_type = 'Refund' AND dp.damage_id IS NULL;