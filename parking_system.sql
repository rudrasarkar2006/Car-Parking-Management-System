-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 27, 2026 at 04:43 PM
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
-- Database: `parking_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `logged_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`log_id`, `user_id`, `action`, `details`, `logged_at`) VALUES
(1, 2, 'Vehicle checked in', 'Plate: asdasdcc, Slot: A1', '2026-08-27 11:23:21'),
(2, 2, 'Vehicle checked in', 'Plate: KKlpkl, Slot: A2', '2026-08-27 11:23:38'),
(3, 1, 'User updated', 'Edited user: Surja (rudrasarkar634@gmail.com)', '2026-08-27 11:39:27'),
(4, 1, 'User updated', 'Edited user: Surja (rudrasarkar634@gmail.com)', '2026-08-27 11:39:35'),
(5, 16, 'Password reset', 'User reset their own password via email code', '2026-08-27 11:47:10'),
(6, 19, 'Password reset', 'User reset their own password via email code', '2026-08-27 14:38:59'),
(7, 2, 'Vehicle checked in', 'Plate: DHAKA-1933, Slot: B1', '2026-08-27 14:39:27'),
(8, 2, 'Slot request approved', 'Request ID: 14', '2026-08-27 14:40:17'),
(9, 2, 'Slot request rejected', 'Request ID: 13', '2026-08-27 14:40:18'),
(10, 2, 'Vehicle checked out', 'Plate: DHAKA-1933, Amount: Tk 10, Method: ', '2026-08-27 14:40:30'),
(11, 1, 'Slot added', 'Slot: Z1', '2026-08-27 14:41:38'),
(12, 1, 'User updated', 'Edited user: Dev Rudra (rudrasarkar01733@yahoo.com)', '2026-08-27 14:42:32');

-- --------------------------------------------------------

--
-- Table structure for table `parking_sessions`
--

