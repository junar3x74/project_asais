-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2024 at 10:50 AM
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
-- Database: `project_asais`
--

-- --------------------------------------------------------

--
-- Table structure for table `assignments`
--

CREATE TABLE `assignments` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `due_date` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assignments`
--

INSERT INTO `assignments` (`id`, `title`, `description`, `teacher_id`, `due_date`, `created_at`, `updated_at`) VALUES
(1, 'test', '1+1', 10, '2024-12-16 00:00:00', '2024-12-11 15:29:04', '2024-12-11 15:29:04'),
(2, 'boboka', '10+10', 10, '2024-12-26 00:00:00', '2024-12-11 16:41:30', '2024-12-11 16:41:30'),
(3, 'how to kill', 'kill a person', 10, '2024-12-18 00:00:00', '2024-12-11 17:10:33', '2024-12-11 17:10:33');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `content` text DEFAULT NULL,
  `status` enum('submitted','graded') DEFAULT 'submitted',
  `grade` float DEFAULT NULL,
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `assignment_id`, `student_id`, `submission_date`, `content`, `status`, `grade`, `feedback`) VALUES
(1, 1, 11, '2024-12-11 09:07:22', '2', 'graded', 100, 'bolok nm'),
(2, 2, 11, '2024-12-11 09:07:45', '20', 'graded', 100, 'fuck you'),
(3, 3, 11, '2024-12-11 09:11:17', 'shoot him man shoot him', 'graded', 100, 'very good man');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('teacher','student') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `otp` varchar(6) DEFAULT NULL,
  `otp_expiry` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `email`, `password`, `role`, `created_at`, `otp`, `otp_expiry`) VALUES
(1, 'junar', 'junar@gmail.com', '$2y$10$UxIE7K7JU4/VNHTtueqTEumM5jpYwJ.VGwV9s3.Cdk4oCwlgINzVe', 'student', '2024-12-08 11:34:20', NULL, NULL),
(2, 'waiting', 'ambot@gmail.com', '$2y$10$N9rZrRfqkyRJfg1sr5linO83WgasN30/xrdjFmkIDl1ZFRZq/L.bq', 'student', '2024-12-08 11:58:10', NULL, NULL),
(4, 'manman', 'kil@gmail.com', '$2y$10$y1NqqFd2MM1StM8jfADgDeTmzHs3gIDo0Qwo6TWwFUH2UBFALQogC', 'student', '2024-12-08 12:03:11', NULL, NULL),
(5, 'run', 'adxwxtxya@gmail.com', '$2y$10$PoNJ2.bFexiLEgJ5pvhHJeF6LT7wfI1HWEPOfboq6oXTCQ1S/qDRe', 'student', '2024-12-08 12:04:53', NULL, NULL),
(6, 'huh', 'ejpadullo@yahoo.com', '$2y$10$vnbjt6x8n1z2GjO39EKzx.mlhtZ1ivD43stotlQ/lYyMVsV3KwQPC', 'student', '2024-12-08 12:06:07', NULL, NULL),
(7, 'hello there', 'bobo@gmail.com', '$2y$10$WA.VU8xcauwedkgKW9ZgruHR1y/9fM.nmxSx.P5LyCI9DQqOnTIZe', 'student', '2024-12-08 12:18:20', NULL, NULL),
(8, 'Emmanuel Jose', 'aa@gmail.com', '$2y$10$0LX3hMO8fSz9/LSSjfTuOexKQQ9uGZwGNPwHppEBAb/kza6qu2Ffq', 'student', '2024-12-08 12:20:17', NULL, NULL),
(9, 'lol', 'lol@gmail.com', '$2y$10$PAAqfnSHTrcuLJ12urTwgeGi1TDdI4T0bTfnHvT0laG4Dvr.z5P76', 'student', '2024-12-08 12:24:07', NULL, NULL),
(10, 'popo', 'popo@gmail.com', '$2y$10$9hiE8FDqBUkhmnu3TrNVcO4fqUiIyEUSj39EGs8oKvpksasadGK7G', 'teacher', '2024-12-08 12:34:42', NULL, NULL),
(11, 'junar afable', 'junarafable14@gmail.com', '$2y$10$kF1nEDBMB6UIYbuUEqfuQuQfZR2EFkeGcF8AofOdiV4D88FIWKEwC', 'student', '2024-12-08 13:53:21', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assignments`
--
ALTER TABLE `assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `assignment_id` (`assignment_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`fname`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assignments`
--
ALTER TABLE `assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assignments`
--
ALTER TABLE `assignments`
  ADD CONSTRAINT `assignments_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_ibfk_1` FOREIGN KEY (`assignment_id`) REFERENCES `assignments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submissions_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
