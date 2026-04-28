-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2026 at 12:38 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `skincare_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL DEFAULT '',
  `shipping_address` text NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','shipped','delivered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `total_price`, `status`, `created_at`) VALUES
(1, NULL, 'hbfd', 'fankyharlow@gmail.com', '', '', 155.00, 'pending', '2026-04-15 18:41:48'),
(2, NULL, 'erfrfr', 'malakazzouz91@gmail.com', '', '', 340.00, 'pending', '2026-04-15 18:43:58'),
(3, NULL, 'erfrfr', 'malakazzouz91@gmail.com', '', '', 27.00, 'pending', '2026-04-15 18:48:59'),
(4, NULL, 'huff', 'malek.azzouz@isgb.ucar.tn', '', '', 27.00, 'pending', '2026-04-15 18:49:31'),
(5, NULL, 'hbfd', 'azzouzmalak95@gmail.com', '', '', 55.00, 'pending', '2026-04-15 19:00:29'),
(6, NULL, 'huff', 'malek.azzouz@isgb.ucar.tn', '', '', 155.00, 'pending', '2026-04-15 19:18:29'),
(7, NULL, 'huff', 'azzouzmalak95@gmail.com', '', '', 75.00, 'pending', '2026-04-17 14:28:14'),
(8, NULL, 'erfrfr', 'malek.azzouz@isgb.ucar.tn', '', '', 75.00, 'pending', '2026-04-17 14:58:20'),
(9, NULL, 'huff', 'fankyharlow@gmail.com', '', '', 75.00, 'pending', '2026-04-19 21:27:19'),
(10, 1, 'mali harlow', 'fankyharlow@gmail.com', '54669874', 'hobi beach', 300.00, 'pending', '2026-04-20 21:51:56'),
(11, 1, 'mali harlow', 'fankyharlow@gmail.com', '54669874', 'hobi beach', 182.00, 'pending', '2026-04-26 14:58:55'),
(12, 1, 'mali harlow', 'fankyharlow@gmail.com', '54669874', 'hobi beach', 27.00, 'pending', '2026-04-26 15:04:48'),
(13, 2, 'malak A', 'azzouzmalak95@gmail.com', '+21627557988', 'cite Malouf corniche bizerte', 110.00, 'pending', '2026-04-26 15:06:23'),
(14, 2, 'malak A', 'azzouzmalak95@gmail.com', '+21627557988', 'cite Malouf corniche bizerte', 440.00, 'pending', '2026-04-26 16:09:13'),
(15, 3, 'malak Alou', '123@gmail.com', '+21627557988', 'cite Malouf corniche bizerte', 75.00, 'pending', '2026-04-28 10:36:00'),
(16, 3, 'malak Alou', '123@gmail.com', '+21627557988', 'cite Malouf corniche bizerte', 305.00, 'pending', '2026-04-28 10:37:35');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(1, 1, 2, 1, 155.00),
(2, 2, 1, 1, 110.00),
(3, 2, 2, 1, 155.00),
(4, 2, 3, 1, 75.00),
(5, 3, 4, 1, 27.00),
(6, 4, 4, 1, 27.00),
(7, 5, 6, 1, 55.00),
(8, 6, 2, 1, 155.00),
(9, 7, 3, 1, 75.00),
(10, 8, 3, 1, 75.00),
(11, 9, 3, 1, 75.00),
(12, 10, 3, 4, 75.00),
(13, 11, 2, 1, 155.00),
(14, 11, 4, 1, 27.00),
(15, 12, 4, 1, 27.00),
(16, 13, 6, 2, 55.00),
(17, 14, 1, 4, 110.00),
(18, 15, 3, 1, 75.00),
(19, 16, 2, 1, 155.00),
(20, 16, 3, 2, 75.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `stock`, `created_at`) VALUES
(1, 'Artemisia Soothing Gel Cream', 'Calming gel-cream with artemisia extract to reduce redness and irritation.', 110.00, 'skincare images/Artemisia Soothing Gel Cream.png', 45, '2026-04-15 18:40:52'),
(2, 'Diamond Truffle Contour Body Oil', 'Luxurious body oil infused with diamond powder and truffle for radiant skin.', 155.00, 'skincare images/Diamond Truffle Contour Body Oil.png', 25, '2026-04-15 18:40:52'),
(3, 'Galactomyces Glutathione Glow Milky Toner', 'Brightening toner that evens skin tone and boosts glow.', 75.00, 'skincare images/Galactomyces Glutathione Glow Milky Toner.png', 49, '2026-04-15 18:40:52'),
(4, 'Pore Cleansing Oil [PHA]', 'Gentle PHA-infused cleansing oil that melts away impurities without stripping skin.', 27.00, 'skincare images/Pore Cleansing Oil [PHA].png', 76, '2026-04-15 18:40:52'),
(5, 'Renewing Rich Beauty Cream', 'Rich anti-aging cream that renews skin texture and restores moisture balance.', 190.00, 'skincare images/Renewing Rich Beauty Cream.png', 25, '2026-04-15 18:40:52'),
(6, 'Snail Mucin Energy Essence', 'Hydrating essence packed with snail secretion filtrate for plump, bouncy skin.', 55.00, 'skincare images/Snail Mucin Energy Essence.png', 67, '2026-04-15 18:40:52'),
(7, 'Solid In Lip Essence', 'Nourishing lip essence that melts on contact for soft, hydrated lips.', 25.00, 'skincare images/Solid In Lip Essence.png', 100, '2026-04-15 18:40:52'),
(8, 'The Cleansing Duo', 'Complete cleansing set: oil cleanser + foam cleanser for a perfect double cleanse.', 220.00, 'skincare images/The Cleansing Duo.png', 20, '2026-04-15 18:40:52'),
(9, 'VT PDRN Essence 100', 'Advanced PDRN essence that repairs skin barrier and boosts elasticity.', 87.00, 'skincare images/VT PDRN Essence 100.png', 45, '2026-04-15 18:40:52');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `email` varchar(180) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` text NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `address`, `password_hash`, `created_at`) VALUES
(1, 'mali', 'harlow', 'fankyharlow@gmail.com', '54669874', 'hobi beach', '$2y$10$SBuro4P4yJ90BGzElUYwq.Vp9bJAj/CJjWlKZYbu.5Y5EmZAJIpnO', '2026-04-20 21:51:46'),
(2, 'malak', 'A', 'azzouzmalak95@gmail.com', '+21627557988', 'cite Malouf corniche bizerte', '$2y$12$qdlEDCue2W.208SHfROyxusyPovFI5RKEjwIZVRoak62NZlB/6ee6', '2026-04-26 15:05:35'),
(3, 'malak', 'Alou', '123@gmail.com', '+21627557988', 'cite Malouf corniche bizerte', '$2y$12$1xvuvtYBI.Cri6MzU4i2CuibNf8AlYxU6whkZ4ectrQY3yr6k./My', '2026-04-28 10:35:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
