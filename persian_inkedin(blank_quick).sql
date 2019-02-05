-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 31, 2018 at 01:46 PM
-- Server version: 10.1.36-MariaDB
-- PHP Version: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `persian_inkedin`
--

-- --------------------------------------------------------

--
-- Table structure for table `plnk_avatar`
--

CREATE TABLE `plnk_avatar` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `filename` text COLLATE utf8_persian_ci NOT NULL,
  `time` bigint(20) NOT NULL,
  `status` int(11) NOT NULL,
  `user_agent` text COLLATE utf8_persian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_block`
--

CREATE TABLE `plnk_block` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_blocked_id` bigint(20) NOT NULL,
  `time` bigint(20) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_connections`
--

CREATE TABLE `plnk_connections` (
  `id` bigint(20) NOT NULL,
  `connect_id` bigint(20) NOT NULL,
  `connected_id` bigint(20) NOT NULL,
  `time` bigint(20) NOT NULL,
  `status` int(11) NOT NULL,
  `requester` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_contact`
--

CREATE TABLE `plnk_contact` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `type` varchar(20) COLLATE utf8_persian_ci NOT NULL,
  `content` text COLLATE utf8_persian_ci NOT NULL,
  `time` bigint(20) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_country`
--

CREATE TABLE `plnk_country` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `plnk_country`
--

INSERT INTO `plnk_country` (`id`, `name`, `status`) VALUES
(1, 'ایران', 1),
(2, 'افغانستان', 1);

-- --------------------------------------------------------

--
-- Table structure for table `plnk_file`
--

CREATE TABLE `plnk_file` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `filename` text COLLATE utf8_persian_ci NOT NULL,
  `time` bigint(20) NOT NULL,
  `status` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `user_agent` text COLLATE utf8_persian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_like`
--

CREATE TABLE `plnk_like` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `post_id` bigint(20) NOT NULL,
  `time` bigint(20) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_login`
--

CREATE TABLE `plnk_login` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `time` bigint(20) NOT NULL,
  `user_agent` text COLLATE utf8_persian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_message`
--

CREATE TABLE `plnk_message` (
  `id` bigint(20) NOT NULL,
  `user_sender_id` bigint(20) DEFAULT NULL,
  `user_reciver_id` bigint(20) DEFAULT NULL,
  `content` varchar(255) COLLATE utf8_persian_ci NOT NULL,
  `time` bigint(20) NOT NULL,
  `status` int(11) NOT NULL,
  `unread` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_old_password`
--

CREATE TABLE `plnk_old_password` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `password` varchar(40) COLLATE utf8_persian_ci NOT NULL,
  `time` bigint(20) NOT NULL,
  `user_agent` text COLLATE utf8_persian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_person`
--

CREATE TABLE `plnk_person` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `firstname` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `lastname` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `country_id` bigint(11) DEFAULT NULL,
  `zip_code` varchar(20) COLLATE utf8_persian_ci DEFAULT NULL,
  `birthday` varchar(10) COLLATE utf8_persian_ci DEFAULT NULL,
  `biography` varchar(255) COLLATE utf8_persian_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_post`
--

CREATE TABLE `plnk_post` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `file_id` bigint(20) DEFAULT NULL,
  `content` text COLLATE utf8_persian_ci NOT NULL,
  `create_time` bigint(20) NOT NULL,
  `updated_time` bigint(20) NOT NULL,
  `status` int(11) NOT NULL,
  `user_agent` text COLLATE utf8_persian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_post_view`
--

CREATE TABLE `plnk_post_view` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `post_viewed_id` bigint(20) NOT NULL,
  `time` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_profile_view`
--

CREATE TABLE `plnk_profile_view` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) DEFAULT NULL,
  `user_viewed_id` bigint(20) NOT NULL,
  `time` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_report`
--

CREATE TABLE `plnk_report` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `user_reported_id` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  `time` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_user`
--

CREATE TABLE `plnk_user` (
  `id` bigint(20) NOT NULL,
  `email` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `password` varchar(40) COLLATE utf8_persian_ci NOT NULL,
  `register_time` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Dumping data for table `plnk_user`
--

INSERT INTO `plnk_user` (`id`, `email`, `password`, `register_time`, `type`, `status`) VALUES
(1, 'amirsh.nll@gmail.com', 'd9c05e0bf68a56953b6bd3a2abc50ff40f8403fa', 1541088808, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `plnk_user_item`
--

CREATE TABLE `plnk_user_item` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `type` int(11) NOT NULL,
  `title` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `content` text COLLATE utf8_persian_ci NOT NULL,
  `start_date` varchar(10) COLLATE utf8_persian_ci NOT NULL,
  `end_date` varchar(10) COLLATE utf8_persian_ci NOT NULL,
  `time` bigint(20) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plnk_user_option`
--

CREATE TABLE `plnk_user_option` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `name` varchar(100) COLLATE utf8_persian_ci NOT NULL,
  `value` text COLLATE utf8_persian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_persian_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `plnk_avatar`
--
ALTER TABLE `plnk_avatar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `plnk_block`
--
ALTER TABLE `plnk_block`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `user_blocked_id` (`user_blocked_id`);

--
-- Indexes for table `plnk_connections`
--
ALTER TABLE `plnk_connections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`connect_id`),
  ADD KEY `connect_id` (`connected_id`);

