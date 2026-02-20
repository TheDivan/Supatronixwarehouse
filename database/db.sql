-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 10, 2018 at 10:05 AM
-- Server version: 10.1.26-MariaDB
-- PHP Version: 7.1.9

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bookingsoftware`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`id`, `title`) VALUES
(1, 'Apple'),
(2, 'Huawei'),
(3, 'Xiamoi'),
(4, 'Samsung'),
(5, 'Honor'),
(6, 'Nokia'),
(7, 'Google'),
(8, "SmartWatch')
-- --------------------------------------------------------

--
-- Table structure for table `brand_models`
--

CREATE TABLE `brand_models` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `brand_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `title` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `brand_models`

INSERT INTO `brand_models` (`id`, `brand_id`, `title`) VALUES
(1, 4, 'Galaxy S6'),
(2, 4, 'Galaxy S6 Edge'),
(3, 4, 'Galaxy S6 Edge+'),
(4, 4, 'Galaxy S7'),
(5, 4, 'Galaxy S7 Edge'),
(6, 4, 'Galaxy S8'),
(7, 4, 'Galaxy S8+'),
(8, 4, 'Galaxy S9'),
(9, 4, 'Galaxy S9+'),
(10, 4, 'Galaxy S10e'),
(11, 4, 'Galaxy S10'),
(12, 4, 'Galaxy S10+'),
(13, 4, 'Galaxy S10 5G'),
(14, 4, 'Galaxy S20'),
(15, 4, 'Galaxy S20+'),
(16, 4, 'Galaxy S20 Ultra'),
(17, 4, 'Galaxy S20 FE'),
(18, 4, 'Galaxy S21'),
(19, 4, 'Galaxy S21+'),
(20, 4, 'Galaxy S21 Ultra'),
(21, 4, 'Galaxy S21 FE'),
(22, 4, 'Galaxy S22'),
(23, 4, 'Galaxy S22+'),
(24, 4, 'Galaxy S22 Ultra'),
(25, 4, 'Galaxy S23'),
(26, 4, 'Galaxy S23+'),
(27, 4, 'Galaxy S23 Ultra'),
(28, 4, 'Galaxy S23 FE'),
(29, 4, 'Galaxy S24'),
(30, 4, 'Galaxy S24+'),
(31, 4, 'Galaxy S24 Ultra'),
(32, 4, 'Galaxy S24 FE'),
(33, 4, 'Galaxy S25'),
(34, 4, 'Galaxy S25+'),
(35, 4, 'Galaxy S25 Ultra'),
(36, 4, 'Galaxy S25 Edge'),
(37, 4, 'Galaxy Note 5'),
(38, 4, 'Galaxy Note 7'),
(39, 4, 'Galaxy Note 8'),
(40, 4, 'Galaxy Note 9'),
(41, 4, 'Galaxy Note 10'),
(42, 4, 'Galaxy Note 10+'),
(43, 4, 'Galaxy Note 20'),
(44, 4, 'Galaxy Note 20 Ultra'),
(45, 4, 'Galaxy Fold (1st Gen)'),
(46, 4, 'Galaxy Z Fold 2'),
(47, 4, 'Galaxy Z Fold 3'),
(48, 4, 'Galaxy Z Fold 4'),
(49, 4, 'Galaxy Z Fold 5'),
(50, 4, 'Galaxy Z Fold 6'),
(51, 4, 'Galaxy Z Fold 7'),
(52, 4, 'Galaxy Z Flip'),
(53, 4, 'Galaxy Z Flip 5G'),
(54, 4, 'Galaxy Z Flip 3'),
(55, 4, 'Galaxy Z Flip 4'),
(56, 4, 'Galaxy Z Flip 5'),
(57, 4, 'Galaxy Z Flip 6'),
(58, 4, 'Galaxy Z Flip 7'),
(59, 4, 'Galaxy A3'),
(60, 4, 'Galaxy A5'),
(61, 4, 'Galaxy A7'),
(62, 4, 'Galaxy A6'),
(63, 4, 'Galaxy A6+'),
(64, 4, 'Galaxy A7 (2018)'),
(65, 4, 'Galaxy A8'),
(66, 4, 'Galaxy A8+'),
(67, 4, 'Galaxy A10'),
(68, 4, 'Galaxy A20'),
(69, 4, 'Galaxy A30'),
(70, 4, 'Galaxy A40'),
(71, 4, 'Galaxy A50'),
(72, 4, 'Galaxy A70'),
(73, 4, 'Galaxy A80'),
(74, 4, 'Galaxy A01'),
(75, 4, 'Galaxy A11'),
(76, 4, 'Galaxy A21'),
(77, 4, 'Galaxy A21s'),
(78, 4, 'Galaxy A31'),
(79, 4, 'Galaxy A41'),
(80, 4, 'Galaxy A51'),
(81, 4, 'Galaxy A51 5G'),
(82, 4, 'Galaxy A71'),
(83, 4, 'Galaxy A71 5G'),
(84, 4, 'Galaxy A02'),
(85, 4, 'Galaxy A02s'),
(86, 4, 'Galaxy A12'),
(87, 4, 'Galaxy A22'),
(88, 4, 'Galaxy A22 5G'),
(89, 4, 'Galaxy A32'),
(90, 4, 'Galaxy A32 5G'),
(91, 4, 'Galaxy A52'),
(92, 4, 'Galaxy A52 5G'),
(93, 4, 'Galaxy A52s 5G'),
(94, 4, 'Galaxy A72'),
(95, 4, 'Galaxy A03'),
(96, 4, 'Galaxy A03 Core'),
(97, 4, 'Galaxy A03s'),
(98, 4, 'Galaxy A13'),
(99, 4, 'Galaxy A13 5G'),
(100, 4, 'Galaxy A23'),
(101, 4, 'Galaxy A33 5G'),
(102, 4, 'Galaxy A53 5G'),
(103, 4, 'Galaxy A73 5G'),
(104, 4, 'Galaxy A04'),
(105, 4, 'Galaxy A04e'),
(106, 4, 'Galaxy A04s'),
(107, 4, 'Galaxy A14'),
(108, 4, 'Galaxy A14 5G'),
(109, 4, 'Galaxy A24'),
(110, 4, 'Galaxy A34 5G'),
(111, 4, 'Galaxy A54 5G'),
(112, 4, 'Galaxy A05'),
(113, 4, 'Galaxy A05s'),
(114, 4, 'Galaxy A15'),
(115, 4, 'Galaxy A15 5G'),
(116, 4, 'Galaxy A25 5G'),
(117, 4, 'Galaxy A35 5G'),
(118, 4, 'Galaxy A55 5G'),
(119, 4, 'Galaxy A06'),
(120, 4, 'Galaxy A16'),
(121, 4, 'Galaxy A16 5G'),
(122, 4, 'Galaxy A26 5G'),
(123, 4, 'Galaxy A36 5G'),
(124, 4, 'Galaxy A56 5G'),
(125, 4, 'Galaxy J1'),
(126, 4, 'Galaxy J1 Ace'),
(127, 4, 'Galaxy J2'),
(128, 4, 'Galaxy J5'),
(129, 4, 'Galaxy J7'),
(130, 4, 'Galaxy J1 (2016)'),
(131, 4, 'Galaxy J2 (2016)'),
(132, 4, 'Galaxy J3 (2016)'),
(133, 4, 'Galaxy J5 (2016)'),
(134, 4, 'Galaxy J7 (2016)'),
(135, 4, 'Galaxy J2 Pro'),
(136, 4, 'Galaxy J3 (2017)'),
(137, 4, 'Galaxy J5 (2017)'),
(138, 4, 'Galaxy J7 (2017)'),
(139, 4, 'Galaxy J7 Pro'),
(140, 4, 'Galaxy J4'),
(141, 4, 'Galaxy J4+'),
(142, 4, 'Galaxy J6'),
(143, 4, 'Galaxy J6+'),
(144, 4, 'Galaxy J8'),
(145, 4, 'Galaxy M12'),
(146, 4, 'Galaxy M32'),
(147, 4, 'Galaxy M13'),
(148, 4, 'Galaxy M23 5G'),
(149, 4, 'Galaxy M33 5G'),
(150, 4, 'Galaxy M14'),
(151, 4, 'Galaxy M14 5G'),
(152, 4, 'Galaxy M34 5G'),
(153, 4, 'Galaxy M15 5G'),
(154, 4, 'Galaxy M35 5G'),
(155, 4, 'Galaxy M55 5G');

