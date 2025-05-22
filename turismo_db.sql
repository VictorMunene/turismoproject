-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 28, 2025 at 11:42 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `turismo_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `brand_list`
--

CREATE TABLE `brand_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand_list`
--

INSERT INTO `brand_list` (`id`, `name`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 'Mercedes-benz', 1, 0, '2022-06-02 08:55:33', '2022-06-02 08:55:33'),
(2, 'Toyota', 1, 0, '2022-06-02 08:55:47', '2022-06-02 08:55:47'),
(3, 'Ford', 1, 0, '2022-06-02 08:56:01', '2022-06-02 08:56:01'),
(4, 'Hyundai', 1, 0, '2022-06-02 08:56:49', '2022-06-02 08:56:49'),
(5, 'Chevrolet', 1, 0, '2022-06-02 08:56:54', '2022-06-02 08:56:54'),
(6, 'Honda', 1, 0, '2022-06-02 08:57:05', '2022-06-02 08:57:05'),
(7, 'Nissan', 1, 0, '2022-06-02 08:58:03', '2022-06-02 08:58:03'),
(8, 'Jeep', 1, 0, '2022-06-02 08:58:15', '2022-06-02 08:58:15'),
(9, 'Volkswagen', 1, 0, '2022-06-02 08:58:22', '2022-06-02 08:58:22'),
(10, 'Volvo', 1, 0, '2022-06-02 08:58:30', '2022-06-02 08:58:30'),
(11, 'Audi', 1, 0, '2022-06-02 08:58:39', '2022-06-02 08:58:39'),
(12, 'Land Rover', 1, 0, '2022-06-02 08:58:54', '2022-06-02 08:58:54'),
(13, 'Rolls Royce', 1, 0, '2022-06-02 08:59:18', '2022-06-02 08:59:18'),
(14, 'Bugati', 1, 0, '2022-06-02 08:59:27', '2022-06-02 08:59:27'),
(15, 'Porsche', 1, 0, '2022-06-02 08:59:40', '2022-06-02 08:59:40'),
(16, 'BMW', 1, 0, '2022-06-02 08:59:49', '2022-06-02 08:59:49'),
(17, 'Tesla', 1, 0, '2022-06-02 08:59:58', '2022-06-02 08:59:58');

-- --------------------------------------------------------

--
-- Table structure for table `car_type_list`
--

CREATE TABLE `car_type_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `car_type_list`
--

INSERT INTO `car_type_list` (`id`, `name`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 'Sedan', 1, 0, '2022-06-02 09:13:24', '2022-06-02 09:13:24'),
(2, 'Coupe', 1, 0, '2022-06-02 09:13:51', '2022-06-02 09:13:51'),
(3, 'Sports', 1, 0, '2022-06-02 09:14:00', '2022-06-02 09:14:00'),
(4, 'Station Wagon', 1, 0, '2022-06-02 09:14:28', '2022-06-02 09:14:28'),
(5, 'Hatchback', 1, 0, '2022-06-02 09:14:42', '2022-06-02 09:14:42'),
(6, 'Sports-Utility Vehicle (SUV)', 1, 0, '2022-06-02 09:15:13', '2022-06-02 09:15:13'),
(7, 'Minivan', 1, 0, '2022-06-02 09:15:25', '2022-06-02 09:15:25'),
(8, 'Pickup Truck ', 1, 0, '2022-06-02 09:15:43', '2022-06-02 09:15:43'),
(9, 'test - updated', 1, 1, '2022-06-02 09:16:19', '2022-06-02 09:16:36');

-- --------------------------------------------------------

--
-- Table structure for table `model_list`
--

