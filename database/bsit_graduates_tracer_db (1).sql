-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2025 at 08:54 AM
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
-- Database: `bsit_graduates_tracer_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(12) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employment_details`
--

CREATE TABLE `employment_details` (
  `employment_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `employment_type` enum('Current','Previous') DEFAULT 'Current',
  `details_of_employment` text DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `company_name` varchar(200) NOT NULL,
  `company_address` text NOT NULL,
  `type_of_company` varchar(100) DEFAULT NULL,
  `position` varchar(100) NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `employment_status` enum('Regular','Contractual','Probationary','Part-time','Full-time') DEFAULT NULL,
  `has_previous_experience` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `graduates`
--

CREATE TABLE `graduates` (
  `graduate_id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `permanent_address` text DEFAULT NULL,
  `barangay` varchar(100) DEFAULT NULL,
  `city_municipality` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `date_of_birth` date NOT NULL,
  `civil_status` varchar(20) NOT NULL,
  `employed_within_6_months` tinyint(1) NOT NULL,
  `employment_type` varchar(20) DEFAULT NULL,
  `registration_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ofw_details`
--

CREATE TABLE `ofw_details` (
  `ofw_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `company_name` varchar(200) NOT NULL,
  `company_address` text NOT NULL,
  `type_of_company` varchar(100) DEFAULT NULL,
  `position` varchar(100) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date DEFAULT NULL,
  `employment_status` enum('Regular','Contractual','Probationary','Part-time','Full-time') DEFAULT NULL,
  `has_previous_experience` tinyint(1) DEFAULT 0,
  `country` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `previous_experiences`
--

CREATE TABLE `previous_experiences` (
  `experience_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `employment_type` enum('Employed','Self-Employed','OFW') DEFAULT NULL,
  `company_name` varchar(200) DEFAULT NULL,
  `nature_of_business` varchar(200) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `job_description` text DEFAULT NULL,
  `company_address` text DEFAULT NULL,
  `type_of_company` varchar(100) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `employment_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `self_employment_details`
--

CREATE TABLE `self_employment_details` (
  `self_employment_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `nature_of_business` text NOT NULL,
  `place_of_business` text NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `has_previous_experience` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unemployed_details`
--

CREATE TABLE `unemployed_details` (
  `unemployed_id` int(11) NOT NULL,
  `graduate_id` int(11) NOT NULL,
  `has_previous_experience` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `validated_graduates`
--

CREATE TABLE `validated_graduates` (
  `validation_id` int(11) NOT NULL,
  `full_name` varchar(150) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `school_graduated` varchar(200) NOT NULL,
  `year_graduated` year(4) NOT NULL,
  `course_graduated` varchar(100) NOT NULL,
  `validation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `validated_graduates`
--

INSERT INTO `validated_graduates` (`validation_id`, `full_name`, `student_id`, `school_graduated`, `year_graduated`, `course_graduated`, `validation_date`) VALUES
(6, 'Horiuchi, ryuta v.', '21-12994', 'ISU - Echague', '2020', 'BSIT', '2025-12-18 06:38:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `employment_details`
--
ALTER TABLE `employment_details`
  ADD PRIMARY KEY (`employment_id`),
  ADD KEY `graduate_id` (`graduate_id`);

--
-- Indexes for table `graduates`
--
ALTER TABLE `graduates`
  ADD PRIMARY KEY (`graduate_id`);

--
-- Indexes for table `ofw_details`
--
ALTER TABLE `ofw_details`
  ADD PRIMARY KEY (`ofw_id`),
  ADD KEY `graduate_id` (`graduate_id`);

--
-- Indexes for table `previous_experiences`
--
ALTER TABLE `previous_experiences`
  ADD PRIMARY KEY (`experience_id`),
  ADD KEY `graduate_id` (`graduate_id`);

--
-- Indexes for table `self_employment_details`
--
ALTER TABLE `self_employment_details`
  ADD PRIMARY KEY (`self_employment_id`),
  ADD KEY `graduate_id` (`graduate_id`);

--
-- Indexes for table `unemployed_details`
--
ALTER TABLE `unemployed_details`
  ADD PRIMARY KEY (`unemployed_id`),
  ADD KEY `graduate_id` (`graduate_id`);

--
-- Indexes for table `validated_graduates`
--
ALTER TABLE `validated_graduates`
  ADD PRIMARY KEY (`validation_id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(12) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employment_details`
--
ALTER TABLE `employment_details`
  MODIFY `employment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `graduates`
--
ALTER TABLE `graduates`
  MODIFY `graduate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ofw_details`
--
ALTER TABLE `ofw_details`
  MODIFY `ofw_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `previous_experiences`
--
ALTER TABLE `previous_experiences`
  MODIFY `experience_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `self_employment_details`
--
ALTER TABLE `self_employment_details`
  MODIFY `self_employment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unemployed_details`
--
ALTER TABLE `unemployed_details`
  MODIFY `unemployed_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `validated_graduates`
--
ALTER TABLE `validated_graduates`
  MODIFY `validation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employment_details`
--
ALTER TABLE `employment_details`
  ADD CONSTRAINT `employment_details_ibfk_1` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE;

--
-- Constraints for table `ofw_details`
--
ALTER TABLE `ofw_details`
  ADD CONSTRAINT `ofw_details_ibfk_1` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE;

--
-- Constraints for table `previous_experiences`
--
ALTER TABLE `previous_experiences`
  ADD CONSTRAINT `previous_experiences_ibfk_1` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE;

--
-- Constraints for table `self_employment_details`
--
ALTER TABLE `self_employment_details`
  ADD CONSTRAINT `self_employment_details_ibfk_1` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE;

--
-- Constraints for table `unemployed_details`
--
ALTER TABLE `unemployed_details`
  ADD CONSTRAINT `unemployed_details_ibfk_1` FOREIGN KEY (`graduate_id`) REFERENCES `graduates` (`graduate_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