--
-- Dumping data for table `device_models`
--

INSERT INTO `device_models` (`id`, `brand_id`, `title`) VALUES
(1, 1, 'iPhone 4c'),
(2, 1, 'iPhone 4s'),
(3, 1, 'iPhone 5s'),
(4, 1, 'iPhone 5c'),
(5, 1, 'iPhone 6'),
(6, 1, 'iPhone 7'),
(7, 1, 'iPhone 8'),
(8, 1, 'iPhone X'),
(9, 1, 'iPhone 11'),
(10, 1, 'iPhone 12'),
(11, 1, 'iPhone 13'),
(12, 1, 'iPhone 14'),
(13, 1, 'iPhone 15'),
(14, 1, 'iPhone 16'),
(15, 2, 'Passport'),
(16, 2, 'Samsung'),
(17, 2, 'Nokia 9720');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(10) UNSIGNED NOT NULL,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `office_id`, `name`, `phone`, `email`, `updated_datetime`, `created_datetime`) VALUES
(1, 1, 'JohnTest', '0321231321231', 'supatronix@iway.na', '2016-04-09 20:13:42', '2016-03-05 23:38:02');

-- --------------------------------------------------------

--
-- Table structure for table `email_templates`
--

CREATE TABLE `email_templates` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(225) NOT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `from_name` varchar(255) DEFAULT NULL,
  `to_email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text,
  `updated_datetime` datetime DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `email_templates`
--

INSERT INTO `email_templates` (`id`, `title`, `from_email`, `from_name`, `to_email`, `subject`, `message`, `updated_datetime`, `created_datetime`, `status`) VALUES
(1, 'Order received', 'supatronix@iway.na', 'Supatronix Warehouse/Repairs', '[EMAIL]', 'Thank you for visiting store.', 'Dear [NAME],\r\nThank you for ordering.\r\n\r\nOrder ID: [ORDER_ID]\r\nDelvery Date: [DELIVERY_DATE]\r\n\r\nRegards,\r\n', '2015-12-11 06:58:05', '2016-01-01 00:00:00', 1),
(2, 'Order ready', 'supatronix@iway.na', 'Supatronix Warehouse/Repairs', '[EMAIL]', 'Your order is ready to pick.', 'Dear [NAME],\r\nYour order number: [ORDER_ID] is ready to pick.\r\n\r\nReceived Date: [RECEIVE_DATE]\r\nRegards,\r\n', '2015-12-11 07:03:29', '2016-01-01 00:00:00', 1),
(3, 'Forgot Password', 'supatronix@iway.na', 'Supatronix Warehouse/Repairs', '[EMAIL]', 'Password Recovery.', 'Dear [NAME],\r\nYour password has sent on your request.\r\nUsername: [USERNAME]\r\nPassword: [PASSWORD]\r\n\r\nRegards,\r\nSupatronix Warehouse/Repairs', '2016-04-10 12:39:51', '2016-04-10 12:39:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `email_template_tags`
--

CREATE TABLE `email_template_tags` (
  `tag` varchar(225) NOT NULL,
  `field_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `email_template_tags`
