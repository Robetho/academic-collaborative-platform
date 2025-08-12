-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 17, 2025 at 07:05 PM
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
-- Database: `academic_collaboration_platform`
--

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `faculty_name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`, `description`, `faculty_name`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 'Web Development', 'Full stack web developers', 'Faculty of Information and Technical Education', 1, 0, '2025-03-21 20:15:23', '2025-03-21 20:15:23'),
(2, 'Computer Application', 'This is for All studets', 'Faculty in Medical Doctors', 1, 0, '2025-07-03 12:13:52', '2025-07-03 12:13:52');

-- --------------------------------------------------------

--
-- Table structure for table `comment_list`
--

CREATE TABLE `comment_list` (
  `id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `post_id` int(30) NOT NULL,
  `comment` text NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `audio_path` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `file_path` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comment_list`
--

INSERT INTO `comment_list` (`id`, `user_id`, `post_id`, `comment`, `date_created`, `audio_path`, `video_path`, `file_path`) VALUES
(1, 3, 1, '<p>Oooh, Okay have it.</p>', '2025-03-21 20:16:48', NULL, NULL, NULL),
(2, 2, 1, '<p>Can you give me ??</p>', '2025-03-21 20:17:11', NULL, NULL, NULL),
(3, 3, 1, '<p>Okay, i can sent to you tomorrow if you don\'t mind</p>', '2025-03-21 20:19:13', NULL, NULL, NULL),
(5, 4, 2, '', '2025-07-03 13:20:26', NULL, 'uploads/comments/video/6866596aa3b26.webm', NULL),
(6, 4, 1, '', '2025-07-05 09:10:41', NULL, 'uploads/comments/video/6868c1e192230.webm', NULL),
(9, 2, 1, '&lt;p&gt;Hello&lt;/p&gt;', '2025-07-17 19:39:12', NULL, NULL, 'uploads/comments/files/6879273069691.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `faculty`
--

CREATE TABLE `faculty` (
  `id` int(200) NOT NULL,
  `faculty_name` varchar(255) NOT NULL,
  `status` int(100) NOT NULL DEFAULT 1,
  `delete_flag` int(100) NOT NULL,
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faculty`
--

INSERT INTO `faculty` (`id`, `faculty_name`, `status`, `delete_flag`, `date_updated`) VALUES
(1, 'Faculty of Information and Technical Education', 1, 0, '2025-03-21 17:12:28'),
(2, 'Faculty in Medical Doctors', 1, 0, '2025-07-03 09:02:35');

-- --------------------------------------------------------

--
-- Table structure for table `post_list`
--

CREATE TABLE `post_list` (
  `id` int(30) NOT NULL,
  `user_id` int(30) NOT NULL,
  `category_id` int(30) NOT NULL,
  `title` text NOT NULL,
  `content` text NOT NULL,
  `file_path` text DEFAULT NULL,
  `faculty_name` varchar(255) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `post_list`
--

INSERT INTO `post_list` (`id`, `user_id`, `category_id`, `title`, `content`, `file_path`, `faculty_name`, `program_name`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 2, 1, 'Admin dashboard templates', '<p>Hello, guys, anyone have a admin dashboard templates</p>', NULL, 'Faculty of Information and Technical Education', 'Bachelors Degree In Computer Science', 1, 0, '2025-03-21 20:16:20', '2025-03-21 20:16:20'),
(2, 4, 2, 'Computer Applied', '<p>Hello Guys</p>', NULL, 'Faculty in Medical Doctors', 'Medical Doctors', 1, 0, '2025-07-03 12:18:13', '2025-07-03 12:18:13'),
(3, 2, 2, 'Demo', '<p>Demo Posts</p>', 'uploads/files/1752770798_687928ee8ff8d.pdf', 'Faculty of Information and Technical Education', 'Bachelors Degree In Computer Science', 1, 0, '2025-07-17 19:46:38', '2025-07-17 19:46:38');

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
(1, 'name', 'KAMPALA INTERNATIONAL UNIVERSITY IN TANZANIA '),
(6, 'short_name', 'KIUT'),
(11, 'logo', 'uploads/logo.png?v=1742382347'),
(13, 'user_avatar', 'uploads/user_avatar.jpg'),
(14, 'cover', 'uploads/cover.png?v=1742382935');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `middlename` text DEFAULT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `secret_code` varchar(255) NOT NULL,
  `avatar` text DEFAULT NULL,
  `faculty_name` varchar(255) NOT NULL,
  `program_name` varchar(255) NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='2';

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `middlename`, `lastname`, `username`, `password`, `secret_code`, `avatar`, `faculty_name`, `program_name`, `last_login`, `type`, `date_added`, `date_updated`) VALUES
(1, 'Administrator', '', '', 'admin', '0192023a7bbd73250516f069df18b500', '', 'uploads/avatars/1.png?v=1649834664', '', '', '0000-00-00 00:00:00', 1, '2024-05-23 09:20:45', '2024-05-23 10:08:14'),
(2, 'robetho', 'John', 'Lyando', 'NIT/BCICT/2020/536', '0192023a7bbd73250516f069df18b500', 'KIUT-677-167', NULL, 'Faculty of Information and Technical Education', 'Bachelors Degree In Computer Science', NULL, 2, '2025-03-21 20:13:05', '2025-07-03 12:00:49'),
(3, 'Stellah', 'John', 'Lyando', 'NIT/BCICT/2023/564', '0192023a7bbd73250516f069df18b500', 'KIUT-774-169', NULL, 'Faculty of Information and Technical Education', 'Bachelor in Computer Science', NULL, 2, '2025-03-21 20:14:08', '2025-07-16 23:29:27'),
(4, 'Robby', 'J', 'Lyando', 'KIUT/MD/2023/546', '35f103dbc00d02af228d75a74b0c7650', 'KIUT-199-994', NULL, 'Faculty in Medical Doctors', 'Medical Doctors', NULL, 2, '2025-07-03 12:10:51', '2025-07-03 12:10:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comment_list`
--
ALTER TABLE `comment_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `faculty`
--
ALTER TABLE `faculty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_list`
--
ALTER TABLE `post_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `system_info`
--
ALTER TABLE `system_info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category_list`
--
ALTER TABLE `category_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `comment_list`
--
ALTER TABLE `comment_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `faculty`
--
ALTER TABLE `faculty`
  MODIFY `id` int(200) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `post_list`
--
ALTER TABLE `post_list`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_info`
--
ALTER TABLE `system_info`
  MODIFY `id` int(30) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comment_list`
--
ALTER TABLE `comment_list`
  ADD CONSTRAINT `post_id_fk_cl` FOREIGN KEY (`post_id`) REFERENCES `post_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_id_fk_cl` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `post_list`
--
ALTER TABLE `post_list`
  ADD CONSTRAINT `category_id_fk_tl` FOREIGN KEY (`category_id`) REFERENCES `category_list` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `user_id_fk_tl` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
