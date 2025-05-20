-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: May 20, 2025 at 07:45 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `shop_db`
--
CREATE DATABASE IF NOT EXISTS `shop_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `shop_db`;

--
-- Dumping data for table `company`
--

INSERT INTO `company` (`company_id`, `company_name`, `company_phone_number`, `company_address`) VALUES
(1, 'Pran-RFL Group', '01711-223344', 'Sheikhpara, Jashore Sadar, Jashore'),
(2, 'Olympic Industries Ltd', '01913-445566', 'hikargacha Bazaar, Jashore'),
(3, 'Aarong', '01817-665544', '	Ghop Noapara Road, Jashore Town'),
(4, 'Igloo Ice Cream', '01844-556677', 'Jessore Cantonment Market, Jashore'),
(5, 'Fresh (Toufika Foods Ltd.)', '01715-889900', 'Chowgacha Main Road, Jashor'),
(6, 'Square Food & Beverage Ltd', '01819-556677', 'Manihar, Jashore City, Jashore'),
(7, 'Akij Group', '01818-778899', 'Akij House, Manihar Mor, Jashore'),
(8, 'Danish Foods Ltd', '	01716-223355', 'Arabpur Bazar, Jashore'),
(9, 'ACI Limited', '01712-334455', 'Keshabpur Bazar, Keshabpur, Jashore'),
(10, 'Partex Group	', '01917-889900	', 'Railgate Area, Abhaynagar, Jashore	'),
(11, 'BD Food Ltd.	', '01733-556677	', 'Monirampur Bus Stand, Monirampur, Jashore	'),
(12, 'Bashundhara Group', '01820-334455	', 'Doratana Mor, Jashore Sadar, Jashore	');

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `contact_info`) VALUES
(1, 'Afroza Begum	', '01822-334455	'),
(2, 'Minhazul Abedin	', '01720-998800	'),
(3, 'Sazzad Hossain	', '01919-884422	'),
(4, 'Khadija Sultana	', '01613-442299	'),
(5, 'Md. Mizanur Rahman	', '01718-332211	'),
(6, 'Tanjila Haque	', '01813-223344	'),
(7, 'Rehana Akter	', '01714-998877	'),
(8, 'Shahinur Rahman	', '01912-776655	'),
(9, 'Mst. Lutfun Nahar	', '01811-443322	'),
(10, 'Nazmul Huda	', '01710-112233	'),
(11, 'Anwara Begum	', '01825-667799	'),
(12, 'Kamal Uddin	', '01719-224488	');

--
-- Dumping data for table `damagedproduct`
--

INSERT INTO `damagedproduct` (`damage_id`, `product_id`, `quantity_damaged`, `damage_cost`, `refund_amount`, `action_taken`, `reason`, `damage_date`) VALUES
(1, 21, 2, 180.00, 0.00, 'Replaced', 'Leakage  found the in the aerosol can ', '2025-05-13 17:38:38'),
(2, 6, 5, 1000.00, 1000.00, 'Refunded', 'The customer complained about the milk and we found that some of them actually got spoiled', '2025-05-13 17:52:18'),
(3, 2, 4, 680.00, 0.00, 'No Action', 'The jug had a slight defect.', '2025-05-13 19:11:46');

--
-- Dumping data for table `orderdetail`
--

INSERT INTO `orderdetail` (`order_detail_id`, `order_id`, `product_id`, `quantity`, `cost`) VALUES
(1, 1, 1, 10, 2900.00),
(2, 1, 2, 20, 2200.00),
(3, 1, 3, 50, 5000.00),
(4, 1, 4, 200, 3000.00),
(5, 1, 5, 100, 1700.00),
(6, 1, 6, 25, 2250.00),
(7, 1, 7, 50, 2100.00),
(8, 1, 8, 30, 4800.00),
(9, 1, 9, 40, 3000.00),
(10, 1, 10, 100, 2000.00),
(11, 2, 29, 40, 3800.00),
(12, 2, 30, 50, 1250.00),
(13, 2, 31, 30, 4350.00),
(14, 2, 32, 100, 1500.00),
(15, 2, 33, 40, 11600.00),
(16, 2, 34, 30, 2400.00),
(17, 2, 35, 40, 4000.00),
(18, 2, 36, 25, 4000.00),
(19, 2, 37, 20, 7800.00),
(20, 2, 38, 60, 2520.00),
(21, 3, 23, 50, 1750.00),
(22, 3, 24, 150, 2700.00),
(23, 3, 25, 70, 2100.00),
(24, 3, 26, 70, 2100.00),
(25, 3, 27, 60, 3000.00),
(26, 3, 28, 80, 2400.00),
(27, 4, 37, 20, 8000.00),
(28, 5, 11, 60, 4800.00),
(29, 5, 12, 100, 3000.00),
(30, 5, 15, 30, 4950.00),
(31, 5, 16, 90, 3600.00),
(32, 5, 17, 70, 3150.00),
(33, 5, 18, 80, 3600.00),
(34, 5, 19, 50, 4750.00),
(35, 5, 20, 60, 5700.00),
(36, 5, 21, 40, 6400.00),
(37, 5, 22, 100, 2500.00),
(38, 6, 40, 120, 4560.00),
(39, 6, 41, 200, 4000.00),
(40, 6, 42, 180, 4320.00),
(41, 6, 43, 30, 11100.00),
(42, 6, 44, 50, 3900.00),
(43, 6, 45, 60, 6000.00),
(44, 6, 46, 40, 3520.00),
(45, 6, 47, 90, 3240.00),
(46, 6, 48, 60, 6000.00);

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `name`, `company_id`, `stock_quantity`, `unit_price`, `restock_threshold`) VALUES
(1, 'RFL Water Drum 20L', 1, 9, 320.00, 10),
(2, 'RFL Plastic Jug 2L', 1, 16, 130.00, 10),
(3, 'Pran Sugar 1kg', 1, 41, 110.00, 20),
(4, 'Pran Noodles Chicken Flavour', 1, 175, 18.00, 120),
(5, 'Pran Drinking Water 1.5L', 1, 94, 20.00, 15),
(6, 'Pran UHT Milk 1L', 1, 18, 100.00, 12),
(7, 'Pran Chanachur 200g', 1, 44, 50.00, 30),
(8, 'Pran Mustard Oil 1L', 1, 27, 180.00, 15),
(9, 'Pran Tomato Ketchup 500g', 1, 40, 85.00, 10),
(10, 'Pran Mango Juice 250ml', 1, 91, 25.00, 25),
(11, 'ACI Pure Mustard Oil 500ml', 9, 57, 90.00, 20),
(12, 'ACI Mosquito Coil (10 pcs)', 9, 91, 35.00, 15),
(13, 'Savlon Baby lotion', 9, 0, 110.00, 20),
(14, 'Savlon Baby pamper (5 pcs)', 9, 0, 180.00, 15),
(15, 'Savlon Baby Wipes 80 pcs', 9, 27, 180.00, 5),
(16, 'Savlon Antibacterial Soap 100g', 9, 80, 45.00, 15),
(17, 'ACI Pure Suji 500g', 9, 68, 50.00, 15),
(18, 'ACI Hand Sanitizer 100ml', 9, 77, 50.00, 25),
(19, 'ACI Pure Atta 2kg', 9, 47, 105.00, 15),
(20, 'Savlon Antiseptic Liquid 500ml', 9, 60, 110.00, 15),
(21, 'ACI Aerosol Insect Spray 475ml', 9, 26, 175.00, 15),
(22, 'ACI Pure Salt 1kg', 9, 86, 28.00, 20),
(23, 'Radhuni Coriander Powder 100g', 6, 45, 40.00, 15),
(24, 'Chopstick Noodles Pack', 6, 130, 20.00, 18),
(25, 'Radhuni Chicken Masala 50g', 6, 56, 35.00, 18),
(26, 'Radhuni Beef Masala 50g', 6, 70, 60.00, 18),
(27, 'Radhuni Turmeric Powder 200g', 6, 58, 35.00, 18),
(28, 'Radhuni Meat Masala 50g', 6, 77, 45.00, 18),
(29, 'Fresh Atta 2kg', 5, 33, 105.00, 15),
(30, 'Fresh Salt 1kg', 5, 41, 28.00, 18),
(31, 'Fresh Soyabean Oil 1L', 5, 20, 160.00, 20),
(32, 'Fresh Drinking Water 1L', 5, 84, 18.00, 18),
(33, 'Fresh Full Cream Milk Powder 500g', 5, 34, 320.00, 12),
(34, 'Fresh Tea 200g', 5, 27, 90.00, 20),
(35, 'Fresh Refined Sugar 1kg', 5, 35, 110.00, 20),
(36, 'Fresh Mustard Oil 1L', 5, 25, 180.00, 18),
(37, 'Fresh Premium Rice 5kg', 5, 39, 420.00, 25),
(38, 'Fresh Suji 500g', 5, 56, 48.00, 12),
(39, 'Igloo Vanilla Cup 100ml', 4, 0, 30.00, 25),
(40, 'Bashundhara Toilet Tissue', 12, 120, 20.00, 120),
(41, 'Bashundhara Facial Tissue', 12, 200, 70.00, 40),
(42, 'Bashundhara Kitchen Towel', 12, 180, 90.00, 30),
(43, 'Bashundhara Paper Napkin', 12, 30, 40.00, 30),
(44, 'Bashundhara A4 Paper 80 GSM', 12, 50, 450.00, 12),
(45, 'Bashundhara Notebook', 12, 60, 55.00, 20),
(46, 'Bashundhara Exercise Book', 12, 40, 30.00, 15),
(47, 'Bashundhara Baby Diaper(M)', 12, 90, 250.00, 25),
(48, 'Bashundhara Detergent Powder', 12, 60, 150.00, 20),
(49, 'Bashundhara Packaged Water 1L', 12, 0, 18.00, 25),
(50, 'BD Chilli Powder 200g', 11, 0, 60.00, 15),
(51, 'BD Turmeric Powder 100g', 11, 0, 35.00, 20),
(52, 'BD Cumin Powder 100g', 11, 0, 50.00, 15),
(53, 'BD Mustard Oil 500mL', 11, 0, 90.00, 15),
(54, 'BD Mix Masala', 11, 0, 35.00, 25),
(55, 'BD Tea 200g', 11, 0, 85.00, 28),
(56, 'BD Salt', 11, 0, 28.00, 15),
(57, 'BD Vermicelli', 11, 0, 30.00, 20),
(58, 'BD Coriander Power 100g', 11, 0, 40.00, 20),
(59, 'Partex Tissue Box', 10, 0, 70.00, 50),
(60, 'Partex Mineral Water', 10, 0, 18.00, 50),
(61, 'Partex Kitchen Naplin', 10, 0, 45.00, 30),
(62, 'Partex Lemon Drink', 10, 0, 25.00, 25),
(63, 'Partex A4 Paper', 10, 0, 420.00, 20),
(64, 'Partex Soft Tissue Roll', 10, 0, 25.00, 20),
(65, 'Partex Hand Wash', 10, 0, 60.00, 25);

--
-- Dumping data for table `productorder`
--

INSERT INTO `productorder` (`order_id`, `company_id`, `order_time`) VALUES
(1, 1, '2025-05-13 23:03:23'),
(2, 5, '2025-05-13 23:05:56'),
(3, 6, '2025-05-13 23:07:38'),
(4, 5, '2025-05-13 23:09:45'),
(5, 9, '2025-05-13 23:12:11'),
(6, 12, '2025-05-14 01:34:43');

--
-- Dumping data for table `sale`
--

INSERT INTO `sale` (`sale_id`, `customer_id`, `sale_time`, `total_amount`, `discount_amount`, `payment_status`) VALUES
(1, NULL, '2025-05-13 23:19:40', 545.00, 0.00, 'Paid'),
(2, NULL, '2025-05-13 23:20:32', 440.00, 0.00, 'Paid'),
(3, NULL, '2025-05-13 23:22:06', 243.00, 0.00, 'Paid'),
(4, NULL, '2025-05-13 23:23:54', 640.00, 0.00, 'Paid'),
(5, NULL, '2025-05-13 23:24:44', 680.00, 0.00, 'Paid'),
(6, NULL, '2025-05-13 23:25:43', 3030.00, 20.00, 'Paid'),
(7, 1, '2025-05-13 23:29:49', 380.00, 0.00, 'Paid'),
(8, 8, '2025-05-13 23:30:50', 410.00, 0.00, 'Paid'),
(9, 4, '2025-05-13 23:32:16', 767.00, 0.00, 'Paid'),
(10, 5, '2025-05-13 23:33:38', 250.00, 0.00, 'Paid'),
(11, 11, '2025-05-13 23:35:10', 855.00, 0.00, 'Paid'),
(12, 12, '2025-05-13 23:40:28', 880.00, 0.00, 'Paid'),
(13, 2, '2025-05-13 23:41:33', 787.00, 0.00, 'Paid'),
(14, 9, '2025-05-13 23:42:24', 692.00, 0.00, 'Paid'),
(15, 10, '2025-05-13 23:43:34', 1285.00, 0.00, 'Paid'),
(16, 7, '2025-05-13 23:53:41', 1075.00, 0.00, 'Paid'),
(17, 3, '2025-05-13 23:55:33', 1365.00, 0.00, 'Paid'),
(18, 6, '2025-05-13 23:57:11', 1305.00, 0.00, 'Paid');

--
-- Dumping data for table `saledetail`
--

INSERT INTO `saledetail` (`sale_detail_id`, `sale_id`, `product_id`, `quantity`, `price_per_unit`) VALUES
(1, 1, 29, 3, 105.00),
(2, 1, 4, 5, 18.00),
(3, 1, 22, 5, 28.00),
(4, 2, 25, 2, 35.00),
(5, 2, 7, 3, 50.00),
(6, 2, 35, 2, 110.00),
(7, 3, 12, 1, 35.00),
(8, 3, 18, 2, 50.00),
(9, 3, 32, 6, 18.00),
(10, 4, 4, 15, 18.00),
(11, 4, 31, 2, 160.00),
(12, 4, 18, 1, 50.00),
(13, 5, 37, 1, 420.00),
(14, 5, 25, 4, 35.00),
(15, 5, 5, 6, 20.00),
(16, 6, 21, 10, 175.00),
(17, 6, 33, 2, 320.00),
(18, 6, 3, 6, 110.00),
(19, 7, 22, 5, 28.00),
(20, 7, 7, 3, 50.00),
(21, 7, 34, 1, 90.00),
(22, 8, 6, 2, 100.00),
(23, 8, 30, 5, 28.00),
(24, 8, 12, 2, 35.00),
(25, 9, 11, 3, 90.00),
(26, 9, 38, 4, 48.00),
(27, 9, 16, 4, 45.00),
(28, 9, 10, 5, 25.00),
(29, 10, 32, 10, 18.00),
(30, 10, 27, 2, 35.00),
(31, 11, 24, 10, 20.00),
(32, 11, 29, 4, 105.00),
(33, 11, 17, 2, 50.00),
(34, 11, 16, 3, 45.00),
(35, 12, 12, 3, 35.00),
(36, 12, 33, 2, 320.00),
(37, 12, 16, 3, 45.00),
(38, 13, 15, 3, 180.00),
(39, 13, 28, 3, 45.00),
(40, 13, 30, 4, 28.00),
(41, 14, 31, 3, 160.00),
(42, 14, 22, 4, 28.00),
(43, 14, 10, 4, 25.00),
(44, 15, 21, 4, 175.00),
(45, 15, 25, 5, 35.00),
(46, 15, 4, 5, 18.00),
(47, 15, 1, 1, 320.00),
(48, 16, 25, 3, 35.00),
(49, 16, 3, 3, 110.00),
(50, 16, 33, 2, 320.00),
(51, 17, 34, 2, 90.00),
(52, 17, 19, 3, 105.00),
(53, 17, 35, 3, 110.00),
(54, 17, 8, 3, 180.00),
(55, 18, 23, 5, 40.00),
(56, 18, 31, 5, 160.00),
(57, 18, 12, 3, 35.00),
(58, 18, 24, 10, 20.00);

--
-- Dumping data for table `salesperson`
--

INSERT INTO `salesperson` (`salesperson_id`, `name`, `phone`, `status`, `archived_on`, `company_id`) VALUES
(1, 'Md. Imran Hossain', '01812-445566', 'Archived', '2025-05-14 01:10:25', 1),
(2, 'Tanzila Haque', '01822-334411', 'Active', NULL, 2),
(3, 'Shakil Ahmed', '01766-123456', 'Active', NULL, 3),
(4, 'Sadia Sultana', '01611-998877', 'Archived', '2025-05-14 01:09:48', 4),
(5, 'Rezaul Karim', '01788-336699', 'Active', NULL, 5),
(6, 'Farzana Akhter', '01913-224466', 'Active', NULL, 6),
(7, 'Rokeya Begum', '01777-554433', 'Active', NULL, 7),
(8, '	Mahbubur Rahman', '01911-887766', 'Active', NULL, 8),
(9, 'Nazmul Arefin', '01745-667788', 'Active', NULL, 9),
(10, 'Jahangir Alam	', '01710-112233', 'Active', NULL, 10),
(11, 'Khaleda Khatun	', '01825-667799', 'Active', NULL, 11),
(12, 'Mizanur Rahman	', '01719-224488', 'Archived', '2025-05-14 01:09:11', 12),
(13, 'Zabed Uddin', '01719-224424', 'Active', NULL, 12),
(14, 'Shahinur Islam ', '01611-998877', 'Active', NULL, 4),
(15, 'Jubayer Al Haque', '01812-445599', 'Active', NULL, 1);

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`transaction_id`, `reference_id`, `transaction_type`, `amount_paid`, `timestamp`) VALUES
(1, 1, 'Order Payment', 28950.00, '2025-05-13 23:03:23'),
(2, 2, 'Order Payment', 43220.00, '2025-05-13 23:05:56'),
(3, 3, 'Order Payment', 14050.00, '2025-05-13 23:07:38'),
(4, 4, 'Order Payment', 8000.00, '2025-05-13 23:09:45'),
(5, 5, 'Order Payment', 42450.00, '2025-05-13 23:12:11'),
(6, 1, 'Sale Payment', 545.00, '2025-05-13 23:19:40'),
(7, 2, 'Sale Payment', 440.00, '2025-05-13 23:20:32'),
(8, 3, 'Sale Payment', 243.00, '2025-05-13 23:22:06'),
(9, 4, 'Sale Payment', 640.00, '2025-05-13 23:23:54'),
(10, 5, 'Sale Payment', 680.00, '2025-05-13 23:24:44'),
(11, 6, 'Sale Payment', 3030.00, '2025-05-13 23:25:43'),
(12, 7, 'Sale Payment', 0.00, '2025-05-13 23:29:49'),
(13, 8, 'Sale Payment', 0.00, '2025-05-13 23:30:50'),
(14, 9, 'Sale Payment', 0.00, '2025-05-13 23:32:16'),
(15, 4, 'Debt Payment', 500.00, '2025-05-13 23:32:30'),
(16, 10, 'Sale Payment', 0.00, '2025-05-13 23:33:38'),
(17, 11, 'Sale Payment', 0.00, '2025-05-13 23:35:10'),
(18, 11, 'Debt Payment', 400.00, '2025-05-13 23:36:53'),
(19, 12, 'Sale Payment', 0.00, '2025-05-13 23:40:28'),
(20, 13, 'Sale Payment', 0.00, '2025-05-13 23:41:33'),
(21, 14, 'Sale Payment', 0.00, '2025-05-13 23:42:24'),
(22, 15, 'Sale Payment', 0.00, '2025-05-13 23:43:34'),
(23, 16, 'Sale Payment', 0.00, '2025-05-13 23:53:41'),
(24, 17, 'Sale Payment', 0.00, '2025-05-13 23:55:33'),
(25, 18, 'Sale Payment', 0.00, '2025-05-13 23:57:11'),
(26, 2, 'Refund', 1000.00, '2025-05-13 23:57:22'),
(27, 3, 'Debt Payment', 700.00, '2025-05-14 00:24:35'),
(28, 7, 'Debt Payment', 500.00, '2025-05-14 00:24:46'),
(29, 5, 'Debt Payment', 250.00, '2025-05-14 00:26:41'),
(30, 6, 'Debt Payment', 500.00, '2025-05-14 01:10:47'),
(31, 6, 'Order Payment', 46640.00, '2025-05-14 01:34:43');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