--

INSERT INTO `email_template_tags` (`tag`, `field_name`) VALUES
('[DELIVERY_DATE]', 'delivery_date'),
('[EMAIL]', 'email'),
('[NAME]', 'name'),
('[OFFICE]', 'office'),
('[ORDER_ID]', 'id'),
('[PASSWORD]', 'password'),
('[RECEIVE_DATE]', 'receive_date'),
('[USERNAME]', 'username');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

CREATE TABLE `employees` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `email` varchar(64) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT NULL,
  `status` tinyint(1) DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `office_id`, `name`, `username`, `email`, `password`, `phone`, `is_admin`, `status`, `updated_datetime`, `created_datetime`) VALUES
(1, 1, 'Divan', 'admin', 'divanbesser@gmail.com', '200946307', '2312312321', 1, 1, '2016-09-09 15:55:45', '2015-11-05 19:18:07');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(10) UNSIGNED NOT NULL,
  `customer_id` int(10) UNSIGNED NOT NULL,
  `employee_id` smallint(5) UNSIGNED NOT NULL,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `technician` varchar(255) DEFAULT NULL,
  `receive_date` date DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `status` tinyint(1) UNSIGNED DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `job_items`
--

CREATE TABLE `job_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED NOT NULL,
  `brand_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `brand_model_id` smallint(5) UNSIGNED DEFAULT NULL,
  `device_number` varchar(255) DEFAULT NULL COMMENT 'IMEI/ ESN/ SN',
  `color` varchar(255) DEFAULT NULL,
  `provider` varchar(255) DEFAULT NULL,
  `device_password` varchar(255) DEFAULT NULL,
  `power_on` tinyint(1) UNSIGNED DEFAULT NULL,
  `charging` tinyint(1) UNSIGNED DEFAULT NULL,
  `network` tinyint(1) UNSIGNED DEFAULT NULL,
  `display` tinyint(1) UNSIGNED DEFAULT NULL,
  `camera` tinyint(1) UNSIGNED DEFAULT NULL,
  `battery` tinyint(1) UNSIGNED DEFAULT NULL,
  `fault_discription` varbinary(255) DEFAULT NULL,
  `cost` float DEFAULT NULL,
  `Total amount` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` tinyint(3) UNSIGNED NOT NULL,
  `title` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`id`, `title`) VALUES
(1, 'Walvis Bay, Namibia'),
(2, 'Swakopmund, Namibia');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand_models`
--
ALTER TABLE `brand_models`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `PHONE_UNIQUE` (`phone`),
  ADD UNIQUE KEY `EMAIL_UNIQUE` (`email`);

