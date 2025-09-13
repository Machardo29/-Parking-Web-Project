-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 13, 2025 at 06:44 PM
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
-- Database: `car_parking`
--

-- --------------------------------------------------------

--
-- Table structure for table `parking_spaces`
--

CREATE TABLE `parking_spaces` (
  `id` int(11) NOT NULL,
  `space_number` varchar(10) NOT NULL,
  `vehicle_type` enum('two-wheeler','four-wheeler') NOT NULL,
  `hourly_rate` decimal(6,2) NOT NULL,
  `status` enum('available','reserved','occupied') DEFAULT 'available',
  `image` varchar(255) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `owner_id` int(11) NOT NULL,
  `total_spaces` int(11) NOT NULL DEFAULT 1,
  `available_spaces` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `parking_spaces`
--

INSERT INTO `parking_spaces` (`id`, `space_number`, `vehicle_type`, `hourly_rate`, `status`, `image`, `location`, `owner_id`, `total_spaces`, `available_spaces`, `created_at`) VALUES
(1, '001', 'two-wheeler', 50.00, 'available', 'assets/images/parking.jpg', 'Nairobi CBD', 2, 20, 18, '2025-07-04 16:56:44'),
(2, '002', 'four-wheeler', 100.00, 'available', 'assets/images/image 2.jpg', 'Nairobi CBD', 2, 35, 28, '2025-07-04 17:08:57');

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `space_id` int(11) NOT NULL,
  `license_plate` varchar(20) NOT NULL,
  `vehicle_type` enum('two-wheeler','four-wheeler') NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL,
  `total_cost` decimal(8,2) DEFAULT NULL,
  `status` enum('active','completed','cancelled') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `space_id`, `license_plate`, `vehicle_type`, `start_time`, `end_time`, `total_cost`, `status`, `created_at`) VALUES
(1, 1, 1, 'KDU 111P', 'two-wheeler', '2025-07-05 10:00:00', '2025-07-05 14:30:00', NULL, 'active', '2025-07-04 16:59:48'),
(2, 3, 2, 'KDV 222Q', 'four-wheeler', '2025-07-07 09:00:00', '2025-07-07 12:00:00', NULL, 'active', '2025-07-04 17:14:19'),
(3, 4, 2, 'KDU 990I', 'four-wheeler', '2025-07-23 14:00:00', '2025-07-23 17:00:00', NULL, 'active', '2025-07-23 09:47:28'),
(4, 5, 2, 'KDO 998P', 'four-wheeler', '2025-07-24 09:00:00', '2025-07-23 12:00:00', NULL, 'cancelled', '2025-07-23 12:15:18'),
(5, 5, 2, 'KDO 998P', 'four-wheeler', '2025-07-24 09:00:00', '2025-07-24 11:00:00', NULL, 'active', '2025-07-23 12:17:02'),
(6, 6, 2, 'KDZ 1824A', 'four-wheeler', '2025-08-29 07:50:00', '2025-08-29 16:30:00', NULL, 'cancelled', '2025-08-28 12:19:28'),
(7, 6, 2, 'KDI VITA1', 'four-wheeler', '2025-08-29 11:00:00', '2025-08-29 15:40:00', NULL, 'active', '2025-08-28 12:22:37'),
(8, 2, 1, 'KDM 2341O', 'two-wheeler', '2025-09-01 08:30:00', '2025-09-01 14:20:00', NULL, 'active', '2025-08-28 12:29:06'),
(9, 7, 2, 'KDS 555I', 'four-wheeler', '2025-09-02 07:45:00', '2025-09-02 17:30:00', NULL, 'active', '2025-09-01 12:32:22');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('user','admin','owner') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `role`, `created_at`) VALUES
(1, 'Mike', '$2y$10$e9DQqkI40tw1R4mO/TRmbO8A2muLpGEoo9GEjZmy56Nfbyr00tGZi', 'mike@outlook.com', 'MIke polo', '0712563498', 'user', '2025-07-04 16:49:43'),
(2, 'Raph', '$2y$10$n/GR/TDjVdYJbVW0GxNSGulRnI7MZ1rGVS6HMLjGwnXUwPSrW0INa', 'raph@outlook.com', 'Raphinha santos', '078912643', 'owner', '2025-07-04 16:51:58'),
(3, 'Jane', '$2y$10$.pppXXHi7RyDcl3Z5nHEi.a6chFeHl2L8OvAiBuLbA5UD/A0b3T4u', 'jane@kawasaki.hot', 'Janepi', '0724674509', 'user', '2025-07-04 17:12:21'),
(4, 'Jackie', '$2y$10$ZvF0yRLesitII68KVZpEU.mqgMH5giBRb0liAOOOT3xpGJiJhE3E.', 'jackie@outlook.com', 'Jackie Chan', '0745678011', 'user', '2025-07-23 09:45:10'),
(5, 'Mark', '$2y$10$YvHBrt3nifn7QDOIGDbpqudQYCqkHdormhSgPL7VUn4MFPYtMnIpW', 'mark@gmail.com', 'Mark polo', '0712346789', 'user', '2025-07-23 12:13:39'),
(6, 'Johhny 10', '$2y$10$kk7F4R8deVN9SzoBM91uQ.I9yClx7pwLCO5v.bmt3LFA5Esr3vFCG', 'joni@hotmail.com', 'Johhny MInter', '0119783476', 'user', '2025-08-28 11:51:23'),
(7, 'Nick12', '$2y$10$.bIbrl3Qrvvk6rAz2TDHL.2SOjbyZZStABsXiy1eUbqH3.va7X0Ta', 'nick@outlook.com', 'Nicholas Jamfe', '0789543200', 'user', '2025-09-01 12:29:26'),
(8, 'Bobby1', '$2y$10$Qx5SOQ1EQrKscdtcmfQ2L.RJKOCTpC5b2T5kO5Mt9gH2Wa2wCFBhW', 'bobby@outlook.com', 'Bobby Lady', '0722324924', 'user', '2025-09-02 10:50:21'),
(9, 'John', '$2y$10$Y.cCrPZefg1hldHtNuU09.cTGuTlLySZhxzVS8XavzJBSnsECBHO6', 'John@outlook.com', 'Johnny Blaze', '0112256554', 'owner', '2025-09-02 10:54:44');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `parking_spaces`
--
ALTER TABLE `parking_spaces`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `space_number` (`space_number`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `space_id` (`space_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `parking_spaces`
--
ALTER TABLE `parking_spaces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `parking_spaces`
--
ALTER TABLE `parking_spaces`
  ADD CONSTRAINT `parking_spaces_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`space_id`) REFERENCES `parking_spaces` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