--
-- Indexes for table `plnk_contact`
--
ALTER TABLE `plnk_contact`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `plnk_country`
--
ALTER TABLE `plnk_country`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `plnk_file`
--
ALTER TABLE `plnk_file`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `plnk_like`
--
ALTER TABLE `plnk_like`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Indexes for table `plnk_login`
--
ALTER TABLE `plnk_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `plnk_message`
--
ALTER TABLE `plnk_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_sender_id` (`user_sender_id`),
  ADD KEY `user_reciver_id` (`user_reciver_id`);

--
-- Indexes for table `plnk_old_password`
--
ALTER TABLE `plnk_old_password`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `plnk_person`
--
ALTER TABLE `plnk_person`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`),
  ADD KEY `user_id_2` (`user_id`),
  ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `plnk_post`
--
ALTER TABLE `plnk_post`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `file_id` (`file_id`);

--
-- Indexes for table `plnk_post_view`
--
ALTER TABLE `plnk_post_view`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_viewed_id` (`post_viewed_id`);

--
-- Indexes for table `plnk_profile_view`
--
ALTER TABLE `plnk_profile_view`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_view_id` (`user_id`),
  ADD KEY `user_viewed_id` (`user_viewed_id`);

--
-- Indexes for table `plnk_report`
--
ALTER TABLE `plnk_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_report_id` (`user_id`),
  ADD KEY `user_reporter_id` (`user_reported_id`);

--
-- Indexes for table `plnk_user`
--
ALTER TABLE `plnk_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `plnk_user_item`
--
ALTER TABLE `plnk_user_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `plnk_user_option`
--
ALTER TABLE `plnk_user_option`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `plnk_avatar`
--
ALTER TABLE `plnk_avatar`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_block`
--
ALTER TABLE `plnk_block`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_connections`
--
ALTER TABLE `plnk_connections`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_contact`
--
ALTER TABLE `plnk_contact`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_country`
--
ALTER TABLE `plnk_country`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `plnk_file`
--
ALTER TABLE `plnk_file`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_like`
--
ALTER TABLE `plnk_like`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_login`
--
ALTER TABLE `plnk_login`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_message`
--
ALTER TABLE `plnk_message`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_old_password`
--
ALTER TABLE `plnk_old_password`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_person`
--
ALTER TABLE `plnk_person`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_post`
--
ALTER TABLE `plnk_post`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_post_view`
--
ALTER TABLE `plnk_post_view`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_profile_view`
--
ALTER TABLE `plnk_profile_view`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_report`
--
ALTER TABLE `plnk_report`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_user`
--
ALTER TABLE `plnk_user`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `plnk_user_item`
--
ALTER TABLE `plnk_user_item`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `plnk_user_option`
--
ALTER TABLE `plnk_user_option`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `plnk_avatar`
--
ALTER TABLE `plnk_avatar`
  ADD CONSTRAINT `plnk_avatar_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_block`
--
ALTER TABLE `plnk_block`
  ADD CONSTRAINT `plnk_block_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `plnk_block_ibfk_2` FOREIGN KEY (`user_blocked_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_connections`
--
ALTER TABLE `plnk_connections`
  ADD CONSTRAINT `plnk_connections_ibfk_1` FOREIGN KEY (`connect_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `plnk_connections_ibfk_2` FOREIGN KEY (`connected_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_contact`
--
ALTER TABLE `plnk_contact`
  ADD CONSTRAINT `plnk_contact_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_file`
--
ALTER TABLE `plnk_file`
  ADD CONSTRAINT `plnk_file_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_like`
--
ALTER TABLE `plnk_like`
  ADD CONSTRAINT `plnk_like_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `plnk_like_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `plnk_post` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_login`
--
ALTER TABLE `plnk_login`
  ADD CONSTRAINT `plnk_login_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_message`
--
ALTER TABLE `plnk_message`
  ADD CONSTRAINT `plnk_message_ibfk_1` FOREIGN KEY (`user_reciver_id`) REFERENCES `plnk_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `plnk_message_ibfk_2` FOREIGN KEY (`user_sender_id`) REFERENCES `plnk_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_old_password`
--
ALTER TABLE `plnk_old_password`
  ADD CONSTRAINT `plnk_old_password_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_person`
--
ALTER TABLE `plnk_person`
  ADD CONSTRAINT `plnk_person_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `plnk_person_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `plnk_country` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_post`
--
ALTER TABLE `plnk_post`
  ADD CONSTRAINT `plnk_post_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `plnk_post_ibfk_2` FOREIGN KEY (`file_id`) REFERENCES `plnk_file` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_post_view`
--
ALTER TABLE `plnk_post_view`
  ADD CONSTRAINT `plnk_post_view_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `plnk_post_view_ibfk_2` FOREIGN KEY (`post_viewed_id`) REFERENCES `plnk_post` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_profile_view`
--
ALTER TABLE `plnk_profile_view`
  ADD CONSTRAINT `plnk_profile_view_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE SET NULL ON UPDATE NO ACTION,
  ADD CONSTRAINT `plnk_profile_view_ibfk_2` FOREIGN KEY (`user_viewed_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_report`
--
ALTER TABLE `plnk_report`
  ADD CONSTRAINT `plnk_report_ibfk_1` FOREIGN KEY (`user_reported_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `plnk_report_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_user_item`
--
ALTER TABLE `plnk_user_item`
  ADD CONSTRAINT `plnk_user_item_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `plnk_user_option`
--
ALTER TABLE `plnk_user_option`
  ADD CONSTRAINT `plnk_user_option_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `plnk_user` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