CREATE TABLE `model_list` (
  `id` int(30) NOT NULL,
  `brand_id` int(30) NOT NULL,
  `model` text NOT NULL,
  `engine_type` text NOT NULL,
  `transmission_type` text NOT NULL,
  `car_type_id` int(30) NOT NULL,
  `technology` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `model_list`
--

INSERT INTO `model_list` (`id`, `brand_id`, `model`, `engine_type`, `transmission_type`, `car_type_id`, `technology`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(2, 2, 'Wigo 1.0 E MT', 'Gasoline', 'Manual (2WD) (5-Speed)', 5, 'Sample Only', 1, 0, '2022-06-02 09:49:08', '2022-06-02 09:52:44'),
(3, 16, 'test', 'test', 'test', 5, 'test', 1, 1, '2022-06-02 13:31:30', '2022-06-02 13:31:35'),
(4, 2, 'Corolla', 'Petrol', 'Automatic', 1, 'Sample', 1, 0, '2025-04-25 18:07:05', '2025-04-25 18:07:05'),
(5, 16, 'X5', 'Diesel', 'Automatic', 6, 'Sample', 1, 0, '2025-04-25 18:07:05', '2025-04-25 18:07:05'),
(6, 1, 'C-Class', 'Hybrid', 'Automatic', 1, 'Sample', 1, 0, '2025-04-25 18:07:05', '2025-04-25 18:07:05');

-- --------------------------------------------------------

--
-- Table structure for table `system_info`
--

CREATE TABLE `system_info` (
  `id` int(30) NOT NULL,
  `meta_field` text NOT NULL,
  `meta_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_info`
--

INSERT INTO `system_info` (`id`, `meta_field`, `meta_value`) VALUES
(1, 'name', 'Auto Dealer Management System'),
(6, 'short_name', 'ADMS - PHP'),
(11, 'logo', 'uploads/logo.png?v=1654130795'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/cover.png?v=1654130796'),
(17, 'phone', '456-987-1231'),
(18, 'mobile', '09123456987 / 094563212222 '),
(19, 'email', 'info@sample.com'),
(20, 'address', '7087 Henry St. Clifton Park, NY 12065 - updated address');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_list`
--

CREATE TABLE `transaction_list` (
  `id` int(30) NOT NULL,
  `vehicle_id` int(30) NOT NULL,
  `agent_name` text NOT NULL,
  `firstname` text NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` text NOT NULL,
  `sex` varchar(20) NOT NULL,
  `dob` date NOT NULL,
  `contact` text NOT NULL,
  `email` text DEFAULT NULL,
  `address` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_list`
--

INSERT INTO `transaction_list` (`id`, `vehicle_id`, `agent_name`, `firstname`, `middlename`, `lastname`, `sex`, `dob`, `contact`, `email`, `address`, `date_created`, `date_updated`) VALUES
(4, 2, 'Mark Cooper', 'John', 'D', 'Smith', 'Male', '1997-06-23', '09123456789', 'jsmith@sample.com', 'Sample Only', '2022-06-02 13:40:37', '2022-06-02 13:40:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `password`) VALUES
(1, 'victor munene', 'victornesh123@gmail.com', 'victornesh123@gmail.com', '0792225400', '$2y$10$GkZuCjzp6lel0VZ9sTJD4.3yBNrP05xKXewOsNafi46IQpbHzas/e');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `make` varchar(50) NOT NULL,
  `model` varchar(50) NOT NULL,
  `year` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `mileage` int(11) DEFAULT NULL,
  `variant` varchar(100) DEFAULT NULL,
  `engine_number` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `user_id`, `make`, `model`, `year`, `price`, `description`, `created_at`, `mileage`, `variant`, `engine_number`) VALUES
(1, 1, 'Nissan Murano', 'Nissan', 2020, 20000.00, '', '2025-04-25 18:03:11', NULL, NULL, NULL),
(2, 1, 'jyuhmghnfb', 'gfngdhjnb ', 2023, 2345.00, 'hgbn bn', '2025-04-25 18:06:11', NULL, NULL, NULL),
(3, 1, 'jyuhmghnfb', 'gfngdhjnb ', 2023, 2345.00, 'hgbn bn', '2025-04-25 18:06:39', NULL, NULL, NULL),
(5, 1, 'Camaro', 'Honda', 2018, 23456.00, 'engine capacity\r\nhorse power', '2025-04-26 06:46:28', NULL, NULL, NULL),
(6, 1, 'BMW', 'BMW', 2018, 345678.00, 'ENGINE POWER\r\nMODEL NO \r\nSERIAL NUMBER', '2025-04-26 07:37:08', NULL, NULL, NULL),
(7, 1, 'Rolls Royce ', 'Phantom', 2022, 2344556.00, '', '2025-04-26 08:24:23', NULL, 'luxury', '2314245464');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_list`
--

CREATE TABLE `vehicle_list` (
  `id` int(30) NOT NULL,
  `model_id` int(30) NOT NULL,
  `mv_number` text NOT NULL,
  `plate_number` text NOT NULL,
  `variant` text NOT NULL,
  `mileage` varchar(20) NOT NULL,
  `engine_number` varchar(100) NOT NULL,
  `chasis_number` varchar(100) NOT NULL,
  `price` float(12,2) NOT NULL DEFAULT 0.00,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = Available,\r\n1=Sold',
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `year` int(11) NOT NULL DEFAULT 2000
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_list`
--

INSERT INTO `vehicle_list` (`id`, `model_id`, `mv_number`, `plate_number`, `variant`, `mileage`, `engine_number`, `chasis_number`, `price`, `status`, `delete_flag`, `date_created`, `date_updated`, `year`) VALUES
(1, 2, '6231415', 'GBN-2306', 'Gray Metalic', '10000', '10141997', '19971507', 450000.00, 0, 0, '2022-06-02 10:52:13', '2025-04-25 19:48:17', 2020),
(2, 2, '123654', 'CDM-9879', 'Red', '15879', '78954623', '5646897546', 425000.00, 1, 0, '2022-06-02 10:58:04', '2025-04-25 19:48:17', 2019),
(3, 4, 'test', 'test', 'test', 'test', 'test', 'test', 123.00, 0, 1, '2022-06-02 13:31:59', '2025-04-25 20:41:57', 2000);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_photos`
--

CREATE TABLE `vehicle_photos` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_photos`
--

INSERT INTO `vehicle_photos` (`id`, `vehicle_id`, `photo_path`, `created_at`) VALUES
(1, 5, 'uploads/vehicles/680c8144e9606.jpeg', '2025-04-26 06:46:29'),
(2, 6, 'uploads/vehicles/680c8d2514159.jpeg', '2025-04-26 07:37:09'),
(3, 7, 'uploads/vehicles/680c98371e69c.jpeg', '2025-04-26 08:24:23');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brand_list`
--
ALTER TABLE `brand_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `car_type_list`
--
ALTER TABLE `car_type_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_list`
--
ALTER TABLE `model_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `brand_id` (`brand_id`),
  ADD KEY `car_type_id` (`car_type_id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction_list`
--
ALTER TABLE `transaction_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `vehicle_list`
--
ALTER TABLE `vehicle_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `model_id` (`model_id`);

--
-- Indexes for table `vehicle_photos`
--
ALTER TABLE `vehicle_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vehicle_id` (`vehicle_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brand_list`
--
ALTER TABLE `brand_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `car_type_list`
--
ALTER TABLE `car_type_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `model_list`
--
ALTER TABLE `model_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transaction_list`
--
ALTER TABLE `transaction_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vehicle_list`
--
ALTER TABLE `vehicle_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicle_photos`
--
ALTER TABLE `vehicle_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `model_list`
--
ALTER TABLE `model_list`
  ADD CONSTRAINT `brand_id_fk_ml` FOREIGN KEY (`brand_id`) REFERENCES `brand_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `car_type_id_fk_ml` FOREIGN KEY (`car_type_id`) REFERENCES `car_type_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `transaction_list`
--
ALTER TABLE `transaction_list`
  ADD CONSTRAINT `vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vehicle_list`
--
ALTER TABLE `vehicle_list`
  ADD CONSTRAINT `model_id_fk_vl` FOREIGN KEY (`model_id`) REFERENCES `model_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `vehicle_photos`
--
ALTER TABLE `vehicle_photos`
  ADD CONSTRAINT `vehicle_photos_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
