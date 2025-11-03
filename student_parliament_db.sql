-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 29, 2025 at 07:33 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_parliament_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `posted_by` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `posted_at` datetime DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `announcements`
--

INSERT INTO `announcements` (`id`, `posted_by`, `title`, `content`, `posted_at`, `status`) VALUES
(1, 'Kobe Deacosta', 'Green Campus', 'Today nag start na tayong maglagay ng mga disfenser sa bawat sulok ng ating dalubhasaan. Goal ng project na ito ang makatulong sa mga students para maging hydrated, ganon din para makaiwas sa mga plastic bottle.', '2025-10-29 02:35:32', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `event_id` int(11) NOT NULL,
  `scan_time` datetime DEFAULT current_timestamp(),
  `status` enum('Present','Absent') DEFAULT 'Present',
  `date` date DEFAULT NULL,
  `am_in` datetime DEFAULT NULL,
  `am_out` datetime DEFAULT NULL,
  `pm_in` datetime DEFAULT NULL,
  `pm_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `user_id`, `name`, `department`, `course`, `event_id`, `scan_time`, `status`, `date`, `am_in`, `am_out`, `pm_in`, `pm_out`) VALUES
(39, 20, 'Juan Dela Cruz', 'BSIS', NULL, 11, '2025-10-28 20:31:26', 'Present', '2025-10-28', '2025-10-28 20:22:36', '2025-10-28 20:31:17', '2025-10-28 20:31:26', NULL),
(42, 22, 'Merry Joy Villaruel Bayta', 'BLIS', NULL, 9, '2025-10-28 20:39:59', 'Present', '2025-10-28', '2025-10-28 20:39:04', '2025-10-28 20:39:31', '2025-10-28 20:39:59', NULL),
(43, 20, 'Juan Dela Cruz', 'BSIS', NULL, 9, '2025-10-28 20:40:36', 'Present', '2025-10-28', '2025-10-28 20:40:27', NULL, NULL, '2025-10-28 20:40:36'),
(44, 23, 'Ghian Carlo Latosa', 'BSTM', NULL, 9, '2025-10-28 20:53:58', 'Present', '2025-10-28', NULL, NULL, '2025-10-28 20:53:58', NULL),
(45, 20, 'Juan Dela Cruz', 'BSIS', NULL, 12, '2025-10-28 21:18:04', 'Present', '2025-10-28', NULL, '2025-10-28 21:17:57', '2025-10-28 21:18:04', NULL),
(46, 24, 'Jose Dela Cruz', 'BSTM', NULL, 12, '2025-10-28 22:02:10', 'Present', '2025-10-28', NULL, NULL, NULL, '2025-10-28 22:02:10');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `department_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`) VALUES
(16, 'BSIS'),
(17, 'BLIS'),
(18, 'BSTM');

-- --------------------------------------------------------

--
-- Table structure for table `fines`
--

CREATE TABLE `fines` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `missing_scans` int(11) DEFAULT 0,
  `total_fine` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fines`
--

INSERT INTO `fines` (`id`, `user_id`, `event_id`, `date`, `missing_scans`, `total_fine`, `created_at`) VALUES
(2, 22, 9, '2025-10-28', 1, '50.00', '2025-10-28 19:39:59'),
(3, 20, 9, '2025-10-28', 2, '100.00', '2025-10-28 19:40:36'),
(4, 23, 9, '2025-10-28', 3, '150.00', '2025-10-28 19:53:58'),
(5, 20, 12, '2025-10-28', 2, '100.00', '2025-10-28 20:18:04'),
(6, 24, 12, '2025-10-28', 3, '150.00', '2025-10-28 21:02:10');

-- --------------------------------------------------------

--
-- Table structure for table `institutional_events`
--

CREATE TABLE `institutional_events` (
  `id` int(11) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `event_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','inactive') DEFAULT 'inactive'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `institutional_events`
--

INSERT INTO `institutional_events` (`id`, `event_name`, `department`, `event_date`, `description`, `created_at`, `status`) VALUES
(2, 'Mr. and Ms. Tagislakasan 2025', NULL, '2025-09-30', '', '2025-10-21 03:52:06', 'inactive'),
(4, 'Buwan ng Wika', NULL, '2025-08-29', '', '2025-10-21 05:04:20', 'inactive'),
(8, 'Intramurals 2025', NULL, '2025-10-01', 'Oct 1-3, 2025', '2025-10-26 07:26:14', 'inactive'),
(9, 'Year End Celebration', NULL, '2025-12-31', '', '2025-10-26 07:52:47', 'inactive'),
(11, 'sample', NULL, '2025-10-29', '', '2025-10-28 02:04:50', 'inactive'),
(12, 'Hearts Day', NULL, '2026-02-14', '', '2025-10-28 20:17:42', 'inactive');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `student_name` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','admin') DEFAULT 'student',
  `qr_code` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `id_number` varchar(50) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `department`, `password`, `role`, `qr_code`, `created_at`, `id_number`, `department_id`) VALUES
(15, 'Kobe Deacosta', 'kobedeacosta@gmail.com', 'BSIS', '$2y$10$GL5PIBoPGPPK9/kM.gWAfO9B7g153joj0GJ6WdSNYCiMcHa.wyiLS', 'admin', NULL, '2025-10-27 06:13:15', '18-01933', 1),
(20, 'Juan Dela Cruz', 'delacruz@gmail.com', 'BSIS', '$2y$10$XZzm0eH8OOmls1JFA8/xy.F1bAxUQBsgHOr.evo/4i2N4HHnkjrmm', 'student', 'qrcodes/student_20.png', '2025-10-28 01:50:39', '20-01934', 1),
(21, 'Reneir Manongsong', 'reneirmanongsong1@gmail.com', 'BSIS', '$2y$10$0GMo0UFyAxeHqw3Xs90z/.HQ/2..hNwm91/ldAdrNWFf21G/jPt3K', 'student', 'qrcodes/student_21.png', '2025-10-28 10:37:42', '23-06060', 1),
(22, 'Merry Joy Villaruel Bayta', 'merryjoybayta@gmail.com', 'BLIS', '$2y$10$P6GReBRHDqXPDYgPLgwACece662X7E0JQighm.FdH75cnpVfz75DG', 'student', 'qrcodes/student_22.png', '2025-10-28 13:01:45', '25-07174', 2),
(23, 'Ghian Carlo Latosa', 'latosa@gmail.com', 'BSTM', '$2y$10$.hjvptk54IWAyz4vvwNXfO6t/tUnoq.kP29ike.xyDuZ4gbLxMvku', 'student', 'qrcodes/student_23.png', '2025-10-28 17:01:09', '25-07176', 3),
(24, 'Jose Dela Cruz', 'juan@gmail.com', 'BSTM', '$2y$10$fjHXRu93QlpQBKE25dXrhObCnIpRT0kRgQXwzAsOfALcd4wrF4pDS', 'student', 'qrcodes/student_24.png', '2025-10-28 20:57:54', '19-0985', 18),
(25, 'Kyla Mae Panotes', 'kyla@gmail.com', 'BSIS', '$2y$10$LunnJ/KdPIclEdpuWozEb.RFthOhagMB0hAMCINzlVQ6kIpYgwW6C', 'student', 'qrcodes/student_25.png', '2025-10-29 04:54:27', '23-06016', 16),
(26, 'Luis Adam Dela Cruz', 'luisadamdelacruz8@gmail.com', 'BSIS', '$2y$10$JKn1p962M9cA7WbcXQiaSeyaCqmNQl32Kc.6boKwU96E0qDV457Lu', 'student', 'qrcodes/student_26.png', '2025-10-29 05:26:37', '23-05969', 16);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`user_id`,`event_id`,`date`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fines`
--
ALTER TABLE `fines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `institutional_events`
--
ALTER TABLE `institutional_events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
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
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `fines`
--
ALTER TABLE `fines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `institutional_events`
--
ALTER TABLE `institutional_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `institutional_events` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fines`
--
ALTER TABLE `fines`
  ADD CONSTRAINT `fines_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fines_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `institutional_events` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