--
-- Indexes for table `email_templates`
--
ALTER TABLE `email_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `email_template_tags`
--
ALTER TABLE `email_template_tags`
  ADD PRIMARY KEY (`tag`);

--
-- Indexes for table `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `USERNAME_UNIQUE` (`username`),
  ADD UNIQUE KEY `EMAIL_UNIQUE` (`email`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `CUSTOMER` (`customer_id`);

--
-- Indexes for table `job_items`
--
ALTER TABLE `job_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `JOB_ITEMS` (`job_id`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `device_models`
--
ALTER TABLE `device_models`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

-- --------------------------------------------------------
--
-- Table structure for table `stock`
--

CREATE TABLE `stock` (
  `id` int(10) UNSIGNED NOT NULL,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `part_category` varchar(64) DEFAULT NULL,
  `part_name` varchar(128) DEFAULT NULL,
  `quantity` int(10) DEFAULT '0',
  `cost` decimal(10,2) DEFAULT NULL,
  `supplier` varchar(128) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
  -- add supplier_id column to stock for linking to suppliers table
  ALTER TABLE `stock` ADD COLUMN `supplier_id` int(10) DEFAULT NULL;

-- Dumping data for table `stock` (empty)

INSERT INTO `stock` (`id`, `office_id`, `part_category`, `part_name`, `quantity`, `cost`, `supplier`, `notes`, `updated_datetime`, `created_datetime`) VALUES
;

-- AUTO_INCREMENT for table `stock`
ALTER TABLE `stock`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- --------------------------------------------------------
--
-- Table structure for table `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(10) UNSIGNED NOT NULL,
  `stock_id` int(10) UNSIGNED NOT NULL,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `change` int(10) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_by` smallint(5) UNSIGNED DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `stock_movements` (`id`, `stock_id`, `office_id`, `change`, `note`, `created_by`, `created_datetime`) VALUES
;

ALTER TABLE `stock_movements`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- --------------------------------------------------------
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `office_id` tinyint(3) UNSIGNED DEFAULT NULL,
  `name` varchar(128) DEFAULT NULL,
  `contact` varchar(128) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `updated_datetime` datetime DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `suppliers` (`id`, `office_id`, `name`, `contact`, `phone`, `email`, `notes`, `updated_datetime`, `created_datetime`) VALUES
;

ALTER TABLE `suppliers`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;

-- --------------------------------------------------------
-- Table structure for table `api_tokens`
--

CREATE TABLE `api_tokens` (
  `id` int(10) UNSIGNED NOT NULL,
  `employee_id` smallint(5) UNSIGNED NOT NULL,
  `token` varchar(128) NOT NULL,
  `name` varchar(128) DEFAULT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `expires` datetime DEFAULT NULL,
  `created_datetime` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Seed token for Divan (employee_id = 1). Replace token in production.
INSERT INTO `api_tokens` (`id`, `employee_id`, `token`, `name`, `is_admin`, `expires`, `created_datetime`) VALUES
(1, 1, 'DIVAN_API_TOKEN_abcdef1234567890', 'Divan API token', 1, NULL, '2026-02-19 00:00:00');

ALTER TABLE `api_tokens`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `email_templates`
--
ALTER TABLE `email_templates`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employees`
--
ALTER TABLE `employees`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `job_items`
--
ALTER TABLE `job_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` tinyint(3) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `CUSTOMER` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `job_items`
--
ALTER TABLE `job_items`
  ADD CONSTRAINT `JOB_ITEMS` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