CREATE TABLE `parking_sessions` (
  `session_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `entry_time` datetime NOT NULL,
  `exit_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_sessions`
--

INSERT INTO `parking_sessions` (`session_id`, `vehicle_id`, `slot_id`, `staff_id`, `entry_time`, `exit_time`) VALUES
(1, 1, 1, 2, '2026-08-06 13:01:48', '2026-08-06 09:02:37'),
(2, 1, 1, 2, '2026-08-06 13:26:58', '2026-08-06 17:43:28'),
(3, 2, 2, 2, '2026-08-06 13:27:27', '2026-08-06 09:28:19'),
(4, 3, 5, 2, '2026-08-06 17:13:51', '2026-08-06 13:14:07'),
(5, 4, 3, 2, '2026-08-06 17:52:06', '2026-08-06 17:43:11'),
(6, 5, 5, 2, '2026-08-06 18:18:08', '2026-08-06 17:42:56'),
(7, 6, 3, 2, '2026-08-06 21:44:50', '2026-08-09 18:33:45'),
(8, 7, 1, 2, '2026-08-07 20:44:29', '2026-08-09 18:34:11'),
(9, 8, 1, 2, '2026-08-09 22:37:46', '2026-08-09 18:38:37'),
(10, 9, 3, 2, '2026-08-10 22:08:57', '2026-08-27 11:29:29'),
(11, 10, 1, 2, '2026-08-10 22:09:21', '2026-08-12 13:16:12'),
(12, 11, 5, 2, '2026-08-10 22:09:32', '2026-08-12 13:54:24'),
(13, 12, 6, 2, '2026-08-10 22:19:31', '2026-08-10 18:21:42'),
(14, 13, 2, 2, '2026-08-12 17:04:22', '2026-08-27 11:29:18'),
(15, 14, 1, 2, '2026-08-12 22:46:21', '2026-08-12 18:47:00'),
(16, 15, 4, 2, '2026-08-13 12:45:54', '2026-08-13 08:49:55'),
(17, 1, 5, 2, '2026-08-13 13:16:29', '2026-08-27 11:29:08'),
(18, 16, 1, 2, '2026-08-14 22:54:02', '2026-08-14 18:58:21'),
(19, 17, 1, 2, '2026-08-27 17:21:33', NULL),
(20, 17, 1, 2, '2026-08-27 17:23:21', NULL),
(21, 18, 2, 2, '2026-08-27 17:23:38', NULL),
(22, 19, 3, 2, '2026-08-27 20:39:27', '2026-08-27 16:40:30');

-- --------------------------------------------------------

--
-- Table structure for table `parking_slots`
--

CREATE TABLE `parking_slots` (
  `slot_id` int(11) NOT NULL,
  `slot_number` varchar(20) NOT NULL,
  `type_id` int(11) NOT NULL,
  `status` enum('available','occupied') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parking_slots`
--

INSERT INTO `parking_slots` (`slot_id`, `slot_number`, `type_id`, `status`) VALUES
(1, 'A1', 1, 'occupied'),
(2, 'A2', 1, 'occupied'),
(3, 'B1', 2, 'available'),
(4, 'B2', 2, 'available'),
(5, 'C1', 3, 'available'),
(6, 'C2', 3, 'available'),
(7, 'A3', 1, 'available'),
(9, 'X1', 1, 'available'),
(10, 'Z1', 2, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `session_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('cash','card','mobile_banking') NOT NULL,
  `paid_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `session_id`, `amount`, `method`, `paid_at`) VALUES
(1, 1, 20.00, 'cash', '2026-08-06 07:02:37'),
(2, 3, 20.00, 'cash', '2026-08-06 07:28:19'),
(3, 4, 30.00, 'cash', '2026-08-06 11:14:07'),
(4, 6, 30.00, 'cash', '2026-08-06 15:42:56'),
(5, 5, 10.00, 'cash', '2026-08-06 15:43:11'),
(6, 2, 100.00, 'cash', '2026-08-06 15:43:28'),
(7, 7, 690.00, 'cash', '2026-08-09 16:33:45'),
(8, 8, 920.00, 'cash', '2026-08-09 16:34:11'),
(9, 9, 20.00, 'cash', '2026-08-09 16:38:37'),
(10, 13, 30.00, 'cash', '2026-08-10 16:21:42'),
(11, 11, 800.00, 'cash', '2026-08-12 11:16:12'),
(12, 12, 1200.00, 'cash', '2026-08-12 11:54:24'),
(13, 15, 20.00, 'cash', '2026-08-12 16:47:00'),
(14, 16, 10.00, 'cash', '2026-08-13 06:49:55'),
(15, 18, 20.00, 'cash', '2026-08-14 16:58:21'),
(16, 17, 6700.00, 'cash', '2026-08-27 09:29:08'),
(17, 14, 7100.00, 'cash', '2026-08-27 09:29:18'),
(18, 10, 3980.00, 'cash', '2026-08-27 09:29:29'),
(19, 22, 10.00, 'cash', '2026-08-27 14:40:30');

-- --------------------------------------------------------

--
-- Table structure for table `slot_requests`
--

CREATE TABLE `slot_requests` (
  `request_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `responded_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `slot_requests`
--

INSERT INTO `slot_requests` (`request_id`, `customer_id`, `slot_id`, `status`, `requested_at`, `responded_at`) VALUES
(1, 6, 6, 'rejected', '2026-08-12 11:34:49', '2026-08-12 11:35:04'),
(2, 14, 1, 'approved', '2026-08-12 16:44:20', '2026-08-12 16:45:23'),
(3, 6, 5, 'approved', '2026-08-12 16:55:21', '2026-08-12 16:55:47'),
(4, 6, 4, 'approved', '2026-08-12 16:55:25', '2026-08-12 16:55:46'),
(5, 6, 6, 'approved', '2026-08-12 16:55:28', '2026-08-12 16:55:45'),
(6, 6, 1, 'approved', '2026-08-12 16:57:46', '2026-08-27 09:29:42'),
(7, 6, 7, 'approved', '2026-08-12 16:57:49', '2026-08-27 09:29:41'),
(8, 6, 5, 'approved', '2026-08-12 16:57:51', '2026-08-27 09:29:40'),
(9, 6, 5, 'approved', '2026-08-12 16:57:53', '2026-08-27 09:29:38'),
(10, 17, 4, 'approved', '2026-08-13 06:44:57', '2026-08-13 06:45:22'),
(11, 18, 1, 'approved', '2026-08-14 16:52:48', '2026-08-14 16:53:39'),
(12, 6, 1, 'approved', '2026-08-27 11:20:43', '2026-08-27 11:21:11'),
(13, 18, 7, 'rejected', '2026-08-27 11:25:06', '2026-08-27 14:40:18'),
(14, 19, 3, 'approved', '2026-08-27 14:36:05', '2026-08-27 14:40:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','customer') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Admin User', 'admin@parking.com', '$2y$10$4QNvc6VnwTnj4mgkj8ri/u5G5yeDQo1Ia8N/mo7ltw1RyR25TPjY2', 'admin', '2026-08-06 06:26:11'),
(2, 'Staff One', 'staff@parking.com', '$2y$10$GuxKEYfPpRdqOLhYNkS1ouKrEMLdkQm9vN4Hzr9UnXIxTC/SJV0.W', 'staff', '2026-08-06 06:26:11'),
(3, 'John Customer', 'john@example.com', '$2y$10$ZY.qY9RZxCcrSPpfIbyib.gvbBTctdA4EkBgDzzS0bhmn/X.EeV.C', 'customer', '2026-08-06 07:20:20'),
(4, 'Fatima Customer', 'fatima@example.com', '$2y$10$ElFwv9cxl3VzLWfrnPl5qOcPF8c9zBDyStbHhotm.mi82FKRKg1EW', 'customer', '2026-08-06 07:20:20'),
(5, 'Rudra Sarkar Surja', 'rudra@121.com', '$2y$10$o0TX50FSsxyzTfdF0EnJsemyqPoTWFxhOIl1Wu1jLEFVIIeiyW8Um', 'customer', '2026-08-06 11:51:11'),
(6, 'Sidratul Maam', 'sidratul@tuli.com', '$2y$10$v2.mBTTStuu5NzA0mogbxuZo6YQfjmp8Nd1h00Dap5ugvCzJLIUOe', 'customer', '2026-08-06 12:17:39'),
(7, 'Maimun', 'maimun@usa.com', '$2y$10$Tla.LvWl9Tscsvfsga4tW.U0tllTyDBD08QhLSQQknyDUXHVxacBW', 'customer', '2026-08-06 16:05:23'),
(8, 'Liton', 'liton@usa.com', '$2y$10$X23rGRcZPvjEE/7sGz80O.B.nOMG10nrPFmCk4YD6Ck2D1mzQxCwi', 'customer', '2026-08-06 16:05:48'),
(9, 'Zaif', 'zaif@usa.com', '$2y$10$PwEloB9ly4qtmr9d8GYTyOf9E.ncECM7.6P3FK5ORwcM75tHEt9UK', 'customer', '2026-08-07 14:43:53'),
(10, 'Devil', 'devil@gmail.com', '$2y$10$lcpsIzKZNzVBAfV8Jhfn9O2DKjbs4cZ//jbKI1vjfIXOFhU3qjHmG', 'customer', '2026-08-09 16:36:46'),
(11, 'Naim', 'naim@gmail.com', '$2y$10$jB2NSH7AeWHzrYMuz4bDq.3KNZxNN3D4/4jZgJDNZEiMLCRmyvpGG', 'customer', '2026-08-10 16:00:51'),
(12, 'Albir', 'albir@mail.com', '$2y$10$t/sM6P7mhf1gn8SEVDn3.uSNQXZ5F3ZeF09c2vbkD7S1ySf8hKdhm', 'customer', '2026-08-10 16:17:50'),
(13, 'Udoy', 'udoy@mail.com', '$2y$10$r1ImzN22MtiJdWNVN0pae.AoPD4l0pbETjN5xlRyNanIzD7iM1qYe', 'customer', '2026-08-11 14:52:46'),
(14, 'HAA', 'haa@mail.com', '$2y$10$Rh3/y6PfQxb9sfcHUaAFzOWKs97o5E.NSHGcEDPWLuH6DwEw7Cs/2', 'customer', '2026-08-12 16:43:25'),
(15, 'Rabindra', 'rudraasarkar017@gmail.com', '$2y$10$ff8IooU9MMJEd5vRvVEJmOKNzdNYKIq1OXQ9cgF1Phr3wNr30fNSm', 'customer', '2026-08-13 06:29:17'),
(16, 'Surja', 'rudrasarkar634@gmail.com', '$2y$10$dUJbKsOiba3j/V/UhNou6.vrr78h6om1j87mJInPGdcXAjrTHD5tO', 'customer', '2026-08-13 06:31:16'),
(17, 'Gita', 'rudrasarkar4433@gmail.com', '$2y$10$HqyTsOhqeTXKJFoB.Har4eCQ3xjHQzPn5qBYGM.8aVgVAxujV7I8m', 'customer', '2026-08-13 06:35:41'),
(18, 'Fardin Rahman', 'fardinrahman57@gmail.com', '$2y$10$0dXoA3Le.Zhq5g7b8I/QEenLswZo.tLgFzXWY8q3.tjBc79xpBNj.', 'customer', '2026-08-14 16:46:54'),
(19, 'Dev Rudra', 'rudrasarkar01733@yahoo.com', '$2y$10$06mBd4W7mwvweXx/I.E0OuFCA3uTWfZOiq0xAOI71ur2TTNSyhEea', 'admin', '2026-08-27 14:35:30');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `plate_number` varchar(20) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `plate_number`, `owner_id`, `type_id`) VALUES
(1, 'DHAKA-1234', 2, 1),
(2, 'asdad', 3, 1),
(3, 'Rudra121', 4, 3),
(4, 'Rudra111', 5, 2),
(5, 'South Asia 112', 6, 3),
(6, 'BPATC1', 5, 2),
(7, 'Zaif121', 9, 1),
(8, 'Car1', 10, 1),
(9, 'USA2', 11, 2),
(10, 'USA3', 9, 1),
(11, 'USA4', 7, 3),
(12, 'USA5', 12, 3),
(13, 'Rudra11', 5, 1),
(14, 'HAA1', 14, 1),
(15, 'CTg-123', 17, 2),
(16, 'ANJUF', 18, 1),
(17, 'asdasdcc', 18, 1),
(18, 'KKlpkl', 5, 1),
(19, 'DHAKA-1933', 19, 2);

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_types`
--

CREATE TABLE `vehicle_types` (
  `type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `hourly_rate` decimal(10,2) NOT NULL,
  `max_hours` int(11) NOT NULL DEFAULT 24
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_types`
--

INSERT INTO `vehicle_types` (`type_id`, `type_name`, `hourly_rate`, `max_hours`) VALUES
(1, 'car', 20.00, 97),
(2, 'bike', 10.00, 24),
(3, 'truck', 30.00, 72);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `parking_sessions`
--
ALTER TABLE `parking_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `slot_id` (`slot_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD PRIMARY KEY (`slot_id`),
  ADD UNIQUE KEY `slot_number` (`slot_number`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `session_id` (`session_id`);

--
-- Indexes for table `slot_requests`
--
ALTER TABLE `slot_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `slot_id` (`slot_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`),
  ADD KEY `owner_id` (`owner_id`),
  ADD KEY `type_id` (`type_id`);

--
-- Indexes for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  ADD PRIMARY KEY (`type_id`),
  ADD UNIQUE KEY `type_name` (`type_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `parking_sessions`
--
ALTER TABLE `parking_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `parking_slots`
--
ALTER TABLE `parking_slots`
  MODIFY `slot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `slot_requests`
--
ALTER TABLE `slot_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `vehicle_types`
--
ALTER TABLE `vehicle_types`
  MODIFY `type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `parking_sessions`
--
ALTER TABLE `parking_sessions`
  ADD CONSTRAINT `parking_sessions_ibfk_1` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`),
  ADD CONSTRAINT `parking_sessions_ibfk_2` FOREIGN KEY (`slot_id`) REFERENCES `parking_slots` (`slot_id`),
  ADD CONSTRAINT `parking_sessions_ibfk_3` FOREIGN KEY (`staff_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `parking_slots`
--
ALTER TABLE `parking_slots`
  ADD CONSTRAINT `parking_slots_ibfk_1` FOREIGN KEY (`type_id`) REFERENCES `vehicle_types` (`type_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `parking_sessions` (`session_id`);

--
-- Constraints for table `slot_requests`
--
ALTER TABLE `slot_requests`
  ADD CONSTRAINT `slot_requests_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `slot_requests_ibfk_2` FOREIGN KEY (`slot_id`) REFERENCES `parking_slots` (`slot_id`);

--
-- Constraints for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD CONSTRAINT `vehicles_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `vehicles_ibfk_2` FOREIGN KEY (`type_id`) REFERENCES `vehicle_types` (`type_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
