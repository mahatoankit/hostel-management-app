-- Active: 1736826824669@@127.0.0.1@3306
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 14, 2025 at 01:05 AM
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
-- Database: `ankitMahato24128422`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `adminID` bigint(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`adminID`, `email`, `password`) VALUES
(12345, 'admin@gmail.com', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `billID` bigint(20) NOT NULL,
  `userID` bigint(20) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `billingDate` date NOT NULL,
  `paymentStatus` enum('Paid','Unpaid','Pending') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `complaintID` bigint(20) NOT NULL,
  `userID` bigint(20) DEFAULT NULL,
  `complaintType` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `complaintStatus` enum('Open','In Progress','Resolved') NOT NULL,
  `postingDate` date NOT NULL,
  `visibility` enum('Private','Public') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `complaintVotes`
--

CREATE TABLE `complaintVotes` (
  `voteID` bigint(20) NOT NULL,
  `complaintID` bigint(20) DEFAULT NULL,
  `userID` bigint(20) DEFAULT NULL,
  `voteType` enum('Upvote','Downvote') NOT NULL,
  `voteDate` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `guardian`
--

CREATE TABLE `guardian` (
  `guardianID` bigint(20) NOT NULL,
  `userID` bigint(20) DEFAULT NULL,
  `guardianFirstName` varchar(100) NOT NULL,
  `guardianLastName` varchar(100) NOT NULL,
  `phoneNumber` varchar(15) NOT NULL,
  `relationship` enum('Father','Mother','Sibling','Guardian','Other') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hostellers`
--

CREATE TABLE `hostellers` (
  `userID` bigint(20) NOT NULL,
  `hostellerID` varchar(20) NOT NULL,
  `hostellersEmail` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstName` varchar(20) NOT NULL,
  `lastName` varchar(20) NOT NULL,
  `phoneNumber` varchar(15) DEFAULT NULL,
  `occupation` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `joinedDate` date DEFAULT NULL,
  `departureDate` date DEFAULT NULL,
  `dietaryPreference` enum('Vegetarian','Non-Vegetarian','Vegan','Others') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roomAllocation`
--

CREATE TABLE `roomAllocation` (
  `allocationID` bigint(20) NOT NULL,
  `userID` bigint(20) DEFAULT NULL,
  `roomNumber` bigint(20) DEFAULT NULL,
  `allocationDate` date DEFAULT NULL,
  `deallocationDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `roomNumber` bigint(20) NOT NULL,
  `userID` bigint(20) DEFAULT NULL,
  `seaterNumber` tinyint(4) NOT NULL,
  `availability` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`adminID`);

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`billID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`complaintID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `complaintVotes`
--
ALTER TABLE `complaintVotes`
  ADD PRIMARY KEY (`voteID`),
  ADD KEY `complaintID` (`complaintID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `guardian`
--
ALTER TABLE `guardian`
  ADD PRIMARY KEY (`guardianID`),
  ADD KEY `userID` (`userID`);

--
-- Indexes for table `hostellers`
--
ALTER TABLE `hostellers`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `hostellerID` (`hostellerID`);

--
-- Indexes for table `roomAllocation`
--
ALTER TABLE `roomAllocation`
  ADD PRIMARY KEY (`allocationID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `roomNumber` (`roomNumber`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`roomNumber`),
  ADD KEY `userID` (`userID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `hostellers` (`userID`);

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `hostellers` (`userID`);

--
-- Constraints for table `complaintVotes`
--
ALTER TABLE `complaintVotes`
  ADD CONSTRAINT `complaintVotes_ibfk_1` FOREIGN KEY (`complaintID`) REFERENCES `complaints` (`complaintID`),
  ADD CONSTRAINT `complaintVotes_ibfk_2` FOREIGN KEY (`userID`) REFERENCES `hostellers` (`userID`);

--
-- Constraints for table `guardian`
--
ALTER TABLE `guardian`
  ADD CONSTRAINT `guardian_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `hostellers` (`userID`);

--
-- Constraints for table `roomAllocation`
--
ALTER TABLE `roomAllocation`
  ADD CONSTRAINT `roomAllocation_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `hostellers` (`userID`),
  ADD CONSTRAINT `roomAllocation_ibfk_2` FOREIGN KEY (`roomNumber`) REFERENCES `rooms` (`roomNumber`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `hostellers` (`userID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
