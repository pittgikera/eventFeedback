-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Aug 31, 2016 at 07:15 PM
-- Server version: 10.1.13-MariaDB
-- PHP Version: 5.5.37

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `feedback`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `accountNo` varchar(30) NOT NULL,
  `nationalID` varchar(20) DEFAULT NULL,
  `phonenumber` varchar(20) DEFAULT NULL,
  `balance` varchar(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `questions` longtext NOT NULL,
  `phonenumber` varchar(50) NOT NULL,
  `status` enum('ACTIVE','INACTIVE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `name`, `questions`, `phonenumber`, `status`) VALUES
(3, 'PizzaFest', '', '+254715224284', 'ACTIVE'),
(4, 'Lunch', '', '+254714196612', 'ACTIVE');

-- --------------------------------------------------------

--
-- Table structure for table `event_feedback`
--

CREATE TABLE `event_feedback` (
  `feedback_id` int(11) NOT NULL,
  `event_name` varchar(50) NOT NULL,
  `phonenumber` varchar(25) NOT NULL,
  `response` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `session_levels`
--

CREATE TABLE `session_levels` (
  `session_id` varchar(100) NOT NULL,
  `phoneNumber` varchar(25) NOT NULL,
  `level` tinyint(1) NOT NULL,
  `temp_pin` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `session_levels`
--

INSERT INTO `session_levels` (`session_id`, `phoneNumber`, `level`, `temp_pin`) VALUES
('ATUid_64466436031cc77b7b3b0544540c2404', '+254715224284', 3, ''),
('ATUid_2cb48f277a487bc76a2bfa0a714069f1', '+254714196612', 3, ''),
('ATUid_3c511e84d1d0db656a45ce837e63a7dd', '+254715224284', 2, '');

-- --------------------------------------------------------

--
-- Table structure for table `smsresponse`
--

CREATE TABLE `smsresponse` (
  `mid` int(11) NOT NULL,
  `from` varchar(100) NOT NULL,
  `to` int(100) NOT NULL,
  `text` varchar(255) NOT NULL,
  `linkId` varchar(100) NOT NULL,
  `date` varchar(100) NOT NULL,
  `id` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `smsresponse`
--

INSERT INTO `smsresponse` (`mid`, `from`, `to`, `text`, `linkId`, `date`, `id`) VALUES
(1, '2147483647', 20414, 'testing mercy', '2016-08-16T13:29:47Z', '0f10ba7e-4a75-4285-9a8a-32322721b080', 2147483647),
(2, '+254715224284', 20414, 'mary', '2016-08-16T13:34:09Z', '7157d631-5cfe-45ee-a9b5-c1db5be0c861', 2147483647),
(3, '+254715224284', 20414, 'Q1 wat is ur favourite show? *Q2 wat did u dislike?', '2016-08-16T13:55:17Z', '0211ef4f-b4e2-44b3-bf30-c3bc4c1e074a', 2147483647),
(4, '+254715224284', 20414, 'How would you rate the event #How would you rate the presentations#How would you rate the food#how would you rate the event #', '2016-08-30T12:29:13Z', '84dd4f4a-bbc0-40f5-8bf2-7be7ddad72a0', 2147483647);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transactionID` int(11) NOT NULL,
  `accountNo` varchar(30) NOT NULL,
  `type` enum('W','D') NOT NULL,
  `amount` varchar(20) NOT NULL,
  `phonenumber` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `phonenumber` varchar(25) NOT NULL,
  `name` varchar(30) NOT NULL,
  `dob` varchar(15) NOT NULL,
  `nationalID` varchar(20) NOT NULL,
  `pin` varchar(10) NOT NULL,
  `status` enum('SUSPENDED','ACTIVE') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`accountNo`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `event_feedback`
--
ALTER TABLE `event_feedback`
  ADD PRIMARY KEY (`feedback_id`);

--
-- Indexes for table `smsresponse`
--
ALTER TABLE `smsresponse`
  ADD PRIMARY KEY (`mid`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transactionID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `event_feedback`
--
ALTER TABLE `event_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `smsresponse`
--
ALTER TABLE `smsresponse`
  MODIFY `mid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transactionID` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
