-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 27, 2024 at 11:07 PM
-- Server version: 8.0.36-0ubuntu0.22.04.1
-- PHP Version: 8.1.2-1ubuntu2.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `test`
--


-- --------------------------------------------------------

--
-- Table structure for table `cape_users`
--

CREATE TABLE `cape_users` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `cid` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cloaks`
--

CREATE TABLE `cloaks` (
  `id` int NOT NULL,
  `uid` int NOT NULL,
  `cloak` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------


-- --------------------------------------------------------

--
-- Table structure for table `hwids`
--

CREATE TABLE `hwids` (
  `id` bigint NOT NULL,
  `publickey` blob,
  `hwDiskId` varchar(255) DEFAULT NULL,
  `baseboardSerialNumber` varchar(255) DEFAULT NULL,
  `graphicCard` varchar(255) DEFAULT NULL,
  `displayId` blob,
  `bitness` int DEFAULT NULL,
  `totalMemory` bigint DEFAULT NULL,
  `logicalProcessors` int DEFAULT NULL,
  `physicalProcessors` int DEFAULT NULL,
  `processorMaxFreq` bigint DEFAULT NULL,
  `battery` tinyint(1) NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Table structure for table `skins`
--

CREATE TABLE `skins` (
  `id` int NOT NULL,
  `uuid` varchar(256) NOT NULL,
  `slim` int NOT NULL,
  `locked` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` varchar(256) NOT NULL,
  `username` varchar(255) NOT NULL,
  `dsid` bigint DEFAULT NULL,
  `mail` varchar(255) DEFAULT NULL,
  `mail_verify` int DEFAULT '0',
  `uuid` char(36) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `perms` int NOT NULL DEFAULT '1',
  `accessToken` char(32) DEFAULT NULL,
  `serverID` varchar(41) DEFAULT NULL,
  `hwidId` bigint DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


CREATE TABLE job_schedule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    job_name VARCHAR(255) NOT NULL,               -- Descriptive name of the job
    action VARCHAR(255),                          -- Action to be taken (e.g., 'remove_permissions', 'update_role', etc.)
    target_id INT,                                -- Target ID (e.g., user_id or item_id)
    scheduled_date BIGINT,                          -- Date when the job is scheduled to run
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending', -- Job status
    created_at INT DEFAULT NULL,      -- Unix timestamp when the job was created
    updated_at INT DEFAULT NULL -- Unix timestamp when the job was last updated
);





--
-- Dumping data for table `users`
--

-- INSERT INTO `users` (`id`, `username`, `ip`, `dsid`, `mail`, `mail_verify`, `uuid`, `password`, `perms`, `accessToken`, `serverID`, `hwidId`) 
-- VALUES 
-- ('MTZ-M46', 'exampleUser', '192.168.1.152', 741957448532754493, NULL, 0, NULL, '$2y$10$XuU9jijr49GxAGprKlNNDuOULzNK1nUkYUSqPzU7ovkfDe6apeaf2', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `uuid` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verify_codes`
--

CREATE TABLE `verify_codes` (
  `id` int NOT NULL,
  `userid` int NOT NULL,
  `code` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `expire` bigint DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

CREATE TABLE `blacklisted_jwts` (
  `id` INT NOT NULL AUTO_INCREMENT,       -- Unique identifier for each entry
  `jwt` TEXT NOT NULL,                    -- The actual JWT token (or its hash for security)
  `expiration` BIGINT NOT NULL,           -- Unix timestamp of the tokens expiration
  PRIMARY KEY (`id`),                     -- Index for quick lookup by ID
  INDEX (`expiration`)                    -- Index for efficiently cleaning up expired tokens
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


--
-- Indexes for table `api_tokens`
--

--
-- Indexes for table `cape_users`
--
ALTER TABLE `cape_users`
  ADD PRIMARY KEY (`id`);



--

--
-- Indexes for table `hwids`
--
ALTER TABLE `hwids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `publickey` (`publickey`(255));

--
-- Indexes for table `skins`
--
ALTER TABLE `skins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_profiles`
--
ALTER TABLE `users_profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `users_hwidfk` (`hwidId`);

--
-- Indexes for table `users_tokens`
--
--
-- Indexes for table `user_permissions`
--
ALTER TABLE `user_permissions`
  ADD KEY `user_permissions_uuid_IDX` (`uuid`) USING BTREE;

--
-- Indexes for table `verify_codes`
--
ALTER TABLE `verify_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `your_table`
--


--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cape_users`
--
ALTER TABLE `cape_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;



--
-- AUTO_INCREMENT for table `hwids`
--
ALTER TABLE `hwids`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skins`
--
ALTER TABLE `skins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users_profiles`
--
ALTER TABLE `users_profiles`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verify_codes`
--
ALTER TABLE `verify_codes`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `users_profiles`
--
ALTER TABLE `users_profiles`
  ADD CONSTRAINT `users_hwidfk` FOREIGN KEY (`hwidId`) REFERENCES `hwids` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
