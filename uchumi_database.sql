-- Uchumi Grocery Database Export for XAMPP
-- Database: uchumi

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user_role` varchar(20) DEFAULT 'user',
  `remember_token` varchar(255) DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--
-- Password for Icon is Icon147* (hashed)
INSERT INTO `users` (`username`, `password`, `email`, `full_name`, `user_role`) VALUES
('Icon', '$2y$10$wcOKlUaGLE0758d6Z5t9Fe0yD8/DUoB2iKiDjpS8KinGLLSlJTKlG', 'admin@uchumi.com', 'Icon Admin', 'admin'),
('Adilex', '$2y$10$3Hgu15ZkP92hvpmhr9Dj6ufyB9eOjEgnz4QfB4TJEfGr3PTBjAiY2', 'adilex@uchumi.com', 'Adilex Admin', 'admin'),
('Lewis', '$2y$10$5./MM79ZqdA1cixXzwZ2EOX1z.XyoQ2se1XzOy7FV8GS0tjvZq.U6', 'lewis@uchumi.com', 'Lewis Admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `unit` varchar(20) DEFAULT 'kg',
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT 'images/products/product-placeholder.jpg',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`name`, `category`, `price`, `unit`, `description`, `image_url`, `status`) VALUES
('Fresh Tomatoes', 'Vegetables', 120.00, 'kg', 'Ripe and juicy organic tomatoes from local farms.', 'images/products/tomatoes.jpg', 'active'),
('Red Onions', 'Vegetables', 150.00, 'kg', 'High-quality red onions, perfect for cooking.', 'images/products/onions.jpg', 'active'),
('Sweet Bananas', 'Fruits', 80.00, 'bunch', 'Sweet and ripe yellow bananas.', 'images/products/banana.jpg', 'active'),
('Fresh Spinach', 'Vegetables', 40.00, 'bunch', 'Freshly harvested green spinach leaves.', 'images/products/spinach.jpg', 'active'),
('Green Cabbage', 'Vegetables', 60.00, 'piece', 'Large, firm green cabbage.', 'images/products/cabbage.jpg', 'active'),
('Potatoes', 'Vegetables', 180.00, 'kg', 'Clean and large Irish potatoes.', 'images/products/potatoes.jpg', 'active'),
('Watermelon', 'Fruits', 350.00, 'piece', 'Large, sweet and juicy watermelons.', 'images/products/watermelon.jpg', 'active'),
('Fresh Milk', 'Dairy', 70.00, 'packet', 'Fresh pasteurized milk (500ml).', 'images/products/milk.jpg', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `shipping_address` text DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE IF NOT EXISTS `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
